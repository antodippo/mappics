<?php
declare(strict_types = 1);

namespace App\Application\Service;

interface FileFinder
{
    public function findGalleries(): array;

    public function findImagesInPath(string $path): array;
}
