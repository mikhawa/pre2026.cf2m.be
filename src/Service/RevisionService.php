<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Formation;
use App\Entity\Page;
use App\Entity\Revision;
use App\Entity\User;
use App\Entity\Works;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
    ) {
    }

    /**
     * Crée une révision (snapshot) pour une entité donnée.
     * Persiste la révision sans effectuer de flush.
     */
    public function createRevision(object $entity, User $author, bool $autoApprove): Revision
    {
        $revision = new Revision();
        $revision->setCreatedBy($author);

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

        $this->em->persist($revision);

        return $revision;
    }

    /**
     * Applique le snapshot d'une révision à l'entité live correspondante.
     * Sauvegarde d'abord l'état actuel comme révision de sauvegarde, puis flush.
     */
    public function applyRevision(Revision $revision, User $reviewer): void
    {
        // Sauvegarde de l'état actuel avant modification
        $this->saveCurrentAsBackup($revision, $reviewer);

        $data = $revision->getData();
        $type = $revision->getEntityType();
        $entityId = $revision->getEntityId();

        match ($type) {
            'formation' => $this->applyFormation($entityId, $data),
            'page'      => $this->applyPage($entityId, $data),
            'works'     => $this->applyWorks($entityId, $data),
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
     * Sauvegarde l'état actuel de l'entité comme révision de sauvegarde (APPROVED).
     */
    private function saveCurrentAsBackup(Revision $revision, User $reviewer): void
    {
        try {
            $currentData = $this->getCurrentSnapshot($revision);
        } catch (\Throwable) {
            return;
        }

        $backup = new Revision();
        $backup->setEntityType($revision->getEntityType());
        $backup->setEntityId($revision->getEntityId());
        $backup->setEntityTitle($revision->getEntityTitle() . ' [avant restauration]');
        $backup->setData($currentData);
        $backup->setStatus(Revision::STATUS_APPROVED);
        $backup->setCreatedBy($reviewer);
        $backup->setReviewedBy($reviewer);
        $backup->setReviewedAt(new \DateTimeImmutable());

        $this->em->persist($backup);
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
     * Envoie un email de notification à tous les administrateurs
     * pour une révision en attente.
     */
    public function notifyAdmins(Revision $revision): void
    {
        $admins = $this->userRepository->findAdmins();

        if ([] === $admins) {
            return;
        }

        $sender = 'noreply@cf2m.be';

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
        ];
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
