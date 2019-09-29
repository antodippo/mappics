<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\CommandHandler\ProcessImageHandler;
use App\Application\Event\SfImageProcessed;
use App\Application\Service\ExifReader;
use App\Application\Service\UuidGenerator;
use App\Domain\Command\ProcessImage;
use App\Domain\Entity\ExifData;
use App\Domain\Entity\FileInfo;
use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Exception\ImageNotFoundException;
use App\Domain\Exception\MissingGeoCoordinatesException;
use App\Domain\Repository\ImageRepository;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProcessImageHandlerTest extends TestCase
{
    private $imageRepository;
    private $uuidGenerator;
    private $eventDispatcher;
    private $exifReader;
    private $logger;
    private $imageFileInfo;
    private $gallery;
    private $exifData;

    public function setUp()
    {
        $this->imageRepository = \Phake::mock(ImageRepository::class);

        $this->uuidGenerator = \Phake::mock(UuidGenerator::class);
        \Phake::when($this->uuidGenerator)->generateUuid()->thenReturn('xxx');

        $this->eventDispatcher = \Phake::mock(EventDispatcherInterface::class);

        $this->imageFileInfo = \Phake::mock(FileInfo::class);
        \Phake::when($this->imageFileInfo)->getFilename()->thenReturn('image_name');
        \Phake::when($this->imageFileInfo)->getMTime()->thenReturn(1525192074);

        $this->exifData = \Phake::mock(ExifData::class);
        $this->exifReader = \Phake::mock(ExifReader::class);
        \Phake::when($this->exifReader)->getExifData($this->imageFileInfo)->thenReturn($this->exifData);

        $this->gallery = \Phake::mock(Gallery::class);

        $this->logger = \Phake::mock(LoggerInterface::class);
    }

    public function test_it_persist_a_new_image_and_raise_event()
    {
        Carbon::setTestNow(new \DateTime());
        \Phake::when($this->imageRepository)->findByFilenameAndGallery('image_name', $this->gallery)->thenThrow(new ImageNotFoundException());

        $command = new ProcessImage($this->imageFileInfo, $this->gallery);

        $processImageHandler = new ProcessImageHandler(
            $this->imageRepository,
            $this->uuidGenerator,
            $this->eventDispatcher,
            $this->exifReader,
            $this->logger
        );
        $processImageHandler->handle($command);

        $image = new Image(
            'xxx',
            'image_name',
            $this->gallery,
            $this->exifData
        );

        \Phake::verify($this->imageRepository, \Phake::times(1))->add($image);
        \Phake::verify($this->eventDispatcher, \Phake::times(1))->dispatch(new SfImageProcessed($image));
    }

    public function test_it_raises_event_for_already_persisted_image()
    {
        $image = new Image(
            'xxx',
            'image_name',
            $this->gallery,
            $this->exifData
        );

        \Phake::when($this->imageRepository)->findByFilenameAndGallery('image_name', $this->gallery)->thenReturn($image);

        $command = new ProcessImage($this->imageFileInfo, $this->gallery);

        $processImageHandler = new ProcessImageHandler(
            $this->imageRepository,
            $this->uuidGenerator,
            $this->eventDispatcher,
            $this->exifReader,
            $this->logger
        );
        $processImageHandler->handle($command);

        \Phake::verify($this->imageRepository, \Phake::times(0))->add($image);
        \Phake::verify($this->eventDispatcher, \Phake::times(1))->dispatch(new SfImageProcessed($image));
    }

    public function test_it_skip_an_image_without_geo_coordinates()
    {
        Carbon::setTestNow(new \DateTime());
        \Phake::when($this->imageRepository)->findByFilenameAndGallery('image_name', $this->gallery)->thenThrow(new ImageNotFoundException());
        \Phake::when($this->exifReader)->getExifData($this->imageFileInfo)->thenThrow(new MissingGeoCoordinatesException());

        $command = new ProcessImage($this->imageFileInfo, $this->gallery);

        $processImageHandler = new ProcessImageHandler(
            $this->imageRepository,
            $this->uuidGenerator,
            $this->eventDispatcher,
            $this->exifReader,
            $this->logger
        );
        $processImageHandler->handle($command);

        \Phake::verify($this->imageRepository, \Phake::times(0))->add(\Phake::anyParameters());
        \Phake::verify($this->eventDispatcher, \Phake::times(0))->dispatch(\Phake::anyParameters());
    }
}
