<?php
declare(strict_types = 1);

namespace App\Domain\Repository;

use App\Domain\Entity\Gallery;
use App\Domain\Exception\GalleryNotFoundException;

interface GalleryRepository
{
    /**
     * @throws GalleryNotFoundException
     */
    public function findByName(string $name): Gallery;

    /**
     * @throws GalleryNotFoundException
     */
    public function findBySlug(string $slug): Gallery;

    public function findAll(): array;

    public function add(Gallery $gallery);
}
