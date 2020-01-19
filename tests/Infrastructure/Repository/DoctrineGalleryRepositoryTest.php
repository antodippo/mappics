<?php
declare(strict_types=1);

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Entity\Gallery;
use App\Domain\Exception\GalleryNotFoundException;
use App\Infrastructure\Repository\DoctrineGalleryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class DoctrineGalleryRepositoryTest extends TestCase
{
    /** @var EntityManager */
    private $em;

    public function setUp(): void
    {
        parent::setUp();
        $this->em = \Phake::mock(EntityManager::class);
    }

    public function test_findByName(): void
    {
        $gallery = \Phake::mock(Gallery::class);
        $repository = \Phake::mock(EntityRepository::class);
        \Phake::when($repository)->findOneBy(['name' => 'Gallery Name'])->thenReturn($gallery);
        \Phake::when($this->em)->getRepository('App\Domain\Entity\Gallery')->thenReturn($repository);

        $doctrineGalleryRepository = new DoctrineGalleryRepository($this->em);
        $this->assertEquals($gallery, $doctrineGalleryRepository->findByName('Gallery Name'));
    }

    public function test_findByNameThrowsException(): void
    {
        $this->expectException(GalleryNotFoundException::class);

        $repository = \Phake::mock(EntityRepository::class);
        \Phake::when($repository)->find(['name' => 'Gallery Name'])->thenReturn(null);
        \Phake::when($this->em)->getRepository('App\Domain\Entity\Gallery')->thenReturn($repository);

        $doctrineGalleryRepository = new DoctrineGalleryRepository($this->em);
        $doctrineGalleryRepository->findByName('Gallery Name');
    }

    public function test_findBySlug(): void
    {
        $gallery = \Phake::mock(Gallery::class);
        $repository = \Phake::mock(EntityRepository::class);
        \Phake::when($repository)->findOneBy(['slug' => 'gallery-slug'])->thenReturn($gallery);
        \Phake::when($this->em)->getRepository('App\Domain\Entity\Gallery')->thenReturn($repository);

        $doctrineGalleryRepository = new DoctrineGalleryRepository($this->em);
        $this->assertEquals($gallery, $doctrineGalleryRepository->findBySlug('gallery-slug'));
    }

    public function test_findBySlugThrowsException(): void
    {
        $this->expectException(GalleryNotFoundException::class);

        $repository = \Phake::mock(EntityRepository::class);
        \Phake::when($repository)->find(['slug' => 'gallery-slug'])->thenReturn(null);
        \Phake::when($this->em)->getRepository('App\Domain\Entity\Gallery')->thenReturn($repository);

        $doctrineGalleryRepository = new DoctrineGalleryRepository($this->em);
        $doctrineGalleryRepository->findBySlug('gallery-slug');
    }

    public function test_findAll(): void
    {
        $galleries = [
            \Phake::mock(Gallery::class),
            \Phake::mock(Gallery::class),
            \Phake::mock(Gallery::class)
        ];

        $repository = \Phake::mock(EntityRepository::class);
        \Phake::when($repository)->findBy([], ['name' => 'ASC'])->thenReturn($galleries);
        \Phake::when($this->em)->getRepository('App\Domain\Entity\Gallery')->thenReturn($repository);

        $doctrineGalleryRepository = new DoctrineGalleryRepository($this->em);
        $this->assertEquals($galleries, $doctrineGalleryRepository->findAll());
    }

    public function test_add(): void
    {
        $gallery = \Phake::mock(Gallery::class);

        $doctrineGalleryRepository = new DoctrineGalleryRepository($this->em);
        $doctrineGalleryRepository->add($gallery);

        \Phake::verify($this->em, \Phake::times(1))->persist($gallery);
        \Phake::verify($this->em, \Phake::times(1))->flush();
    }

}
