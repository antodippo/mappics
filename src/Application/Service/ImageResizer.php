<?php
declare(strict_types = 1);

namespace App\Application\Service;

use App\Domain\Entity\Image;

interface ImageResizer
{
    public function resize(Image $image): string;

    public function createThumbnail(Image $image): string;
}
