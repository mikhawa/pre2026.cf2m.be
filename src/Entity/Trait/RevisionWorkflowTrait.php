<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait commun pour les champs de workflow de validation des révisions.
 * Partagé par FormationHistory, PageHistory et WorksHistory.
 */
trait RevisionWorkflowTrait
{
    /** Révision soumise par un formateur, en attente de validation */
    public const STATUS_PENDING = 0;

    /** Révision approuvée et appliquée par un admin */
    public const STATUS_APPROVED = 1;

    /** Révision rejetée par un admin */
    public const STATUS_REJECTED = 2;

    /** Sauvegarde auto-approuvée (admin ou super-admin en écriture directe) */
    public const STATUS_AUTO_APPROVED = 3;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true, 'default' => 0])]
    private int $revisionStatus = 0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $reviewedBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $reviewedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $reviewNote = null;

    public function getRevisionStatus(): int
    {
        return $this->revisionStatus;
    }

    public function setRevisionStatus(int $revisionStatus): static
    {
        $this->revisionStatus = $revisionStatus;

        return $this;
    }

    public function getRevisionStatusLabel(): string
    {
        return match ($this->revisionStatus) {
            self::STATUS_PENDING       => 'En attente',
            self::STATUS_APPROVED      => 'Approuvée',
            self::STATUS_REJECTED      => 'Rejetée',
            self::STATUS_AUTO_APPROVED => 'Auto-approuvée',
            default                    => 'Inconnu',
        };
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReviewedBy(): ?User
    {
        return $this->reviewedBy;
    }

    public function setReviewedBy(?User $reviewedBy): static
    {
        $this->reviewedBy = $reviewedBy;

        return $this;
    }

    public function getReviewedAt(): ?\DateTimeImmutable
    {
        return $this->reviewedAt;
    }

    public function setReviewedAt(?\DateTimeImmutable $reviewedAt): static
    {
        $this->reviewedAt = $reviewedAt;

        return $this;
    }

    public function getReviewNote(): ?string
    {
        return $this->reviewNote;
    }

    public function setReviewNote(?string $reviewNote): static
    {
        $this->reviewNote = $reviewNote;

        return $this;
    }
}
