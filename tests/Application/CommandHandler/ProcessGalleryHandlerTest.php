<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\CommandHandler\ProcessGalleryHandler;
use App\Application\Event\SfGalleryProcessed;
use App\Application\Service\UuidGenerator;
use App\Domain\Command\ProcessGallery;
use App\Domain\Entity\FileInfo;
use App\Domain\Entity\Gallery;
use App\Domain\Exception\GalleryNotFoundException;
use App\Domain\Repository\GalleryRepository;
use App\Infrastructure\Service\EasySlugger;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ProcessGalleryHandlerTest extends TestCase
{
    private $galleryRepository;
    private $uuidGenerator;
    private $slugger;
    private $eventDispatcher;
    private $galleryFileInfo;

    public function setUp(): void
    {
        $this->galleryRepository = \Phake::mock(GalleryRepository::class);
        $this->uuidGenerator = \Phake::mock(UuidGenerator::class);
        $this->slugger = new EasySlugger();
        $this->eventDispatcher = \Phake::mock(EventDispatcher::class);
        $this->galleryFileInfo = \Phake::mock(FileInfo::class);
    }

    public function test_it_persists_a_new_gallery_and_raise_event()
    {
        Carbon::setTestNow(new \DateTime());
        \Phake::when($this->galleryFileInfo)->getRealPath()->thenReturn('path/to/gallery/Gallery Name');
        \Phake::when($this->galleryRepository)->findByName(\Phake::anyParameters())->thenThrow(new GalleryNotFoundException());
        \Phake::when($this->uuidGenerator)->generateUuid()->thenReturn('xxx');

        $persistedGallery = new Gallery(
            'xxx',
            'path/to/gallery/Gallery Name',
            'Gallery Name',
            'gallery-name'
        );

        $command = new ProcessGallery($this->galleryFileInfo);

        $processGalleryHandler = new ProcessGalleryHandler(
            $this->galleryRepository,
            $this->uuidGenerator,
            $this->slugger,
            $this->eventDispatcher
        );
        $processGalleryHandler->handle($command);

        \Phake::verify($this->galleryRepository)->add($persistedGallery);
        \Phake::verify($this->eventDispatcher, \Phake::times(1))->dispatch(new SfGalleryProcessed($persistedGallery));
    }

    public function test_it_raises_event_for_already_persisted_gallery()
    {
        $persistedGallery = \Phake::mock(Gallery::class);
        \Phake::when($this->galleryRepository)
            ->findByName(\Phake::anyParameters())
            ->thenReturn($persistedGallery);

        $command = new ProcessGallery($this->galleryFileInfo);

        $galleryPersister = new ProcessGalleryHandler(
            $this->galleryRepository,
            $this->uuidGenerator,
            $this->slugger,
            $this->eventDispatcher
        );
        $galleryPersister->handle($command);

        \Phake::verify($this->eventDispatcher, \Phake::times(1))->dispatch(new SfGalleryProcessed($persistedGallery));
    }
}
