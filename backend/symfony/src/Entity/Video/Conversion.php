<?php

namespace App\Entity\Video;

use App\Repository\Video\ConversionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ConversionRepository::class)]
class Conversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $width;

    #[ORM\Column]
    private int $height;

    #[ORM\Column]
    private int $bandwidth;

    /**
     * @var Collection<int, Video>
     */
    #[ORM\ManyToMany(targetEntity: Video::class, mappedBy: 'conversions')]
    private Collection $videos;

    /**
     * @param int $width
     * @param int $height
     * @param int $bandwidth
     */
    public function __construct(int $width, int $height, int $bandwidth)
    {
        $this->width = $width;
        $this->height = $height;
        $this->bandwidth = $bandwidth;
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
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getBandwidth(): int
    {
        return $this->bandwidth;
    }

    /**
     * @return string
     */
    #[Groups([Video::VIDEO_READ, Video::VIDEOS_READ])]
    public function getLabel(): string
    {
        return $this->getHeight() . 'p';
    }

    /**
     * @return string
     */
    #[Groups([Video::VIDEO_READ, Video::VIDEOS_READ])]
    public function getResolution(): string
    {
        return $this->width . 'x' . $this->height;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    /**
     * @param Video $video
     * @return void
     */
    public function addVideo(Video $video): void
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->addConversion($this);
        }
    }
}
