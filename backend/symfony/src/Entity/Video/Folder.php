<?php

namespace App\Entity\Video;

use App\Entity\Account\Account;
use App\Entity\Video\Share\ShareFolder;
use App\Repository\Video\FolderRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FolderRepository::class)]
class Folder
{
    public const FOLDER_READ = 'folder:read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::FOLDER_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::FOLDER_READ])]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'folders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::FOLDER_READ])]
    private Account $owner;

    #[ORM\Column]
    #[Groups([self::FOLDER_READ])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups([self::FOLDER_READ])]
    private DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'folders')]
    private ?Folder $parent;

    /**
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'folder')]
    private Collection $videos;

    /**
     * @var Collection<int, Folder>
     */
    #[ORM\OneToMany(targetEntity: Folder::class, mappedBy: 'parent')]
    private Collection $subFolders;

    /**
     * @var Collection<int, ShareFolder>
     */
    #[ORM\OneToMany(targetEntity: ShareFolder::class, mappedBy: 'folder', orphanRemoval: true)]
    private Collection $shares;

    /**
     * @param string $name
     * @param Account $owner
     * @param Folder|null $parent
     */
    public function __construct(string $name, Account $owner, ?Folder $parent = null)
    {
        $this->name = $name;
        $this->owner = $owner;
        $this->createdAt = new DateTimeImmutable();
        $this->videos = new ArrayCollection();
        $this->subFolders = new ArrayCollection();
        $this->parent = $parent;
        $this->update();
        $this->shares = new ArrayCollection();
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
        $this->update();
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
     * @return Folder|null
     */
    public function getParent(): ?Folder
    {
        return $this->parent;
    }

    #[Groups([self::FOLDER_READ])]
    public function getParentId(): ?int
    {
        return $this->parent?->getId();
    }

    /**
     * @param Folder|null $parent
     * @return void
     */
    public function setParent(?Folder $parent): void
    {
        $this->parent = $parent;
        $this->update();
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    /**
     * @return Collection<int, Folder>
     */
    public function getSubFolders(): Collection
    {
        return $this->subFolders;
    }

    /**
     * Update the folder and its parent
     * @return void
     */
    private function update(): void
    {
        $this->updatedAt = new DateTimeImmutable();

        $parent = $this->getParent();
        $parent?->update();
    }

    /**
     * @return Collection<int, ShareFolder>
     */
    public function getShares(): Collection
    {
        return $this->shares;
    }

    /**
     * @return bool
     */
    #[Groups([self::FOLDER_READ])]
    public function isShared(): bool
    {
        return $this->shares->count() > 0;
    }
}
