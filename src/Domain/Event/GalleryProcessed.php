<?php
declare(strict_types = 1);

namespace App\Domain\Event;

use App\Domain\Entity\Gallery;

interface GalleryProcessed
{
    public function getGallery(): Gallery;
}
