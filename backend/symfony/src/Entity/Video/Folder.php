<?php

namespace App\Entity\Video;

use App\Entity\Account\Account;
use App\Repository\Video\FolderRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FolderRepository::class)]
class Folder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'folders')]
    #[ORM\JoinColumn(nullable: false)]
    private Account $owner;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'folders')]
    private ?Folder $parent = null;

    /**
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'folder')]
    private Collection $videos;

    /**
     * @param string $name
     * @param Account $owner
     */
    public function __construct(string $name, Account $owner)
    {
        $this->name = $name;
        $this->owner = $owner;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->videos = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Account
     */
    public function getOwner(): Account
    {
        return $this->owner;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     * @return void
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Folder|null
     */
    public function getParent(): ?Folder
    {
        return $this->parent;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }
}
