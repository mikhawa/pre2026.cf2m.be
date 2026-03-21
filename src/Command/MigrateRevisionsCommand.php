<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Formation;
use App\Entity\FormationHistory;
use App\Entity\Page;
use App\Entity\PageHistory;
use App\Entity\User;
use App\Entity\Works;
use App\Entity\WorksHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:migrate-revisions',
    description: 'Migre les révisions JSON (table revision) vers les tables d\'historique typées.',
)]
class MigrateRevisionsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simule la migration sans écrire en base')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Vide les tables d\'historique avant de migrer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');
        $force  = (bool) $input->getOption('force');

        $io->title('Migration des révisions JSON → tables d\'historique typées');

        if ($dryRun) {
            $io->warning('Mode simulation (--dry-run) : aucune écriture en base.');
        }

        // Vérifier si les tables cibles sont déjà peuplées
        $conn = $this->em->getConnection();
        $existingCount = (int) $conn->fetchOne('SELECT COUNT(*) FROM formation_history')
            + (int) $conn->fetchOne('SELECT COUNT(*) FROM page_history')
            + (int) $conn->fetchOne('SELECT COUNT(*) FROM works_history');

        if ($existingCount > 0 && !$force && !$dryRun) {
            $io->error(sprintf(
                'Les tables d\'historique contiennent déjà %d entrées. Utilisez --force pour vider et relancer.',
                $existingCount
            ));

            return Command::FAILURE;
        }

        if ($force && !$dryRun) {
            $io->warning('--force : vidage des tables d\'historique avant migration.');
            $conn->executeStatement('DELETE FROM formation_history_responsable');
            $conn->executeStatement('DELETE FROM formation_history');
            $conn->executeStatement('DELETE FROM page_history_user');
            $conn->executeStatement('DELETE FROM page_history');
            $conn->executeStatement('DELETE FROM works_history_user');
            $conn->executeStatement('DELETE FROM works_history');
        }

        // Charger toutes les révisions triées par entité + date de création
        $rows = $conn->fetchAllAssociative(
            'SELECT * FROM revision ORDER BY entity_type, entity_id, created_at ASC, id ASC'
        );

        $total = count($rows);
        $io->info(sprintf('%d révisions à migrer.', $total));

        $counts   = ['formation' => 0, 'page' => 0, 'works' => 0, 'skipped' => 0];
        $versions = []; // clé : 'formation_1' → version courante

        $io->progressStart($total);

        foreach ($rows as $row) {
            $type     = (string) $row['entity_type'];
            $entityId = (int) $row['entity_id'];
            /** @var array<string, mixed> $data */
            $data     = json_decode((string) $row['data'], true) ?? [];

            // Incrémenter la version pour cette entité
            $key = $type . '_' . $entityId;
            $versions[$key] = ($versions[$key] ?? 0) + 1;
            $version = $versions[$key];

            // Résoudre l'auteur
            $createdBy = $this->em->getRepository(User::class)->find((int) $row['created_by_id']);
            if (null === $createdBy) {
                $io->warning(sprintf(
                    'Auteur #%d introuvable pour révision #%d — ignorée.',
                    $row['created_by_id'],
                    $row['id']
                ));
                $counts['skipped']++;
                $io->progressAdvance();
                continue;
            }

            $reviewedBy = null;
            if (!empty($row['reviewed_by_id'])) {
                $reviewedBy = $this->em->getRepository(User::class)->find((int) $row['reviewed_by_id']);
            }

            $createdAt  = new \DateTimeImmutable((string) $row['created_at']);
            $reviewedAt = !empty($row['reviewed_at']) ? new \DateTimeImmutable((string) $row['reviewed_at']) : null;
            $reviewNote = !empty($row['review_note']) ? (string) $row['review_note'] : null;
            $revisionStatus = (int) $row['status']; // 0/1/2 → identique dans le trait

            try {
                match ($type) {
                    'formation' => $this->migrateFormation(
                        $entityId, $version, $data, $createdBy, $createdAt,
                        $reviewedBy, $reviewedAt, $reviewNote, $revisionStatus, $dryRun
                    ),
                    'page' => $this->migratePage(
                        $entityId, $version, $data, $createdBy, $createdAt,
                        $reviewedBy, $reviewedAt, $reviewNote, $revisionStatus, $dryRun
                    ),
                    'works' => $this->migrateWorks(
                        $entityId, $version, $data, $createdBy, $createdAt,
                        $reviewedBy, $reviewedAt, $reviewNote, $revisionStatus, $dryRun
                    ),
                    default => throw new \InvalidArgumentException(sprintf('Type inconnu : %s', $type)),
                };

                if (isset($counts[$type])) {
                    $counts[$type]++;
                }
            } catch (\Throwable $e) {
                $io->warning(sprintf('Révision #%d ignorée : %s', $row['id'], $e->getMessage()));
                $counts['skipped']++;
                // Décaler la version pour ne pas créer de trou
                $versions[$key]--;
            }

            $io->progressAdvance();
        }

        if (!$dryRun) {
            $this->em->flush();
        }

        $io->progressFinish();
        $io->success(sprintf(
            'Migration terminée : %d formations, %d pages, %d works migrés, %d ignorées.',
            $counts['formation'],
            $counts['page'],
            $counts['works'],
            $counts['skipped'],
        ));

        return Command::SUCCESS;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function migrateFormation(
        int $entityId,
        int $version,
        array $data,
        User $createdBy,
        \DateTimeImmutable $createdAt,
        ?User $reviewedBy,
        ?\DateTimeImmutable $reviewedAt,
        ?string $reviewNote,
        int $revisionStatus,
        bool $dryRun,
    ): void {
        $formation = $this->em->getRepository(Formation::class)->find($entityId);
        if (null === $formation) {
            throw new \RuntimeException(sprintf('Formation #%d introuvable.', $entityId));
        }

        $history = new FormationHistory();
        $history->setFormation($formation);
        $history->setVersion($version);
        $history->setTitle((string) ($data['title'] ?? $formation->getTitle() ?? ''));
        $history->setSlug((string) ($data['slug'] ?? $formation->getSlug() ?? ''));
        $history->setDescription($data['description'] ?? null);
        $history->setStatus((string) ($data['status'] ?? $formation->getStatus()));
        $history->setColorPrimary($data['colorPrimary'] ?? null);
        $history->setColorSecondary($data['colorSecondary'] ?? null);
        $history->setPublishedAt(
            !empty($data['publishedAt']) ? new \DateTimeImmutable((string) $data['publishedAt']) : null
        );
        $history->setCreatedBy($createdBy);
        $history->setCreatedAt($createdAt);
        $history->setRevisionStatus($revisionStatus);

        if ($reviewedBy !== null) {
            $history->setReviewedBy($reviewedBy);
        }
        if ($reviewedAt !== null) {
            $history->setReviewedAt($reviewedAt);
        }
        if ($reviewNote !== null) {
            $history->setReviewNote($reviewNote);
        }

        // ManyToMany responsables : depuis l'état live (limitation connue de la migration)
        foreach ($formation->getResponsables() as $user) {
            $history->addResponsable($user);
        }

        if (!$dryRun) {
            $this->em->persist($history);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function migratePage(
        int $entityId,
        int $version,
        array $data,
        User $createdBy,
        \DateTimeImmutable $createdAt,
        ?User $reviewedBy,
        ?\DateTimeImmutable $reviewedAt,
        ?string $reviewNote,
        int $revisionStatus,
        bool $dryRun,
    ): void {
        $page = $this->em->getRepository(Page::class)->find($entityId);
        if (null === $page) {
            throw new \RuntimeException(sprintf('Page #%d introuvable.', $entityId));
        }

        $history = new PageHistory();
        $history->setPage($page);
        $history->setVersion($version);
        $history->setTitle((string) ($data['title'] ?? $page->getTitle() ?? ''));
        $history->setSlug((string) ($data['slug'] ?? $page->getSlug() ?? ''));
        $history->setContent((string) ($data['content'] ?? $page->getContent() ?? ''));
        $history->setStatus((string) ($data['status'] ?? $page->getStatus()));
        $history->setPublishedAt(
            !empty($data['publishedAt']) ? new \DateTimeImmutable((string) $data['publishedAt']) : null
        );
        $history->setCreatedBy($createdBy);
        $history->setCreatedAt($createdAt);
        $history->setRevisionStatus($revisionStatus);

        if ($reviewedBy !== null) {
            $history->setReviewedBy($reviewedBy);
        }
        if ($reviewedAt !== null) {
            $history->setReviewedAt($reviewedAt);
        }
        if ($reviewNote !== null) {
            $history->setReviewNote($reviewNote);
        }

        // ManyToMany users : depuis l'état live
        foreach ($page->getUsers() as $user) {
            $history->addUser($user);
        }

        if (!$dryRun) {
            $this->em->persist($history);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function migrateWorks(
        int $entityId,
        int $version,
        array $data,
        User $createdBy,
        \DateTimeImmutable $createdAt,
        ?User $reviewedBy,
        ?\DateTimeImmutable $reviewedAt,
        ?string $reviewNote,
        int $revisionStatus,
        bool $dryRun,
    ): void {
        $works = $this->em->getRepository(Works::class)->find($entityId);
        if (null === $works) {
            throw new \RuntimeException(sprintf('Works #%d introuvable.', $entityId));
        }

        $history = new WorksHistory();
        $history->setWorks($works);
        $history->setVersion($version);
        $history->setTitle((string) ($data['title'] ?? $works->getTitle() ?? ''));
        $history->setSlug((string) ($data['slug'] ?? $works->getSlug() ?? ''));
        $history->setDescription($data['description'] ?? null);
        $history->setStatus((string) ($data['status'] ?? $works->getStatus()));
        $history->setPublishedAt(
            !empty($data['publishedAt']) ? new \DateTimeImmutable((string) $data['publishedAt']) : null
        );

        // Formation : depuis le JSON si présente, sinon depuis l'état live
        $formation = null;
        if (!empty($data['formationId'])) {
            $formation = $this->em->getRepository(Formation::class)->find((int) $data['formationId']);
        }
        if (null === $formation) {
            $formation = $works->getFormation();
        }
        if (null === $formation) {
            throw new \RuntimeException(sprintf('Aucune formation liée pour Works #%d.', $entityId));
        }
        $history->setFormation($formation);

        $history->setCreatedBy($createdBy);
        $history->setCreatedAt($createdAt);
        $history->setRevisionStatus($revisionStatus);

        if ($reviewedBy !== null) {
            $history->setReviewedBy($reviewedBy);
        }
        if ($reviewedAt !== null) {
            $history->setReviewedAt($reviewedAt);
        }
        if ($reviewNote !== null) {
            $history->setReviewNote($reviewNote);
        }

        // ManyToMany users : depuis l'état live
        foreach ($works->getUsers() as $user) {
            $history->addUser($user);
        }

        if (!$dryRun) {
            $this->em->persist($history);
        }
    }
}
