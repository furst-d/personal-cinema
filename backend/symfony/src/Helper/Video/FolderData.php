<?php

namespace App\Helper\Video;

use App\Entity\Video\Folder;

class FolderData
{
    /**
     * @var Folder|null $folder
     */
    private ?Folder $folder;

    /**
     * @var bool $defaultFolder
     */
    private bool $defaultFolder;

    /**
     * @param Folder|null $folder
     * @param bool $defaultFolder
     */
    public function __construct(?Folder $folder, bool $defaultFolder)
    {
        $this->folder = $folder;
        $this->defaultFolder = $defaultFolder;
    }

    /**
     * @return Folder|null
     */
    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    /**
     * @return bool
     */
    public function isDefaultFolder(): bool
    {
        return $this->defaultFolder;
    }
}
