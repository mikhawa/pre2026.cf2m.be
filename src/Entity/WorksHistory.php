<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\RevisionWorkflowTrait;
use App\Repository\WorksHistoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorksHistoryRepository::class)]
#[ORM\Table(name: 'works_history')]
#[ORM\UniqueConstraint(name: 'uq_works_version', columns: ['works_id', 'version'])]
#[ORM\Index(name: 'idx_wh_revision_status', columns: ['revision_status'])]
#[ORM\Index(name: 'idx_wh_created_at', columns: ['created_at'])]
#[ORM\HasLifecycleCallbacks]
class WorksHistory
{
    use RevisionWorkflowTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Works::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Works $works = null;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    private int $version = 1;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20, options: ['default' => 'draft'])]
    private string $status = 'draft';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\ManyToOne(targetEntity: Formation::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $formation = null;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'works_history_user')]
    #[ORM\JoinColumn(name: 'works_history_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    /**
     * Crée un snapshot complet depuis l'entité Works live.
     */
    public static function fromWorks(Works $works, User $author, int $version): self
    {
        $history = new self();
        $history->works       = $works;
        $history->version     = $version;
        $history->title       = $works->getTitle();
        $history->slug        = $works->getSlug();
        $history->description = $works->getDescription();
        $history->status      = $works->getStatus();
        $history->publishedAt = $works->getPublishedAt();
        $history->formation   = $works->getFormation();
        $history->createdBy   = $author;
        $history->createdAt   = new \DateTimeImmutable();

        foreach ($works->getUsers() as $user) {
            $history->users->add($user);
        }

        return $history;
    }

    public function __toString(): string
    {
        return sprintf('Works #%d v%d', $this->works?->getId() ?? 0, $this->version);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorks(): ?Works
    {
        return $this->works;
    }

    public function setWorks(?Works $works): static
    {
        $this->works = $works;

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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    /** @return Collection<int, User> */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }
}
