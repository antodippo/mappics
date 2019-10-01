<?php
declare(strict_types = 1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Exception\ImageNotFoundException;
use App\Domain\Repository\ImageRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class DoctrineImageRepository implements ImageRepository
{

    /** @var EntityManager */
    private $em;

    /** @var ObjectRepository */
    private $entityRepository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entityRepository = $this->em->getRepository('App\Domain\Entity\Image');
    }

    public function findById(string $imageId): Image
    {
        $image = $this->entityRepository->find($imageId);
        if (! $image instanceof Image) {
            throw new ImageNotFoundException();
        }

        return $image;
    }

    public function findByFilenameAndGallery(string $filename, Gallery $gallery): Image
    {
        $image = $this->entityRepository->findOneBy([ 'filename' => $filename, 'gallery' => $gallery ]);
        if (! $image instanceof Image) {
            throw new ImageNotFoundException();
        }

        return $image;
    }

    public function add(Image $image): void
    {
        $this->em->persist($image);
        $this->em->flush();
    }
}
