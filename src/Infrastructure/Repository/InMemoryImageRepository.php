<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;


use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Exception\ImageNotFoundException;
use App\Domain\Repository\ImageRepository;

class InMemoryImageRepository implements ImageRepository
{
    /** @var Image[] */
    private array $images = [];

    public function findById(string $imageId): Image
    {
        foreach ($this->images as $image) {
            if ($image->getId() === $imageId) {
                return $image;
            }
        }

        throw new ImageNotFoundException();
    }

    public function findByFilenameAndGallery(string $filename, Gallery $gallery): Image
    {
        foreach ($this->images as $image) {
            if ($image->getFilename() === $filename && $image->getGallery()->getId() === $gallery->getId()) {
                return $image;
            }
        }

        throw new ImageNotFoundException();
    }

    public function findAll(): array
    {
        return $this->images;
    }

    public function add(Image $image): void
    {
        $this->images[$image->getId()] = $image;
    }
}