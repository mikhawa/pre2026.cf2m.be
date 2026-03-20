<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\RevisionWorkflowTrait;
use App\Repository\PageHistoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageHistoryRepository::class)]
#[ORM\Table(name: 'page_history')]
#[ORM\UniqueConstraint(name: 'uq_page_version', columns: ['page_id', 'version'])]
#[ORM\Index(name: 'idx_ph_revision_status', columns: ['revision_status'])]
#[ORM\Index(name: 'idx_ph_created_at', columns: ['created_at'])]
#[ORM\HasLifecycleCallbacks]
class PageHistory
{
    use RevisionWorkflowTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Page::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Page $page = null;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    private int $version = 1;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 20, options: ['default' => 'draft'])]
    private string $status = 'draft';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'page_history_user')]
    #[ORM\JoinColumn(name: 'page_history_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
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
     * Crée un snapshot complet depuis l'entité Page live.
     */
    public static function fromPage(Page $page, User $author, int $version): self
    {
        $history = new self();
        $history->page        = $page;
        $history->version     = $version;
        $history->title       = $page->getTitle();
        $history->slug        = $page->getSlug();
        $history->content     = $page->getContent();
        $history->status      = $page->getStatus();
        $history->publishedAt = $page->getPublishedAt();
        $history->createdBy   = $author;
        $history->createdAt   = new \DateTimeImmutable();

        foreach ($page->getUsers() as $user) {
            $history->users->add($user);
        }

        return $history;
    }

    public function __toString(): string
    {
        return sprintf('Page #%d v%d', $this->page?->getId() ?? 0, $this->version);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): static
    {
        $this->page = $page;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

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
