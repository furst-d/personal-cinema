<?php

namespace App\Entity\Video;

use App\Repository\Video\ConversionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversionRepository::class)]
class Conversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'conversions')]
    #[ORM\JoinColumn(nullable: false)]
    private Video $video;

    #[ORM\Column]
    private int $quality;

    /**
     * @param Video $video
     * @param int $quality
     */
    public function __construct(Video $video, int $quality)
    {
        $this->video = $video;
        $this->quality = $quality;
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
     * @return int
     */
    public function getQuality(): int
    {
        return $this->quality;
    }
}
