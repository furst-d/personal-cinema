<?php

namespace App\Helper\Video;

use Symfony\Component\String\Slugger\AsciiSlugger;

class NameNormalizer
{
    /**
     * Normalize the name of the video or another entity.
     *
     * @param string $name
     * @return string
     */
    public static function normalize(string $name): string
    {
        $slugger = new AsciiSlugger();
        $slug = $slugger->slug($name)->lower();

        return $slug->toString();
    }
}
