<?php
declare(strict_types = 1);

namespace App\Application\Service;

use App\Domain\Entity\ExifData;
use App\Domain\Entity\FileInfo;

interface ExifReader
{
    public function getExifData(FileInfo $imageFile): ExifData;
}
