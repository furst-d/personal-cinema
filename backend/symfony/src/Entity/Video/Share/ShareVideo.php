<?php

namespace App\Entity\Video\Share;

use App\Entity\Account\Account;
use App\Entity\Video\Video;
use App\Repository\Video\Share\ShareVideoRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ShareVideoRepository::class)]
class ShareVideo
{
    public const SHARE_VIDEO_READ = 'shareVideo:read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::SHARE_VIDEO_READ])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shares')]
    #[ORM\JoinColumn(nullable: false)]
    private Video $video;

    #[ORM\ManyToOne(inversedBy: 'sharedVideos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::SHARE_VIDEO_READ])]
    private Account $account;

    #[ORM\Column]
    #[Groups([self::SHARE_VIDEO_READ])]
    private DateTimeImmutable $createdAt;

    /**
     * @param Video $video
     * @param Account $account
     */
    public function __construct(Video $video, Account $account)
    {
        $this->video = $video;
        $this->account = $account;
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
     * @return Video
     */
    public function getVideo(): Video
    {
        return $this->video;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
