<?php

namespace App\Entity\Video\Share;

use App\Entity\Video\Video;
use App\Repository\Video\Share\ShareVideoPublicRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShareVideoPublicRepository::class)]
class ShareVideoPublic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sharesPublic')]
    #[ORM\JoinColumn(nullable: false)]
    private Video $video;

    #[ORM\Column(length: 255)]
    private string $hash;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $expiredAt;

    /**
     * @var Collection<int, ShareVideoPublicView>
     */
    #[ORM\OneToMany(targetEntity: ShareVideoPublicView::class, mappedBy: 'shareVideoPublic', orphanRemoval: true)]
    private Collection $views;

    /**
     * @param Video $video
     * @param string $hash
     */
    public function __construct(Video $video, string $hash)
    {
        $this->video = $video;
        $this->hash = $hash;
        $this->createdAt = new DateTimeImmutable();
        $this->expiredAt = new DateTimeImmutable('+1 week');
        $this->views = new ArrayCollection();
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
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
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
    public function getExpiredAt(): DateTimeImmutable
    {
        return $this->expiredAt;
    }

    /**
     * @return Collection<int, ShareVideoPublicView>
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(ShareVideoPublicView $view): static
    {
        if (!$this->views->contains($view)) {
            $this->views->add($view);
            $view->setShareVideoPublic($this);
        }

        return $this;
    }

    public function removeView(ShareVideoPublicView $view): static
    {
        if ($this->views->removeElement($view)) {
            // set the owning side to null (unless already changed)
            if ($view->getShareVideoPublic() === $this) {
                $view->setShareVideoPublic(null);
            }
        }

        return $this;
    }
}
