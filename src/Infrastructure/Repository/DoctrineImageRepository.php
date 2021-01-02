<?php
declare(strict_types = 1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Exception\ImageNotFoundException;
use App\Domain\Repository\ImageRepository;
use Doctrine\ORM\EntityManager;

class DoctrineImageRepository implements ImageRepository
{
    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findById(string $imageId): Image
    {
        $image = $this->em
            ->getRepository('App\Domain\Entity\Image')
            ->find($imageId);
        if (! $image instanceof Image) {
            throw new ImageNotFoundException();
        }

        return $image;
    }

    public function findByFilenameAndGallery(string $filename, Gallery $gallery): Image
    {
        $image = $this->em
            ->getRepository('App\Domain\Entity\Image')
            ->findOneBy([ 'filename' => $filename, 'gallery' => $gallery ]);
        if (! $image instanceof Image) {
            throw new ImageNotFoundException();
        }

        return $image;
    }

    public function findAll(): array
    {
        return $this->em
            ->getRepository('App\Domain\Entity\Image')
            ->findBy([], ['name' => 'ASC']);
    }

    public function add(Image $image): void
    {
        $this->em->persist($image);
        $this->em->flush();
    }
}
