<?php
declare(strict_types = 1);

namespace App\Application\Subscriber;

use App\Application\Event\SfImageProcessed;
use App\Domain\Command\RetrieveImageGeoDescription;
use App\Domain\Event\ImageProcessed;
use App\Domain\Exception\GeoInfoRetrievingException;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GeoInfoSubscriber implements EventSubscriberInterface
{
    /** @var CommandBus */
    private $commandBus;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(CommandBus $commandBus, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SfImageProcessed::class => 'onProcessImage',
        ];
    }

    public function onProcessImage(ImageProcessed $event): void
    {
        $image = $event->getImage();

        if ($image->needsDescriptionUpdate()) {
            try {
                $retrieveImageGeoDescription = new RetrieveImageGeoDescription($image);
                $this->commandBus->handle($retrieveImageGeoDescription);
                $this->logger->info(
                    'Description for ' . $image->getFilename() . ' of gallery ' .
                    $image->getGallery()->getName() . ' retrieved'
                );
            } catch (GeoInfoRetrievingException $e) {
                $this->logger->error(
                    'Exception retrieving geo description for image ' . $image->getFilename() .
                    ' of gallery ' . $image->getGallery()->getName() . ': ' . $e->getMessage()
                );
            }
        }
    }
}
