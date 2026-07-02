<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FormationStagiaireRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité pivot rattachant un utilisateur (stagiaire) à une formation précise.
 *
 * La présence d'au moins une ligne FormationStagiaire pour un utilisateur
 * déclenche la synchronisation physique de ROLE_STAGIAIRE (voir StagiaireService).
 * Les champs addedBy/addedAt assurent la traçabilité (qui a inscrit ce stagiaire, quand).
 */
#[ORM\Entity(repositoryClass: FormationStagiaireRepository::class)]
#[ORM\Table(name: 'formation_stagiaire')]
#[ORM\UniqueConstraint(name: 'uq_formation_stagiaire', columns: ['formation_id', 'user_id'])]
#[ORM\HasLifecycleCallbacks]
class FormationStagiaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'stagiaires')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Formation $formation = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $addedBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\PrePersist]
    public function setAddedAtValue(): void
    {
        if (null === $this->addedAt) {
            $this->addedAt = new \DateTimeImmutable();
        }
    }

    public function __toString(): string
    {
        return (string) $this->user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAddedBy(): ?User
    {
        return $this->addedBy;
    }

    public function setAddedBy(?User $addedBy): static
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }
}
