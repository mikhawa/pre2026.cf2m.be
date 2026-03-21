<?php

declare(strict_types=1);

namespace App\Entity;

/**
 * DTO transient pour les emails de notification de révision.
 * N'est plus persisté en base de données depuis la Phase 5 (tables d'historique typées).
 * Les données réelles sont stockées dans formation_history, page_history, works_history.
 */
class Revision
{
    /** Révision en attente de validation */
    public const STATUS_PENDING = 0;

    /** Révision approuvée et appliquée */
    public const STATUS_APPROVED = 1;

    /** Révision rejetée */
    public const STATUS_REJECTED = 2;

    private ?int $id = null;

    private ?string $entityType = null;

    private ?int $entityId = null;

    private ?string $entityTitle = null;

    /** @var array<string, mixed> */
    private array $data = [];

    /** @var array<string, mixed>|null */
    private ?array $previousData = null;

    private int $status = self::STATUS_PENDING;

    private ?User $createdBy = null;

    private ?\DateTimeImmutable $createdAt = null;

    private ?User $reviewedBy = null;

    private ?\DateTimeImmutable $reviewedAt = null;

    private ?string $reviewNote = null;

    public function __toString(): string
    {
        return $this->entityTitle ?? '';
    }

    /**
     * Getter virtuel retournant une chaîne vide.
     * Utilisé comme accroche pour les templates de diff.
     */
    public function getDiffDisplay(): string
    {
        return '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): static
    {
        $this->entityType = $entityType;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getEntityTitle(): ?string
    {
        return $this->entityTitle;
    }

    public function setEntityTitle(string $entityTitle): static
    {
        $this->entityTitle = $entityTitle;

        return $this;
    }

    /** @return array<string, mixed> */
    public function getData(): array
    {
        return $this->data;
    }

    /** @param array<string, mixed> $data */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /** @return array<string, mixed>|null */
    public function getPreviousData(): ?array
    {
        return $this->previousData;
    }

    /** @param array<string, mixed>|null $previousData */
    public function setPreviousData(?array $previousData): static
    {
        $this->previousData = $previousData;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Retourne le libellé du statut pour l'affichage.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING  => 'En attente',
            self::STATUS_APPROVED => 'Approuvée',
            self::STATUS_REJECTED => 'Rejetée',
            default               => 'Inconnu',
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
