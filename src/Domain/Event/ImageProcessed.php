<?php
declare(strict_types = 1);

namespace App\Domain\Event;

use App\Domain\Entity\Image;

interface ImageProcessed
{
    public function getImage(): Image;
}
