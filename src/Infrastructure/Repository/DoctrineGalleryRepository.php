<?php
declare(strict_types = 1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Gallery;
use App\Domain\Exception\GalleryNotFoundException;
use App\Domain\Repository\GalleryRepository;
use Doctrine\ORM\EntityManager;

class DoctrineGalleryRepository implements GalleryRepository
{
    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findByName(string $name): Gallery
    {
        $gallery = $this->em
            ->getRepository('App\Domain\Entity\Gallery')
            ->findOneBy(['name' => $name]);

        if (! $gallery instanceof Gallery) {
            throw new GalleryNotFoundException();
        }

        return $gallery;
    }

    public function findBySlug(string $slug): Gallery
    {
        $gallery = $this->em
            ->getRepository('App\Domain\Entity\Gallery')
            ->findOneBy(['slug' => $slug]);

        if (! $gallery instanceof Gallery) {
            throw new GalleryNotFoundException();
        }

        return $gallery;
    }

    public function findAll(): array
    {
        return $this->em
            ->getRepository('App\Domain\Entity\Gallery')
            ->findBy([], ['name' => 'ASC']);
    }

    public function add(Gallery $gallery)
    {
        $this->em->persist($gallery);
        $this->em->flush();
    }
}
