<?php
declare(strict_types = 1);

namespace App\Application\Subscriber;

use App\Application\Event\SfGalleryProcessed;
use App\Application\Service\FileFinder;
use App\Domain\Command\ProcessImage;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProcessImagesSubscriber implements EventSubscriberInterface
{
    /** @var FileFinder */
    private $fileFinder;

    /** @var CommandBus */
    private $commandBus;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(FileFinder $fileFinder, CommandBus $commandBus, LoggerInterface $logger)
    {
        $this->fileFinder = $fileFinder;
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SfGalleryProcessed::NAME => 'processGalleryImages',
        ];
    }

    public function processGalleryImages(SfGalleryProcessed $galleryProcessed): void
    {
        $gallery = $galleryProcessed->getGallery();
        $images = $this->fileFinder->findImagesInPath($gallery->getPath());

        foreach ($images as $imageFileInfo) {
            try {
                $processImage = new ProcessImage($imageFileInfo, $gallery);
                $this->commandBus->handle($processImage);
                $this->logger->info('Image ' . $imageFileInfo->getFilename() . ' of gallery ' . $gallery->getName() . ' processed');
            } catch (\Exception $e) {
                $this->logger->error(
                    'Exception processing image ' . $imageFileInfo->getFilename() .
                    ' of gallery ' . $gallery->getName() . ': ' . $e->getMessage()
                );
            }
        }
    }
}
