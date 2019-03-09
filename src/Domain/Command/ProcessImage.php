<?php
declare(strict_types = 1);

namespace App\Domain\Command;

use App\Domain\Entity\FileInfo;
use App\Domain\Entity\Gallery;

class ProcessImage
{
    /**
     * @var FileInfo
     */
    private $imageFileInfo;
    /**
     * @var Gallery
     */
    private $gallery;

    public function __construct(FileInfo $imageFileInfo, Gallery $gallery)
    {
        $this->imageFileInfo = $imageFileInfo;
        $this->gallery = $gallery;
    }

    public function getImageFileInfo(): FileInfo
    {
        return $this->imageFileInfo;
    }

    /**
     * @return Gallery
     */
    public function getGallery(): Gallery
    {
        return $this->gallery;
    }
}
