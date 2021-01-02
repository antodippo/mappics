<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;


use App\Domain\Entity\Gallery;
use App\Domain\Exception\GalleryNotFoundException;
use App\Domain\Repository\GalleryRepository;

class InMemoryGalleryRepository implements GalleryRepository
{
    /** @var Gallery[]  */
    private array $galleries = [];

    public function findByName(string $name): Gallery
    {
        foreach ($this->galleries as $gallery) {
            if ($gallery->getName() === $name) {
                return $gallery;
            }
        }

        throw new GalleryNotFoundException();
    }

    public function findBySlug(string $slug): Gallery
    {
        foreach ($this->galleries as $gallery) {
            if ($gallery->getSlug() === $slug) {
                return $gallery;
            }
        }

        throw new GalleryNotFoundException();
    }

    public function findAll(): array
    {
        return $this->galleries;
    }

    public function add(Gallery $gallery)
    {
        $this->galleries[$gallery->getId()] = $gallery;
    }
}