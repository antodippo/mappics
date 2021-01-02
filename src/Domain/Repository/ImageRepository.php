<?php
declare(strict_types = 1);

namespace App\Domain\Repository;

use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Exception\ImageNotFoundException;

interface ImageRepository
{
    /**
     * @throws ImageNotFoundException
     */
    public function findById(string $imageId): Image;

    /**
     * @throws ImageNotFoundException
     */
    public function findByFilenameAndGallery(string $filename, Gallery $gallery): Image;

    public function findAll(): array;

    public function add(Image $image): void;
}
