<?php

namespace App\Tests\Application\Subscriber;

use App\Application\Event\SfGalleryProcessed;
use App\Application\Subscriber\ProcessImagesSubscriber;
use App\Application\Service\FileFinder;
use App\Domain\Entity\FileInfo;
use App\Domain\Entity\Gallery;
use App\Domain\Command\ProcessImage;
use ArrayIterator;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProcessImagesSubscriberTest extends TestCase
{
    private $commandBus;
    private $logger;
    private $gallery;

    public function setUp(): void
    {
        $this->commandBus = \Phake::mock(CommandBus::class);
        $this->logger = \Phake::mock(LoggerInterface::class);

        $this->gallery = \Phake::mock(Gallery::class);
        \Phake::when($this->gallery)->getPath()->thenReturn('path/to/gallery');
    }

    public function test_it_gets_subscribed_events()
    {
        $subscribedEvents = [
            SfGalleryProcessed::class => 'processGalleryImages'
        ];
        $this->assertEquals($subscribedEvents, ProcessImagesSubscriber::getSubscribedEvents());
    }

    public function test_it_processes_gallery_images()
    {
        $fileFinder = \Phake::mock(FileFinder::class);
        \Phake::when($fileFinder)->findImagesInPath('path/to/gallery')->thenReturn(
            [
                $imageFileInfo1 = \Phake::mock(FileInfo::class),
                $imageFileInfo2 = \Phake::mock(FileInfo::class),
            ]
        );

        $processImageSubscriber = new ProcessImagesSubscriber($fileFinder, $this->commandBus, $this->logger);
        $processImageSubscriber->processGalleryImages(new SfGalleryProcessed($this->gallery));

        \Phake::verify($this->commandBus, \Phake::times(1))->handle(new ProcessImage($imageFileInfo1, $this->gallery));
        \Phake::verify($this->commandBus, \Phake::times(1))->handle(new ProcessImage($imageFileInfo2, $this->gallery));
    }

    public function test_itLogsException()
    {
        $fileFinder = \Phake::mock(FileFinder::class);
        \Phake::when($fileFinder)->findImagesInPath('path/to/gallery')->thenReturn(
            [
                $imageFileInfo1 = \Phake::mock(FileInfo::class),
                $imageFileInfo2 = \Phake::mock(FileInfo::class),
            ]
        );

        $command = new ProcessImage($imageFileInfo1, $this->gallery);
        \Phake::when($this->commandBus)->handle($command)->thenThrow(new \Exception());

        $processImageSubscriber = new ProcessImagesSubscriber($fileFinder, $this->commandBus, $this->logger);
        $processImageSubscriber->processGalleryImages(new SfGalleryProcessed($this->gallery));

        \Phake::verify($this->logger, \Phake::times(1))->error(\Phake::anyParameters());
    }
}
