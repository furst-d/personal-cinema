<?php

namespace App\Entity\Video;

use App\Entity\Account\Account;
use App\Repository\Video\VideoRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    private MD5 $md5;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    private ?Folder $folder = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $hash;

    #[ORM\Column(length: 255)]
    private string $extension;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\Column(type: Types::BIGINT)]
    private string $size;

    #[ORM\Column(type: Types::BIGINT)]
    private string $length;

    #[ORM\Column(unique: true)]
    private int $cdnId;

    #[ORM\Column(length: 255, unique: true)]
    private string $cdnLink;

    #[ORM\Column]
    private int $originalWidth;

    #[ORM\Column]
    private int $originalHeight;

    #[ORM\Column]
    private int $thumbsCount;

    #[ORM\Column]
    private bool $isDeleted;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deletedAt;

    /**
     * @param string $name
     * @param Account $account
     */
    public function __construct(string $name, Account $account)
    {
        $this->name = $name;
        $this->account = $account;
        $this->hash = uniqid();
        $this->thumbsCount = 0;
        $this->isDeleted = false;
        $this->createdAt = new DateTimeImmutable();
        $this->deletedAt = null;
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
     * @return MD5
     */
    public function getMd5(): MD5
    {
        return $this->md5;
    }

    /**
     * @param MD5 $md5
     * @return void
     */
    public function setMd5(MD5 $md5): void
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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
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
     * @return string
     */
    public function getLength(): string
    {
        return $this->length;
    }

    /**
     * @param string $length
     * @return void
     */
    public function setLength(string $length): void
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getCdnId(): int
    {
        return $this->cdnId;
    }

    /**
     * @param int $cdnId
     * @return void
     */
    public function setCdnId(int $cdnId): void
    {
        $this->cdnId = $cdnId;
    }

    /**
     * @return string
     */
    public function getCdnLink(): string
    {
        return $this->cdnLink;
    }

    /**
     * @param string $cdnLink
     * @return void
     */
    public function setCdnLink(string $cdnLink): void
    {
        $this->cdnLink = $cdnLink;
    }

    /**
     * @return int
     */
    public function getOriginalWidth(): int
    {
        return $this->originalWidth;
    }

    /**
     * @param int $originalWidth
     * @return void
     */
    public function setOriginalWidth(int $originalWidth): void
    {
        $this->originalWidth = $originalWidth;
    }

    /**
     * @return int
     */
    public function getOriginalHeight(): int
    {
        return $this->originalHeight;
    }

    /**
     * @param int $originalHeight
     * @return void
     */
    public function setOriginalHeight(int $originalHeight): void
    {
        $this->originalHeight = $originalHeight;
    }

    /**
     * @return int
     */
    public function getThumbsCount(): int
    {
        return $this->thumbsCount;
    }

    /**
     * @param int $thumbsCount
     * @return void
     */
    public function setThumbsCount(int $thumbsCount): void
    {
        $this->thumbsCount = $thumbsCount;
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