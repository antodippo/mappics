<?php
declare(strict_types=1);

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Exception\ImageNotFoundException;
use App\Infrastructure\Repository\DoctrineImageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class DoctrineImageRepositoryTest extends TestCase
{
    /** @var EntityManager */
    private $em;

    public function setUp(): void
    {
        parent::setUp();
        $this->em = \Phake::mock(EntityManager::class);
    }

    public function test_findById(): void
    {
        $image = \Phake::mock(Image::class);
        $repository = \Phake::mock(EntityRepository::class);
        \Phake::when($repository)->find('image-id')->thenReturn($image);
        \Phake::when($this->em)->getRepository('App\Domain\Entity\Image')->thenReturn($repository);

        $doctrineImageRepository = new DoctrineImageRepository($this->em);
        $this->assertEquals($image, $doctrineImageRepository->findById('image-id'));
    }

    public function test_findByIdThrowsException(): void
    {
        $this->expectException(ImageNotFoundException::class);

        $repository = \Phake::mock(EntityRepository::class);
        \Phake::when($repository)->find('image-id')->thenReturn(null);
        \Phake::when($this->em)->getRepository('App\Domain\Entity\Image')->thenReturn($repository);

        $doctrineImageRepository = new DoctrineImageRepository($this->em);
        $doctrineImageRepository->findById('image-id');
    }

    public function test_findByFilenameAndGallery(): void
    {
        $image = \Phake::mock(Image::class);
        $gallery = \Phake::mock(Gallery::class);
        $repository = \Phake::mock(EntityRepository::class);
        \Phake::when($repository)->findOneBy(['filename' => 'filename.jpg', 'gallery' => $gallery])->thenReturn($image);
        \Phake::when($this->em)->getRepository('App\Domain\Entity\Image')->thenReturn($repository);

        $doctrineImageRepository = new DoctrineImageRepository($this->em);
        $this->assertEquals($image, $doctrineImageRepository->findByFilenameAndGallery('filename.jpg', $gallery));
    }

    public function test_findByFilenameAndGalleryThrowsException(): void
    {
        $this->expectException(ImageNotFoundException::class);

        $gallery = \Phake::mock(Gallery::class);
        $repository = \Phake::mock(EntityRepository::class);
        \Phake::when($repository)->findOneBy(['filename' => 'filename.jpg', 'gallery' => $gallery])->thenReturn(null);
        \Phake::when($this->em)->getRepository('App\Domain\Entity\Image')->thenReturn($repository);

        $doctrineImageRepository = new DoctrineImageRepository($this->em);
        $doctrineImageRepository->findByFilenameAndGallery('filename.jpg', $gallery);
    }

    public function test_add(): void
    {
        $image = \Phake::mock(Image::class);

        $doctrineImageRepository = new DoctrineImageRepository($this->em);
        $doctrineImageRepository->add($image);

        \Phake::verify($this->em, \Phake::times(1))->persist($image);
        \Phake::verify($this->em, \Phake::times(1))->flush();

    }
}
