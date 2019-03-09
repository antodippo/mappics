<?php
declare(strict_types = 1);

namespace App\Domain\Command;

use App\Domain\Entity\FileInfo;

class ProcessGallery
{
    /** @var FileInfo */
    private $galleryFileInfo;

    public function __construct(FileInfo $galleryFileInfo)
    {
        $this->galleryFileInfo = $galleryFileInfo;
    }

    public function getGalleryFileInfo(): FileInfo
    {
        return $this->galleryFileInfo;
    }
}
