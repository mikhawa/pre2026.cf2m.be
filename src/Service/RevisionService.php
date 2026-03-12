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
     * Applique le snapshot d'une révision à l'entité live correspondante, puis flush.
     */
    public function applyRevision(Revision $revision): void
    {
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
