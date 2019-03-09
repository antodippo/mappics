<?php
declare(strict_types = 1);

namespace App\Application\Service;

interface FileFinder
{
    public function findGalleries(): iterable;

    public function findImagesInPath(string $path): iterable;
}
