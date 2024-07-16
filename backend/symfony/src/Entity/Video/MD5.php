<?php

namespace App\Entity\Video;

use App\Repository\Video\MD5Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MD5Repository::class)]
class MD5
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $md5;

    #[ORM\Column]
    private bool $isBlacklisted;

    /**
     * @var Collection<int, Video>
     */
    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'account', orphanRemoval: true)]
    private Collection $videos;

    /**
     * @param string $md5
     */
    public function __construct(string $md5)
    {
        $this->md5 = $md5;
        $this->isBlacklisted = false;
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
    public function getMd5(): string
    {
        return $this->md5;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    /**
     * @return bool
     */
    public function isBlacklisted(): bool
    {
        return $this->isBlacklisted;
    }

    /**
     * @param bool $isBlacklisted
     * @return void
     */
    public function setIsBlacklisted(bool $isBlacklisted): void
    {
        $this->isBlacklisted = $isBlacklisted;
    }
}
