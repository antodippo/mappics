<?php
declare(strict_types = 1);

namespace App\Application\CommandHandler;

use App\Application\Event\SfImageProcessed;
use App\Application\Service\UuidGenerator;
use App\Application\Service\ExifReader;
use App\Domain\Command\ProcessImage;
use App\Domain\Entity\Image;
use App\Domain\Exception\ImageNotFoundException;
use App\Domain\Exception\MissingGeoCoordinatesException;
use App\Domain\Repository\ImageRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProcessImageHandler
{

    /** @var ImageRepository */
    private $imageRepository;

    /** @var UuidGenerator */
    private $uuidGenerator;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ExifReader */
    private $exifReader;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ProcessGalleryHandler constructor.
     * @param ImageRepository $imageRepository
     * @param UuidGenerator $uuidGenerator
     * @param EventDispatcherInterface $eventDispatcher
     * @param ExifReader $exifReader
     * @param LoggerInterface $logger
     */
    public function __construct(
        ImageRepository $imageRepository,
        UuidGenerator $uuidGenerator,
        EventDispatcherInterface $eventDispatcher,
        ExifReader $exifReader,
        LoggerInterface $logger
    ) {
        $this->uuidGenerator = $uuidGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->exifReader = $exifReader;
        $this->imageRepository = $imageRepository;
        $this->logger = $logger;
    }

    public function handle(ProcessImage $command)
    {
        $imageFileInfo = $command->getImageFileInfo();
        $gallery = $command->getGallery();

        $newImage = false;
        try {
            $image = $this->imageRepository->findByFilenameAndGallery($imageFileInfo->getFilename(), $gallery);
        } catch (ImageNotFoundException $e) {
            $newImage = true;
            $this->logger->info('New image ' . $imageFileInfo->getFilename() . ' in gallery ' . $gallery->getName());
        }

        if ($newImage) {
            try {
                $image = new Image(
                    $this->uuidGenerator->generateUuid(),
                    $imageFileInfo->getFilename(),
                    $gallery,
                    $this->exifReader->getExifData($imageFileInfo)
                );
                $this->imageRepository->add($image);
            } catch (MissingGeoCoordinatesException $e) {
                $this->logger->warning(
                    'Image ' . $imageFileInfo->getFilename() .
                    ' of gallery ' . $gallery->getName() . ' has not geo coordinates'
                );

                return;
            }
        }

        $imageProcessed = new SfImageProcessed($image);
        $this->eventDispatcher->dispatch(SfImageProcessed::NAME, $imageProcessed);
    }
}
