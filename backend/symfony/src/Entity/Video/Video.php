<?php

namespace App\Entity\Video;

use App\Entity\Account\Account;
use App\Repository\Video\VideoRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['video:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['video:read'])]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['video:read'])]
    private Account $account;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['video:read'])]
    private ?MD5 $md5;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[Groups(['video:read'])]
    private ?Folder $folder = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['video:read'])]
    private string $hash;

    #[ORM\Column(length: 255)]
    #[Groups(['video:read'])]
    private string $extension;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['video:read'])]
    private ?string $codec = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups(['video:read'])]
    private string $size;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    #[Groups(['video:read'])]
    private ?string $length = null;

    #[ORM\Column(unique: true)]
    #[Groups(['video:read'])]
    private string $cdnId;

    #[ORM\Column(nullable: true)]
    private ?string $path = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['video:read'])]
    private ?int $originalWidth = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['video:read'])]
    private ?int $originalHeight = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['video:read'])]
    private ?string $thumbnail = null;

    #[ORM\Column]
    private bool $isDeleted;

    #[ORM\Column]
    #[Groups(['video:read'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

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
     * @return string
     */
    public function getSize(): string
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
}
