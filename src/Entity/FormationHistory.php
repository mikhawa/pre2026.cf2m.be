<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\RevisionWorkflowTrait;
use App\Repository\FormationHistoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationHistoryRepository::class)]
#[ORM\Table(name: 'formation_history')]
#[ORM\UniqueConstraint(name: 'uq_formation_version', columns: ['formation_id', 'version'])]
#[ORM\Index(name: 'idx_fh_revision_status', columns: ['revision_status'])]
#[ORM\Index(name: 'idx_fh_created_at', columns: ['created_at'])]
#[ORM\HasLifecycleCallbacks]
class FormationHistory
{
    use RevisionWorkflowTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Formation::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Formation $formation = null;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    private int $version = 1;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 800, nullable: true)]
    private ?string $descriptionCourte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 20, options: ['default' => 'draft'])]
    private string $status = 'draft';

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $colorPrimary = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $colorSecondary = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'formation_history_responsable')]
    #[ORM\JoinColumn(name: 'formation_history_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $responsables;

    public function __construct()
    {
        $this->responsables = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    /**
     * Crée un snapshot complet depuis l'entité Formation live.
     */
    public static function fromFormation(Formation $formation, User $author, int $version): self
    {
        $history = new self();
        $history->formation        = $formation;
        $history->version          = $version;
        $history->title            = $formation->getTitle();
        $history->slug             = $formation->getSlug();
        $history->description      = $formation->getDescription();
        $history->descriptionCourte = $formation->getDescriptionCourte();
        $history->logo             = $formation->getLogo();
        $history->status           = $formation->getStatus();
        $history->colorPrimary     = $formation->getColorPrimary();
        $history->colorSecondary   = $formation->getColorSecondary();
        $history->publishedAt      = $formation->getPublishedAt();
        $history->createdBy        = $author;
        $history->createdAt        = new \DateTimeImmutable();

        foreach ($formation->getResponsables() as $user) {
            $history->responsables->add($user);
        }

        return $history;
    }

    public function __toString(): string
    {
        return sprintf('Formation #%d v%d', $this->formation?->getId() ?? 0, $this->version);
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

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescriptionCourte(): ?string
    {
        return $this->descriptionCourte;
    }

    public function setDescriptionCourte(?string $descriptionCourte): static
    {
        $this->descriptionCourte = $descriptionCourte;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getColorPrimary(): ?string
    {
        return $this->colorPrimary;
    }

    public function setColorPrimary(?string $colorPrimary): static
    {
        $this->colorPrimary = $colorPrimary;

        return $this;
    }

    public function getColorSecondary(): ?string
    {
        return $this->colorSecondary;
    }

    public function setColorSecondary(?string $colorSecondary): static
    {
        $this->colorSecondary = $colorSecondary;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /** @return Collection<int, User> */
    public function getResponsables(): Collection
    {
        return $this->responsables;
    }

    public function addResponsable(User $user): static
    {
        if (!$this->responsables->contains($user)) {
            $this->responsables->add($user);
        }

        return $this;
    }

    public function removeResponsable(User $user): static
    {
        $this->responsables->removeElement($user);

        return $this;
    }
}
