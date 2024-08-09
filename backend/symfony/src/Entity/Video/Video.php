<?php

namespace App\Entity\Video;

use App\Entity\Account\Account;
use App\Entity\Video\Share\ShareVideo;
use App\Entity\Video\Share\ShareVideoPublic;
use App\Repository\Video\VideoRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    private const VIDEO_READ = 'video:read';
    private const VIDEOS_READ = 'videos:read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private Account $account;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private ?MD5 $md5;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private ?Folder $folder = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private string $hash;

    #[ORM\Column(length: 255)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private string $extension;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private ?string $codec = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private ?string $size = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private ?string $length = null;

    #[ORM\Column(unique: true)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private string $cdnId;

    #[ORM\Column(nullable: true)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private ?string $path = null;

    #[ORM\Column(nullable: true)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private ?int $originalWidth = null;

    #[ORM\Column(nullable: true)]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private ?int $originalHeight = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $thumbnail = null;

    #[ORM\Column]
    private bool $isDeleted;

    #[ORM\Column]
    #[Groups([self::VIDEO_READ, self::VIDEOS_READ])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    #[Groups([self::VIDEO_READ])]
    private ?string $videoUrl = null;

    #[Groups([self::VIDEOS_READ])]
    private ?string $thumbnailUrl = null;

    /**
     * @var Collection<int, ShareVideo>
     */
    #[ORM\OneToMany(targetEntity: ShareVideo::class, mappedBy: 'video', orphanRemoval: true)]
    private Collection $shares;

    /**
     * @var Collection<int, ShareVideoPublic>
     */
    #[ORM\OneToMany(targetEntity: ShareVideoPublic::class, mappedBy: 'video', orphanRemoval: true)]
    private Collection $sharesPublic;

    /**
     * @param string $name
     * @param Account $account
     */
    public function __construct(string $name, Account $account)
    {
        $this->name = $name;
        $this->account = $account;
        $this->hash = uniqid();
        $this->isDeleted = false;
        $this->createdAt = new DateTimeImmutable();
        $this->shares = new ArrayCollection();
        $this->sharesPublic = new ArrayCollection();
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
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return MD5|null
     */
    public function getMd5(): ?MD5
    {
        return $this->md5;
    }

    /**
     * @param MD5|null $md5
     * @return void
     */
    public function setMd5(?MD5 $md5): void
    {
        $this->md5 = $md5;
    }

    /**
     * @return Folder|null
     */
    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    /**
     * @param Folder|null $folder
     * @return void
     */
    public function setFolder(?Folder $folder): void
    {
        $this->folder = $folder;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     * @return void
     */
    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * @return string|null
     */
    public function getCodec(): ?string
    {
        return $this->codec;
    }

    /**
     * @param string|null $codec
     * @return void
     */
    public function setCodec(?string $codec): void
    {
        $this->codec = $codec;
    }

    /**
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * @param string $size
     * @return void
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string|null
     */
    public function getLength(): ?string
    {
        return $this->length;
    }

    /**
     * @param string|null $length
     * @return void
     */
    public function setLength(?string $length): void
    {
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function getCdnId(): string
    {
        return $this->cdnId;
    }

    /**
     * @param string $cdnId
     * @return void
     */
    public function setCdnId(string $cdnId): void
    {
        $this->cdnId = $cdnId;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     * @return void
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return int|null
     */
    public function getOriginalWidth(): ?int
    {
        return $this->originalWidth;
    }

    /**
     * @param int|null $originalWidth
     * @return void
     */
    public function setOriginalWidth(?int $originalWidth): void
    {
        $this->originalWidth = $originalWidth;
    }

    /**
     * @return int|null
     */
    public function getOriginalHeight(): ?int
    {
        return $this->originalHeight;
    }

    /**
     * @param int|null $originalHeight
     * @return void
     */
    public function setOriginalHeight(?int $originalHeight): void
    {
        $this->originalHeight = $originalHeight;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param string|null $thumbnail
     * @return void
     */
    public function setThumbnail(?string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     * @return void
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * @return void
     */
    public function setDeleted(): void
    {
        $this->isDeleted = true;
        $this->deletedAt = new DateTimeImmutable();
    }

    /**
     * @return string|null
     */
    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    /**
     * @param string|null $videoUrl
     * @return void
     */
    public function setVideoUrl(?string $videoUrl): void
    {
        $this->videoUrl = $videoUrl;
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }

    /**
     * @param string|null $thumbnailUrl
     * @return void
     */
    public function setThumbnailUrl(?string $thumbnailUrl): void
    {
        $this->thumbnailUrl = $thumbnailUrl;
    }

    /**
     * @return Collection<int, ShareVideo>
     */
    public function getShares(): Collection
    {
        return $this->shares;
    }

    /**
     * @return Collection<int, ShareVideoPublic>
     */
    public function getSharesPublic(): Collection
    {
        return $this->sharesPublic;
    }
}
