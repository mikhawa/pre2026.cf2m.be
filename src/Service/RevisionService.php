<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Formation;
use App\Entity\FormationHistory;
use App\Entity\Page;
use App\Entity\PageHistory;
use App\Entity\Revision;
use App\Entity\User;
use App\Entity\Works;
use App\Entity\WorksHistory;
use App\Repository\FormationHistoryRepository;
use App\Repository\PageHistoryRepository;
use App\Repository\UserRepository;
use App\Repository\WorksHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Service de gestion des révisions (snapshots) pour Formation, Page et Works.
 */
class RevisionService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
        private readonly UserRepository $userRepository,
        private readonly FormationHistoryRepository $formationHistoryRepo,
        private readonly PageHistoryRepository $pageHistoryRepo,
        private readonly WorksHistoryRepository $worksHistoryRepo,
        #[Autowire(env: 'MAIL_FORM')]
        private readonly string $mailFrom,
    ) {
    }

    /**
     * Crée une révision (snapshot) pour une entité donnée.
     * Capture l'état précédent (previousData) via DBAL avant tout flush,
     * sauf pour une création initiale ($isCreation = true) où previousData reste null.
     * Persiste la révision sans effectuer de flush.
     */
    public function createRevision(object $entity, User $author, bool $autoApprove, bool $isCreation = false): Revision
    {
        $revision = new Revision();
        $revision->setCreatedBy($author);

        // Pour une création, previousData = null (aucun état précédent)
        // Pour une modification, capture via DBAL avant flush
        if (!$isCreation) {
            $revision->setPreviousData($this->snapshotPreviousFromDb($entity));
        }

        if ($entity instanceof Formation) {
            $revision->setEntityType('formation');
            $revision->setEntityId($entity->getId());
            $revision->setEntityTitle($entity->getTitle() ?? '');
            $revision->setData($this->snapshotFormation($entity));
        } elseif ($entity instanceof Page) {
            $revision->setEntityType('page');
            $revision->setEntityId($entity->getId());
            $revision->setEntityTitle($entity->getTitle() ?? '');
            $revision->setData($this->snapshotPage($entity));
        } elseif ($entity instanceof Works) {
            $revision->setEntityType('works');
            $revision->setEntityId($entity->getId());
            $revision->setEntityTitle($entity->getTitle() ?? '');
            $revision->setData($this->snapshotWorks($entity));
        } else {
            throw new \InvalidArgumentException(sprintf('Type d\'entité non supporté : %s', $entity::class));
        }

        if ($autoApprove) {
            $revision->setStatus(Revision::STATUS_APPROVED);
            $revision->setReviewedBy($author);
            $revision->setReviewedAt(new \DateTimeImmutable());
        } else {
            $revision->setStatus(Revision::STATUS_PENDING);
        }

        // Phase 5 : Revision n'est plus persistée, elle sert de DTO transient pour les emails
        $revision->setCreatedAt(new \DateTimeImmutable());

        // Écriture dans la table historique typée
        $this->saveToTypedHistory($entity, $author, $autoApprove);

        return $revision;
    }

    /**
     * Applique les données d'un snapshot à une Formation en mémoire (sans persist ni flush).
     * Utilisé pour pré-remplir le formulaire d'édition EasyAdmin avec les données de la révision PENDING.
     *
     * @param array<string, mixed> $data
     */
    public function applyRevisionDataToFormation(Formation $entity, array $data): void
    {
        $entity->setTitle($data['title'] ?? $entity->getTitle());
        $entity->setSlug($data['slug'] ?? $entity->getSlug());
        $entity->setDescription($data['description'] ?? null);
        $entity->setStatus($data['status'] ?? $entity->getStatus());
        $entity->setPublishedAt(isset($data['publishedAt']) ? new \DateTimeImmutable($data['publishedAt']) : null);
        $entity->setColorPrimary($data['colorPrimary'] ?? null);
        $entity->setColorSecondary($data['colorSecondary'] ?? null);
    }

    /**
     * Met à jour les données d'une révision PENDING existante avec le snapshot actuel de l'entité.
     * Ne modifie pas previousData (l'état d'origine avant la première soumission est conservé).
     * Ne flush pas : le contrôleur gère le timing.
     */
    public function updatePendingRevision(Revision $revision, object $entity): void
    {
        if ($entity instanceof Formation) {
            $revision->setData($this->snapshotFormation($entity));
            $revision->setEntityTitle($entity->getTitle() ?? '');
        } elseif ($entity instanceof Page) {
            $revision->setData($this->snapshotPage($entity));
            $revision->setEntityTitle($entity->getTitle() ?? '');
        } elseif ($entity instanceof Works) {
            $revision->setData($this->snapshotWorks($entity));
            $revision->setEntityTitle($entity->getTitle() ?? '');
        } else {
            throw new \InvalidArgumentException(sprintf('Type d\'entité non supporté : %s', $entity::class));
        }

        // Double écriture : mise à jour de l'entrée pending dans la table typée (transition Phase 3)
        $this->updatePendingTypedHistory($entity);
    }

    /**
     * Applique le snapshot d'une révision PENDING à l'entité live correspondante.
     * Met à jour previousData avec l'état live courant si non déjà renseigné, puis flush.
     */
    public function applyRevision(Revision $revision, User $reviewer): void
    {
        // previousData déjà capturé à la création ; on ne l'écrase que s'il est absent
        if (null === $revision->getPreviousData()) {
            try {
                $revision->setPreviousData($this->getCurrentSnapshot($revision));
            } catch (\Throwable) {
                // Entité introuvable, on continue sans sauvegarde
            }
        }

        $data = $revision->getData();
        $type = $revision->getEntityType();
        $entityId = $revision->getEntityId();

        match ($type) {
            'formation' => $this->applyFormation($entityId, $data),
            'page'      => $this->applyPage($entityId, $data),
            'works'     => $this->applyWorks($entityId, $data),
            default     => throw new \InvalidArgumentException(sprintf('Type d\'entité inconnu : %s', $type)),
        };

        // Double écriture : approbation dans la table typée (transition Phase 3)
        $this->approvePendingTypedHistory($type, $entityId, $reviewer);

        $this->em->flush();
    }

    /**
     * Applique les données d'une révision au contenu live (navigation historique).
     * Sauvegarde l'état courant en tant que nouvelle révision APPROVED avant d'appliquer.
     */
    public function appliquerVersion(Revision $source, User $reviewer): void
    {
        // Sauvegarder l'état courant dans l'historique avant écrasement
        try {
            $currentSnapshot = $this->getCurrentSnapshot($source);

            $backup = new Revision();
            $backup->setEntityType($source->getEntityType());
            $backup->setEntityId($source->getEntityId());
            $backup->setEntityTitle($source->getEntityTitle());
            $backup->setData($currentSnapshot);
            // previousData = données de la version restaurée (diff = "ce qui existait depuis cette version")
            $backup->setPreviousData($source->getData());
            $backup->setStatus(Revision::STATUS_APPROVED);
            $backup->setCreatedBy($reviewer);
            $backup->setReviewedBy($reviewer);
            $backup->setReviewedAt(new \DateTimeImmutable());
            $this->em->persist($backup);
        } catch (\Throwable) {
            // Entité introuvable, on continue sans sauvegarde préalable
        }

        $type = $source->getEntityType();
        $entityId = $source->getEntityId();

        match ($type) {
            'formation' => $this->applyFormation($entityId, $source->getData()),
            'page'      => $this->applyPage($entityId, $source->getData()),
            'works'     => $this->applyWorks($entityId, $source->getData()),
            default     => throw new \InvalidArgumentException(sprintf('Type inconnu : %s', $type)),
        };

        $this->em->flush();
    }

    /**
     * Restaure l'état précédent stocké dans previousData.
     * Permute previousData et l'état live actuel (undo/redo possible).
     *
     * @throws \RuntimeException si aucune sauvegarde n'est disponible
     */
    public function applyPreviousData(Revision $revision): void
    {
        $previousData = $revision->getPreviousData();
        if (null === $previousData) {
            throw new \RuntimeException('Aucune sauvegarde disponible pour cette révision.');
        }

        // Sauvegarder l'état courant avant restauration (pour permettre undo/redo)
        try {
            $revision->setPreviousData($this->getCurrentSnapshot($revision));
        } catch (\Throwable) {
            $revision->setPreviousData(null);
        }

        $type = $revision->getEntityType();
        $entityId = $revision->getEntityId();

        match ($type) {
            'formation' => $this->applyFormation($entityId, $previousData),
            'page'      => $this->applyPage($entityId, $previousData),
            'works'     => $this->applyWorks($entityId, $previousData),
            default     => throw new \InvalidArgumentException(sprintf('Type d\'entité inconnu : %s', $type)),
        };

        $this->em->flush();
    }

    /**
     * Retourne le snapshot actuel de l'entité ciblée par une révision.
     *
     * @return array<string, mixed>
     */
    public function getCurrentSnapshot(Revision $revision): array
    {
        return match ($revision->getEntityType()) {
            'formation' => $this->snapshotFormation(
                $this->em->getRepository(Formation::class)->find($revision->getEntityId())
                ?? throw new \RuntimeException(sprintf('Formation #%d introuvable.', $revision->getEntityId()))
            ),
            'page' => $this->snapshotPage(
                $this->em->getRepository(Page::class)->find($revision->getEntityId())
                ?? throw new \RuntimeException(sprintf('Page #%d introuvable.', $revision->getEntityId()))
            ),
            'works' => $this->snapshotWorks(
                $this->em->getRepository(Works::class)->find($revision->getEntityId())
                ?? throw new \RuntimeException(sprintf('Works #%d introuvable.', $revision->getEntityId()))
            ),
            default => throw new \InvalidArgumentException(sprintf('Type inconnu : %s', $revision->getEntityType())),
        };
    }

    /**
     * Construit un affichage git-like des changements d'une révision.
     * Compare previousData ↔ data et n'affiche QUE les champs modifiés.
     */
    public function buildHistoryDiffHtml(Revision $revision): string
    {
        $before = $revision->getPreviousData();
        $after  = $revision->getData();

        if ($before === null) {
            return '<span class="badge bg-secondary">Création initiale</span>';
        }

        $labels = [
            'title'          => 'Titre',
            'slug'           => 'Slug',
            'description'    => 'Description',
            'content'        => 'Contenu',
            'status'         => 'Statut',
            'publishedAt'    => 'Date de publication',
            'colorPrimary'   => 'Couleur primaire',
            'colorSecondary' => 'Couleur secondaire',
            'formationId'    => 'Formation (ID)',
        ];

        $richFields = ['description', 'content'];
        $changes = [];

        foreach ($after as $key => $newVal) {
            $oldVal = $before[$key] ?? null;
            if ($oldVal === $newVal) {
                continue;
            }

            $label  = $labels[$key] ?? $key;
            $isRich = in_array($key, $richFields, true);
            $changes[] = ['label' => $label, 'key' => $key, 'old' => $oldVal, 'new' => $newVal, 'rich' => $isRich];
        }

        if ($changes === []) {
            return '<span class="text-muted fst-italic small">Aucun changement détecté</span>';
        }

        $uid = 'diff-' . $revision->getId();
        $html = '<ul class="list-unstyled mb-0 small font-monospace">';

        foreach ($changes as $i => $c) {
            if ($c['rich']) {
                $collapseId = $uid . '-' . $c['key'];
                $oldFmt     = $this->formatRichFieldForDiff((string) ($c['old'] ?? ''));
                $newFmt     = $this->formatRichFieldForDiff((string) ($c['new'] ?? ''));
                $html .= sprintf(
                    '<li class="py-1 border-bottom border-light">'
                    . '<span class="text-secondary fw-semibold">%s</span> '
                    . '<button class="btn btn-link btn-sm p-0 text-decoration-none" '
                    . 'type="button" data-bs-toggle="collapse" data-bs-target="#%s" '
                    . 'aria-expanded="false">modifié ▾</button>'
                    . '<div class="collapse mt-1" id="%s">'
                    . '<pre class="p-2 mb-1 bg-danger-subtle text-danger rounded small mb-1"'
                    . ' style="white-space:pre-wrap;word-break:break-all;max-height:none;">%s%s</pre>'
                    . '<pre class="p-2 bg-success-subtle text-success rounded small"'
                    . ' style="white-space:pre-wrap;word-break:break-all;max-height:none;">%s%s</pre>'
                    . '</div></li>',
                    htmlspecialchars($c['label']),
                    $collapseId, $collapseId,
                    htmlspecialchars($oldFmt['text']), $oldFmt['truncated'] ? "\n…" : '',
                    htmlspecialchars($newFmt['text']), $newFmt['truncated'] ? "\n…" : '',
                );
            } else {
                $old = htmlspecialchars($this->truncateForDisplay((string) ($c['old'] ?? '—')));
                $new = htmlspecialchars($this->truncateForDisplay((string) ($c['new'] ?? '—')));
                $html .= sprintf(
                    '<li class="py-1%s">'
                    . '<span class="text-secondary fw-semibold">%s :</span> '
                    . '<del class="text-danger me-1">%s</del>'
                    . '<span class="text-muted me-1">→</span>'
                    . '<ins class="text-success fw-semibold">%s</ins>'
                    . '</li>',
                    $i < count($changes) - 1 ? ' border-bottom border-light' : '',
                    htmlspecialchars($c['label']),
                    $old,
                    $new,
                );
            }
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Construit un tableau HTML de comparaison (valeur actuelle vs proposée).
     * Les champs modifiés sont surlignés en jaune.
     */
    public function buildDiffHtml(Revision $revision): string
    {
        $labels = [
            'title'          => 'Titre',
            'slug'           => 'Slug',
            'description'    => 'Description',
            'content'        => 'Contenu',
            'status'         => 'Statut',
            'publishedAt'    => 'Date de publication',
            'colorPrimary'   => 'Couleur primaire',
            'colorSecondary' => 'Couleur secondaire',
            'formationId'    => 'Formation (ID)',
        ];

        try {
            $current = $this->getCurrentSnapshot($revision);
        } catch (\Throwable) {
            $current = [];
        }

        /** Champs dont le contenu HTML doit être affiché intégralement (pas de truncature). */
        $richFields = ['description', 'content'];

        $proposed = $revision->getData();
        $rows = '';

        foreach ($proposed as $key => $newVal) {
            $label   = $labels[$key] ?? $key;
            $oldVal  = $current[$key] ?? null;
            $changed = $oldVal !== $newVal;

            $isRich  = in_array($key, $richFields, true);
            $rowBg   = $changed ? 'background:#fff3cd;' : '';
            $newBold = $changed ? 'font-weight:bold;' : '';

            if ($isRich) {
                // Affichage HTML complet dans un conteneur scrollable
                $oldCell = sprintf(
                    '<div style="max-height:200px;overflow:auto;font-size:12px;border:1px solid #e0e0e0;padding:6px;background:#fff">%s</div>',
                    (string) ($oldVal ?? '<em>—</em>')
                );
                $newCell = sprintf(
                    '<div style="max-height:200px;overflow:auto;font-size:12px;border:1px solid #e0e0e0;padding:6px;background:#fff">%s</div>',
                    (string) ($newVal ?? '<em>—</em>')
                );
            } else {
                $oldCell = nl2br(htmlspecialchars($this->truncateForDisplay((string) ($oldVal ?? '—'))));
                $newCell = nl2br(htmlspecialchars($this->truncateForDisplay((string) ($newVal ?? '—'))));
            }

            $rows .= sprintf(
                '<tr style="%s">'
                . '<td style="padding:6px 10px;border:1px solid #dee2e6;font-weight:bold;white-space:nowrap;vertical-align:top">%s</td>'
                . '<td style="padding:6px 10px;border:1px solid #dee2e6;color:#6c757d;word-break:break-word;vertical-align:top">%s</td>'
                . '<td style="padding:6px 10px;border:1px solid #dee2e6;%sword-break:break-word;vertical-align:top">%s</td>'
                . '</tr>',
                $rowBg,
                htmlspecialchars($label),
                $oldCell,
                $newBold,
                $newCell,
            );
        }

        return '<table style="width:100%;border-collapse:collapse;font-size:13px;">'
            . '<thead><tr>'
            . '<th style="padding:8px 10px;border:1px solid #dee2e6;background:#f8f9fa;text-align:left">Champ</th>'
            . '<th style="padding:8px 10px;border:1px solid #dee2e6;background:#f8f9fa;text-align:left">Valeur actuelle</th>'
            . '<th style="padding:8px 10px;border:1px solid #dee2e6;background:#fff3cd;text-align:left">Valeur proposée ✎</th>'
            . '</tr></thead>'
            . '<tbody>' . $rows . '</tbody>'
            . '</table>';
    }

    /**
     * Tronque et nettoie une valeur pour l'affichage dans le diff.
     */
    private function truncateForDisplay(string $value, int $max = 300): string
    {
        $clean = strip_tags($value);

        return mb_strlen($clean) > $max ? mb_substr($clean, 0, $max) . '…' : $clean;
    }

    /**
     * Formate le HTML brut d'un champ SunEditor pour affichage dans l'historique.
     * Insère des sauts de ligne après les balises de bloc, retourne max $maxLines lignes.
     * Les balises HTML restent visibles (non rendues).
     *
     * @return array{text: string, truncated: bool}
     */
    private function formatRichFieldForDiff(string $html, int $maxLines = 5): array
    {
        // Insérer un saut de ligne après chaque balise de bloc fermante
        $formatted = preg_replace(
            '/(<\/(p|h[1-6]|li|div|ul|ol|blockquote|pre|tr|td|th)>)/i',
            "$1\n",
            $html
        ) ?? $html;

        // Insérer un saut de ligne après les <br> et <br/>
        $formatted = preg_replace('/<br\s*\/?>/i', "<br>\n", $formatted) ?? $formatted;

        $lines = explode("\n", $formatted);
        $lines = array_values(array_filter($lines, static fn(string $l): bool => trim($l) !== ''));

        $truncated = count($lines) > $maxLines;
        $visible   = array_slice($lines, 0, $maxLines);

        return [
            'text'      => implode("\n", $visible),
            'truncated' => $truncated,
        ];
    }

    /**
     * Envoie un email à l'auteur de la révision pour l'informer
     * de l'approbation ou du rejet de sa demande.
     */
    public function notifyAuthor(Revision $revision, bool $approved): void
    {
        $author = $revision->getCreatedBy();
        $authorEmail = $author?->getEmail();

        if (null === $authorEmail || '' === $authorEmail) {
            return;
        }

        $subject = $approved
            ? '[CF2m] Votre révision a été approuvée — ' . $revision->getEntityTitle()
            : '[CF2m] Votre révision a été rejetée — ' . $revision->getEntityTitle();

        $email = (new TemplatedEmail())
            ->from(new Address($this->mailFrom, 'CF2m — Révisions'))
            ->to($authorEmail)
            ->subject($subject)
            ->htmlTemplate('emails/revision_decision.html.twig')
            ->context([
                'revision' => $revision,
                'approved' => $approved,
            ])
        ;

        $this->mailer->send($email);
    }

    /**
     * Envoie un email de notification à tous les administrateurs
     * pour une révision en attente.
     */
    public function notifyAdmins(Revision $revision): void
    {
        $admins = $this->userRepository->findAdmins();

        if ([] === $admins) {
            return;
        }

        $sender = $this->mailFrom;

        foreach ($admins as $admin) {
            $adminEmail = $admin->getEmail();
            if (null === $adminEmail || '' === $adminEmail) {
                continue;
            }

            $email = (new TemplatedEmail())
                ->from(new Address($sender, 'CF2m — Révisions'))
                ->to($adminEmail)
                ->subject('[CF2m] Nouvelle révision en attente de validation')
                ->htmlTemplate('emails/revision_pending.html.twig')
                ->context(['revision' => $revision])
            ;

            $this->mailer->send($email);
        }
    }

    /**
     * Récupère l'état précédent d'une entité via DBAL (contourne le cache d'identité).
     * Appelé avant le flush : la DB contient encore les anciennes valeurs.
     * Retourne null si l'entité est nouvelle (pas encore en base).
     *
     * @return array<string, mixed>|null
     */
    private function snapshotPreviousFromDb(object $entity): ?array
    {
        $id = method_exists($entity, 'getId') ? $entity->getId() : null;
        if (!$id) {
            return null;
        }

        $conn  = $this->em->getConnection();
        $table = $this->em->getClassMetadata($entity::class)->getTableName();

        $row = $conn->fetchAssociative(
            sprintf('SELECT * FROM `%s` WHERE id = :id', $table),
            ['id' => $id]
        );

        if (!$row) {
            return null;
        }

        // Convertit une chaîne datetime DB en ISO 8601 (même format que les snapshots)
        $fmtDate = static function (?string $val): ?string {
            if ($val === null || $val === '') {
                return null;
            }
            try {
                return (new \DateTimeImmutable($val))->format('c');
            } catch (\Throwable) {
                return $val;
            }
        };

        if ($entity instanceof Formation) {
            return [
                'title'          => $row['title'] ?? null,
                'slug'           => $row['slug'] ?? null,
                'description'    => $row['description'] ?? null,
                'status'         => $row['status'] ?? null,
                'publishedAt'    => $fmtDate($row['published_at'] ?? null),
                'colorPrimary'   => $row['color_primary'] ?? null,
                'colorSecondary' => $row['color_secondary'] ?? null,
            ];
        }

        if ($entity instanceof Page) {
            return [
                'title'       => $row['title'] ?? null,
                'slug'        => $row['slug'] ?? null,
                'content'     => $row['content'] ?? null,
                'status'      => $row['status'] ?? null,
                'publishedAt' => $fmtDate($row['published_at'] ?? null),
            ];
        }

        if ($entity instanceof Works) {
            return [
                'title'       => $row['title'] ?? null,
                'slug'        => $row['slug'] ?? null,
                'description' => $row['description'] ?? null,
                'status'      => $row['status'] ?? null,
                'publishedAt' => $fmtDate($row['published_at'] ?? null),
                'formationId' => $row['formation_id'] ?? null,
            ];
        }

        return null;
    }

    /**
     * Retourne le snapshot live d'une Formation pour comparaison avec les révisions.
     *
     * @return array<string, mixed>
     */
    public function getLiveFormationSnapshot(Formation $entity): array
    {
        return $this->snapshotFormation($entity);
    }

    /**
     * Retourne le snapshot live d'une Page pour comparaison avec les révisions.
     *
     * @return array<string, mixed>
     */
    public function getLivePageSnapshot(Page $entity): array
    {
        return $this->snapshotPage($entity);
    }

    /**
     * Retourne le snapshot live d'un Works pour comparaison avec les révisions.
     *
     * @return array<string, mixed>
     */
    public function getLiveWorksSnapshot(Works $entity): array
    {
        return $this->snapshotWorks($entity);
    }

    /**
     * Snapshot des champs principaux d'une Formation.
     *
     * @return array<string, mixed>
     */
    private function snapshotFormation(Formation $entity): array
    {
        return [
            'title'          => $entity->getTitle(),
            'slug'           => $entity->getSlug(),
            'description'    => $entity->getDescription(),
            'status'         => $entity->getStatus(),
            'publishedAt'    => $entity->getPublishedAt()?->format('c'),
            'colorPrimary'   => $entity->getColorPrimary(),
            'colorSecondary' => $entity->getColorSecondary(),
            'responsables'   => $this->usersToSortedString($entity->getResponsables()),
        ];
    }

    /**
     * Snapshot des champs principaux d'une Page.
     *
     * @return array<string, mixed>
     */
    private function snapshotPage(Page $entity): array
    {
        return [
            'title'       => $entity->getTitle(),
            'slug'        => $entity->getSlug(),
            'content'     => $entity->getContent(),
            'status'      => $entity->getStatus(),
            'publishedAt' => $entity->getPublishedAt()?->format('c'),
            'users'       => $this->usersToSortedString($entity->getUsers()),
        ];
    }

    /**
     * Snapshot des champs principaux d'un Works.
     *
     * @return array<string, mixed>
     */
    private function snapshotWorks(Works $entity): array
    {
        return [
            'title'       => $entity->getTitle(),
            'slug'        => $entity->getSlug(),
            'description' => $entity->getDescription(),
            'status'      => $entity->getStatus(),
            'publishedAt' => $entity->getPublishedAt()?->format('c'),
            'formationId' => $entity->getFormation()?->getId(),
            'users'       => $this->usersToSortedString($entity->getUsers()),
        ];
    }

    /**
     * Convertit un itérable d'utilisateurs en chaîne triée de noms d'utilisateur.
     * Utilisé pour inclure les relations ManyToMany dans les snapshots de comparaison.
     *
     * @param iterable<User> $users
     */
    private function usersToSortedString(iterable $users): string
    {
        $names = [];
        foreach ($users as $user) {
            $names[] = (string) $user;
        }
        sort($names);

        return implode(', ', $names);
    }

    /**
     * Applique un snapshot Formation à l'entité live.
     *
     * @param array<string, mixed> $data
     */
    private function applyFormation(int $entityId, array $data): void
    {
        $entity = $this->em->getRepository(Formation::class)->find($entityId);
        if (null === $entity) {
            throw new \RuntimeException(sprintf('Formation #%d introuvable.', $entityId));
        }

        $entity->setTitle($data['title']);
        $entity->setSlug($data['slug']);
        $entity->setDescription($data['description'] ?? null);
        $entity->setStatus($data['status']);
        $entity->setPublishedAt(isset($data['publishedAt']) ? new \DateTimeImmutable($data['publishedAt']) : null);
        $entity->setColorPrimary($data['colorPrimary'] ?? null);
        $entity->setColorSecondary($data['colorSecondary'] ?? null);
        $entity->setUpdatedAt(new \DateTimeImmutable());
    }

    /**
     * Applique un snapshot Page à l'entité live.
     *
     * @param array<string, mixed> $data
     */
    private function applyPage(int $entityId, array $data): void
    {
        $entity = $this->em->getRepository(Page::class)->find($entityId);
        if (null === $entity) {
            throw new \RuntimeException(sprintf('Page #%d introuvable.', $entityId));
        }

        $entity->setTitle($data['title']);
        $entity->setSlug($data['slug']);
        $entity->setContent($data['content'] ?? '');
        $entity->setStatus($data['status']);
        $entity->setPublishedAt(isset($data['publishedAt']) ? new \DateTimeImmutable($data['publishedAt']) : null);
    }

    // -------------------------------------------------------------------------
    // Convertisseurs snapshot pour les tables typées (Phase 4)
    // -------------------------------------------------------------------------

    /**
     * Convertit un FormationHistory en tableau snapshot (même format que getLiveFormationSnapshot).
     *
     * @return array<string, mixed>
     */
    public function snapshotFromFormationHistory(FormationHistory $h): array
    {
        return [
            'title'          => $h->getTitle(),
            'slug'           => $h->getSlug(),
            'description'    => $h->getDescription(),
            'status'         => $h->getStatus(),
            'publishedAt'    => $h->getPublishedAt()?->format('c'),
            'colorPrimary'   => $h->getColorPrimary(),
            'colorSecondary' => $h->getColorSecondary(),
            'responsables'   => $this->usersToSortedString($h->getResponsables()),
        ];
    }

    /**
     * Convertit un PageHistory en tableau snapshot (même format que getLivePageSnapshot).
     *
     * @return array<string, mixed>
     */
    public function snapshotFromPageHistory(PageHistory $h): array
    {
        return [
            'title'       => $h->getTitle(),
            'slug'        => $h->getSlug(),
            'content'     => $h->getContent(),
            'status'      => $h->getStatus(),
            'publishedAt' => $h->getPublishedAt()?->format('c'),
            'users'       => $this->usersToSortedString($h->getUsers()),
        ];
    }

    /**
     * Convertit un WorksHistory en tableau snapshot (même format que getLiveWorksSnapshot).
     *
     * @return array<string, mixed>
     */
    public function snapshotFromWorksHistory(WorksHistory $h): array
    {
        return [
            'title'       => $h->getTitle(),
            'slug'        => $h->getSlug(),
            'description' => $h->getDescription(),
            'status'      => $h->getStatus(),
            'publishedAt' => $h->getPublishedAt()?->format('c'),
            'formationId' => $h->getFormation()?->getId(),
            'users'       => $this->usersToSortedString($h->getUsers()),
        ];
    }

    /**
     * Construit un affichage git-like des changements entre deux snapshots typés.
     * Si $before === null, retourne un badge "Création initiale".
     * Compare champ par champ, génère le HTML avec collapse pour les champs riches.
     *
     * @param array<string, mixed>      $after
     * @param array<string, mixed>|null $before
     */
    public function buildTypedHistoryDiffHtml(array $after, ?array $before): string
    {
        $labels = [
            'title'          => 'Titre',
            'slug'           => 'Slug',
            'description'    => 'Description',
            'content'        => 'Contenu',
            'status'         => 'Statut',
            'publishedAt'    => 'Date de publication',
            'colorPrimary'   => 'Couleur primaire',
            'colorSecondary' => 'Couleur secondaire',
            'formationId'    => 'Formation (ID)',
            'responsables'   => 'Responsables',
            'users'          => 'Participants',
        ];

        $richFields = ['description', 'content'];

        // Cas création initiale : afficher tous les champs non vides comme "ajoutés"
        if ($before === null) {
            $uid  = 'diff-init-' . substr(md5(serialize($after)), 0, 8);
            $html = '<span class="badge bg-secondary mb-1">Création initiale</span>'
                . '<ul class="list-unstyled mb-0 small font-monospace mt-1">';

            $idx = 0;
            $total = count(array_filter($after, static fn ($v): bool => $v !== null && $v !== ''));

            foreach ($after as $key => $val) {
                if ($val === null || $val === '') {
                    continue;
                }

                $label  = $labels[$key] ?? $key;
                $isRich = in_array($key, $richFields, true);
                $isLast = ++$idx === $total;

                if ($isRich) {
                    $collapseId = $uid . '-' . $key;
                    $fmt        = $this->formatRichFieldForDiff((string) $val);
                    $html .= sprintf(
                        '<li class="py-1%s">'
                        . '<span class="text-secondary fw-semibold">%s</span> '
                        . '<button class="btn btn-link btn-sm p-0 text-decoration-none text-success" '
                        . 'type="button" data-bs-toggle="collapse" data-bs-target="#%s" '
                        . 'aria-expanded="false">voir ▾</button>'
                        . '<div class="collapse mt-1" id="%s">'
                        . '<pre class="p-2 bg-success-subtle text-success rounded small"'
                        . ' style="white-space:pre-wrap;word-break:break-all;max-height:none;">%s%s</pre>'
                        . '</div></li>',
                        $isLast ? '' : ' border-bottom border-light',
                        htmlspecialchars($label),
                        $collapseId,
                        $collapseId,
                        htmlspecialchars($fmt['text']),
                        $fmt['truncated'] ? "\n…" : '',
                    );
                } else {
                    $html .= sprintf(
                        '<li class="py-1%s">'
                        . '<span class="text-secondary fw-semibold">%s :</span> '
                        . '<ins class="text-success fw-semibold">%s</ins>'
                        . '</li>',
                        $isLast ? '' : ' border-bottom border-light',
                        htmlspecialchars($label),
                        htmlspecialchars($this->truncateForDisplay((string) $val)),
                    );
                }
            }

            $html .= '</ul>';

            return $html;
        }

        // Cas modification : afficher uniquement les champs modifiés
        $changes = [];

        foreach ($after as $key => $newVal) {
            $oldVal = $before[$key] ?? null;
            if ($oldVal === $newVal) {
                continue;
            }

            $label    = $labels[$key] ?? $key;
            $isRich   = in_array($key, $richFields, true);
            $changes[] = ['label' => $label, 'key' => $key, 'old' => $oldVal, 'new' => $newVal, 'rich' => $isRich];
        }

        if ($changes === []) {
            return '<span class="text-muted fst-italic small">Aucun changement détecté</span>';
        }

        $count = count($changes);
        $uid   = 'diff-typed-' . substr(md5(serialize($after)), 0, 8);
        $html  = sprintf(
            '<p class="text-muted small mb-1">%d champ%s modifié%s</p>',
            $count,
            $count > 1 ? 's' : '',
            $count > 1 ? 's' : '',
        );
        $html .= '<ul class="list-unstyled mb-0 small font-monospace">';

        foreach ($changes as $i => $c) {
            if ($c['rich']) {
                $collapseId = $uid . '-' . $c['key'];
                $oldFmt     = $this->formatRichFieldForDiff((string) ($c['old'] ?? ''));
                $newFmt     = $this->formatRichFieldForDiff((string) ($c['new'] ?? ''));
                $html .= sprintf(
                    '<li class="py-1 border-bottom border-light">'
                    . '<span class="text-secondary fw-semibold">%s</span> '
                    . '<button class="btn btn-link btn-sm p-0 text-decoration-none" '
                    . 'type="button" data-bs-toggle="collapse" data-bs-target="#%s" '
                    . 'aria-expanded="false">modifié ▾</button>'
                    . '<div class="collapse mt-1" id="%s">'
                    . '<pre class="p-2 mb-1 bg-danger-subtle text-danger rounded small mb-1"'
                    . ' style="white-space:pre-wrap;word-break:break-all;max-height:none;">%s%s</pre>'
                    . '<pre class="p-2 bg-success-subtle text-success rounded small"'
                    . ' style="white-space:pre-wrap;word-break:break-all;max-height:none;">%s%s</pre>'
                    . '</div></li>',
                    htmlspecialchars($c['label']),
                    $collapseId, $collapseId,
                    htmlspecialchars($oldFmt['text']), $oldFmt['truncated'] ? "\n…" : '',
                    htmlspecialchars($newFmt['text']), $newFmt['truncated'] ? "\n…" : '',
                );
            } else {
                $old = htmlspecialchars($this->truncateForDisplay((string) ($c['old'] ?? '—')));
                $new = htmlspecialchars($this->truncateForDisplay((string) ($c['new'] ?? '—')));
                $html .= sprintf(
                    '<li class="py-1%s">'
                    . '<span class="text-secondary fw-semibold">%s :</span> '
                    . '<del class="text-danger me-1">%s</del>'
                    . '<span class="text-muted me-1">→</span>'
                    . '<ins class="text-success fw-semibold">%s</ins>'
                    . '</li>',
                    $i < $count - 1 ? ' border-bottom border-light' : '',
                    htmlspecialchars($c['label']),
                    $old,
                    $new,
                );
            }
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Approuve un FormationHistory : applique le snapshot à la Formation live et flush.
     */
    public function approuverFormationHistory(FormationHistory $h, User $reviewer): void
    {
        $formation = $h->getFormation();
        if ($formation === null) {
            throw new \RuntimeException('FormationHistory sans Formation liée.');
        }

        $this->applyFormation($formation->getId(), $this->snapshotFromFormationHistory($h));

        $h->setRevisionStatus(FormationHistory::STATUS_APPROVED);
        $h->setReviewedBy($reviewer);
        $h->setReviewedAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    /**
     * Rejette un FormationHistory : marque comme rejeté et flush.
     */
    public function rejeterFormationHistory(FormationHistory $h, User $reviewer): void
    {
        $h->setRevisionStatus(FormationHistory::STATUS_REJECTED);
        $h->setReviewedBy($reviewer);
        $h->setReviewedAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    /**
     * Approuve un PageHistory : applique le snapshot à la Page live et flush.
     */
    public function approuverPageHistory(PageHistory $h, User $reviewer): void
    {
        $page = $h->getPage();
        if ($page === null) {
            throw new \RuntimeException('PageHistory sans Page liée.');
        }

        $this->applyPage($page->getId(), $this->snapshotFromPageHistory($h));

        $h->setRevisionStatus(PageHistory::STATUS_APPROVED);
        $h->setReviewedBy($reviewer);
        $h->setReviewedAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    /**
     * Rejette un PageHistory : marque comme rejeté et flush.
     */
    public function rejeterPageHistory(PageHistory $h, User $reviewer): void
    {
        $h->setRevisionStatus(PageHistory::STATUS_REJECTED);
        $h->setReviewedBy($reviewer);
        $h->setReviewedAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    /**
     * Approuve un WorksHistory : applique le snapshot au Works live et flush.
     */
    public function approuverWorksHistory(WorksHistory $h, User $reviewer): void
    {
        $works = $h->getWorks();
        if ($works === null) {
            throw new \RuntimeException('WorksHistory sans Works lié.');
        }

        $this->applyWorks($works->getId(), $this->snapshotFromWorksHistory($h));

        $h->setRevisionStatus(WorksHistory::STATUS_APPROVED);
        $h->setReviewedBy($reviewer);
        $h->setReviewedAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    /**
     * Rejette un WorksHistory : marque comme rejeté et flush.
     */
    public function rejeterWorksHistory(WorksHistory $h, User $reviewer): void
    {
        $h->setRevisionStatus(WorksHistory::STATUS_REJECTED);
        $h->setReviewedBy($reviewer);
        $h->setReviewedAt(new \DateTimeImmutable());

        $this->em->flush();
    }

    /**
     * Restaure une version de l'historique Formation sur l'entité live.
     * Applique le snapshot (champs scalaires + responsables) à la Formation,
     * puis crée une nouvelle entrée d'historique auto-approuvée.
     */
    public function restaurerFormationHistory(FormationHistory $h, User $reviewer): FormationHistory
    {
        $formation = $h->getFormation();
        if ($formation === null) {
            throw new \RuntimeException('FormationHistory sans Formation liée.');
        }

        $this->applyFormation($formation->getId(), $this->snapshotFromFormationHistory($h));

        foreach ($formation->getResponsables()->toArray() as $resp) {
            $formation->removeResponsable($resp);
        }
        foreach ($h->getResponsables() as $resp) {
            $formation->addResponsable($resp);
        }

        $this->em->flush();

        $nextVersion = $this->formationHistoryRepo->getNextVersion($formation);
        $newHistory  = FormationHistory::fromFormation($formation, $reviewer, $nextVersion);
        $newHistory->setRevisionStatus(FormationHistory::STATUS_AUTO_APPROVED);
        $newHistory->setReviewedBy($reviewer);
        $newHistory->setReviewedAt(new \DateTimeImmutable());

        $this->em->persist($newHistory);
        $this->em->flush();

        return $newHistory;
    }

    /**
     * Restaure une version de l'historique Page sur l'entité live.
     * Applique le snapshot (champs scalaires + users) à la Page,
     * puis crée une nouvelle entrée d'historique auto-approuvée.
     */
    public function restaurerPageHistory(PageHistory $h, User $reviewer): PageHistory
    {
        $page = $h->getPage();
        if ($page === null) {
            throw new \RuntimeException('PageHistory sans Page liée.');
        }

        $this->applyPage($page->getId(), $this->snapshotFromPageHistory($h));

        foreach ($page->getUsers()->toArray() as $user) {
            $page->removeUser($user);
        }
        foreach ($h->getUsers() as $user) {
            $page->addUser($user);
        }

        $this->em->flush();

        $nextVersion = $this->pageHistoryRepo->getNextVersion($page);
        $newHistory  = PageHistory::fromPage($page, $reviewer, $nextVersion);
        $newHistory->setRevisionStatus(PageHistory::STATUS_AUTO_APPROVED);
        $newHistory->setReviewedBy($reviewer);
        $newHistory->setReviewedAt(new \DateTimeImmutable());

        $this->em->persist($newHistory);
        $this->em->flush();

        return $newHistory;
    }

    /**
     * Restaure une version de l'historique Works sur l'entité live.
     * Applique le snapshot (champs scalaires + users) au Works,
     * puis crée une nouvelle entrée d'historique auto-approuvée.
     */
    public function restaurerWorksHistory(WorksHistory $h, User $reviewer): WorksHistory
    {
        $works = $h->getWorks();
        if ($works === null) {
            throw new \RuntimeException('WorksHistory sans Works lié.');
        }

        $this->applyWorks($works->getId(), $this->snapshotFromWorksHistory($h));

        foreach ($works->getUsers()->toArray() as $user) {
            $works->removeUser($user);
        }
        foreach ($h->getUsers() as $user) {
            $works->addUser($user);
        }

        $this->em->flush();

        $nextVersion = $this->worksHistoryRepo->getNextVersion($works);
        $newHistory  = WorksHistory::fromWorks($works, $reviewer, $nextVersion);
        $newHistory->setRevisionStatus(WorksHistory::STATUS_AUTO_APPROVED);
        $newHistory->setReviewedBy($reviewer);
        $newHistory->setReviewedAt(new \DateTimeImmutable());

        $this->em->persist($newHistory);
        $this->em->flush();

        return $newHistory;
    }

    /**
     * Envoie un email à l'auteur d'une révision typée (FormationHistory, PageHistory, WorksHistory)
     * pour l'informer de l'approbation ou du rejet de sa demande.
     * Crée un objet Revision transient (non persisté) pour la compatibilité avec les templates email.
     */
    public function notifyAuthorFromHistory(FormationHistory|PageHistory|WorksHistory $history, bool $approved): void
    {
        $createdBy = $history->getCreatedBy();
        if ($createdBy === null) {
            return;
        }

        $transient = new Revision();
        $transient->setCreatedBy($createdBy);
        $transient->setCreatedAt($history->getCreatedAt() ?? new \DateTimeImmutable());
        $transient->setEntityTitle($history->getTitle() ?? '');
        $transient->setReviewedBy($history->getReviewedBy());
        $transient->setReviewedAt($history->getReviewedAt());

        if ($history instanceof FormationHistory) {
            $transient->setEntityType('formation');
        } elseif ($history instanceof PageHistory) {
            $transient->setEntityType('page');
        } else {
            $transient->setEntityType('works');
        }

        $this->notifyAuthor($transient, $approved);
    }

    // -------------------------------------------------------------------------
    // Tables d'historique typées
    // -------------------------------------------------------------------------

    /**
     * Crée une entrée dans la table d'historique typée correspondant à l'entité.
     * Appelé après chaque createRevision() pour maintenir la synchronisation
     * entre l'ancienne table `revision` et les nouvelles tables typées.
     */
    private function saveToTypedHistory(object $entity, User $author, bool $autoApprove): void
    {
        $revisionStatus = $autoApprove
            ? FormationHistory::STATUS_AUTO_APPROVED
            : FormationHistory::STATUS_PENDING;

        if ($entity instanceof Formation) {
            $version = $this->formationHistoryRepo->getNextVersion($entity);
            $history = FormationHistory::fromFormation($entity, $author, $version);
            $history->setRevisionStatus($revisionStatus);
            if ($autoApprove) {
                $history->setReviewedBy($author);
                $history->setReviewedAt(new \DateTimeImmutable());
            }
            $this->em->persist($history);

            return;
        }

        if ($entity instanceof Page) {
            $version = $this->pageHistoryRepo->getNextVersion($entity);
            $history = PageHistory::fromPage($entity, $author, $version);
            $history->setRevisionStatus($revisionStatus);
            if ($autoApprove) {
                $history->setReviewedBy($author);
                $history->setReviewedAt(new \DateTimeImmutable());
            }
            $this->em->persist($history);

            return;
        }

        if ($entity instanceof Works) {
            $version = $this->worksHistoryRepo->getNextVersion($entity);
            $history = WorksHistory::fromWorks($entity, $author, $version);
            $history->setRevisionStatus($revisionStatus);
            if ($autoApprove) {
                $history->setReviewedBy($author);
                $history->setReviewedAt(new \DateTimeImmutable());
            }
            $this->em->persist($history);
        }
    }

    /**
     * Met à jour les champs de l'entrée PENDING dans la table typée
     * pour refléter le nouvel état proposé par le formateur.
     */
    public function updatePendingTypedHistory(object $entity): void
    {
        if ($entity instanceof Formation) {
            $pending = $this->formationHistoryRepo->findPendingForFormation($entity);
            if ($pending === null) {
                return;
            }
            $pending->setTitle($entity->getTitle() ?? '');
            $pending->setSlug($entity->getSlug() ?? '');
            $pending->setDescription($entity->getDescription());
            $pending->setStatus($entity->getStatus());
            $pending->setColorPrimary($entity->getColorPrimary());
            $pending->setColorSecondary($entity->getColorSecondary());
            $pending->setPublishedAt($entity->getPublishedAt());

            return;
        }

        if ($entity instanceof Page) {
            $pending = $this->pageHistoryRepo->findPendingForPage($entity);
            if ($pending === null) {
                return;
            }
            $pending->setTitle($entity->getTitle() ?? '');
            $pending->setSlug($entity->getSlug() ?? '');
            $pending->setContent($entity->getContent() ?? '');
            $pending->setStatus($entity->getStatus());
            $pending->setPublishedAt($entity->getPublishedAt());

            return;
        }

        if ($entity instanceof Works) {
            $pending = $this->worksHistoryRepo->findPendingForWorks($entity);
            if ($pending === null) {
                return;
            }
            $pending->setTitle($entity->getTitle() ?? '');
            $pending->setSlug($entity->getSlug() ?? '');
            $pending->setDescription($entity->getDescription());
            $pending->setStatus($entity->getStatus());
            $pending->setPublishedAt($entity->getPublishedAt());
        }
    }

    /**
     * Marque l'entrée PENDING dans la table typée comme approuvée.
     * Appelé lors de l'approbation d'une révision via applyRevision().
     */
    private function approvePendingTypedHistory(string $entityType, int $entityId, User $reviewer): void
    {
        $pending = match ($entityType) {
            'formation' => $this->formationHistoryRepo->findPendingForFormation(
                $this->em->getRepository(Formation::class)->find($entityId) ?? throw new \RuntimeException()
            ),
            'page' => $this->pageHistoryRepo->findPendingForPage(
                $this->em->getRepository(Page::class)->find($entityId) ?? throw new \RuntimeException()
            ),
            'works' => $this->worksHistoryRepo->findPendingForWorks(
                $this->em->getRepository(Works::class)->find($entityId) ?? throw new \RuntimeException()
            ),
            default => null,
        };

        if ($pending === null) {
            return;
        }

        $pending->setRevisionStatus(FormationHistory::STATUS_APPROVED);
        $pending->setReviewedBy($reviewer);
        $pending->setReviewedAt(new \DateTimeImmutable());
    }

    /**
     * Applique un snapshot Works à l'entité live.
     *
     * @param array<string, mixed> $data
     */
    private function applyWorks(int $entityId, array $data): void
    {
        $entity = $this->em->getRepository(Works::class)->find($entityId);
        if (null === $entity) {
            throw new \RuntimeException(sprintf('Works #%d introuvable.', $entityId));
        }

        $entity->setTitle($data['title']);
        $entity->setSlug($data['slug']);
        $entity->setDescription($data['description'] ?? null);
        $entity->setStatus($data['status']);
        $entity->setPublishedAt(isset($data['publishedAt']) ? new \DateTimeImmutable($data['publishedAt']) : null);

        if (isset($data['formationId'])) {
            $formation = $this->em->getRepository(Formation::class)->find($data['formationId']);
            if (null !== $formation) {
                $entity->setFormation($formation);
            }
        }
    }
}
