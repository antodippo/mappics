<?php
declare(strict_types = 1);

namespace App\Application\Subscriber;

use App\Application\Event\SfImageProcessed;
use App\Domain\Command\ResizeImage;
use App\Domain\Event\ImageProcessed;
use App\Domain\Exception\ImageResizingException;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ResizeImageSubscriber implements EventSubscriberInterface
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

        if ($image->needsResizing()) {
            try {
                $resizeImage = new ResizeImage($image);
                $this->commandBus->handle($resizeImage);
                $this->logger->info(
                    'Image ' . $image->getFilename() . ' of gallery ' .
                    $image->getGallery()->getName() . ' resized'
                );
            } catch (ImageResizingException $e) {
                $this->logger->error(
                    'Exception resizing image ' . $image->getFilename() .
                    ' of gallery ' . $image->getGallery()->getName() .
                    ': ' . $e->getMessage()
                );
            }
        }
    }
}
