<?php

namespace App\Entity\Video\Share;

use App\Repository\Video\Share\ShareVideoPublicViewRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShareVideoPublicViewRepository::class)]
class ShareVideoPublicView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'views')]
    #[ORM\JoinColumn(nullable: false)]
    private ShareVideoPublic $shareVideoPublic;

    #[ORM\Column(length: 255)]
    private string $sessionId;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /**
     * @param ShareVideoPublic $shareVideoPublic
     * @param string $sessionId
     */
    public function __construct(ShareVideoPublic $shareVideoPublic, string $sessionId)
    {
        $this->shareVideoPublic = $shareVideoPublic;
        $this->sessionId = $sessionId;
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
     * @return ShareVideoPublic
     */
    public function getShareVideoPublic(): ShareVideoPublic
    {
        return $this->shareVideoPublic;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
