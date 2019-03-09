<?php
declare(strict_types = 1);

namespace App\Application\Subscriber;

use App\Application\Event\SfImageProcessed;
use App\Domain\Command\RetrieveImageWeather;
use App\Domain\Event\ImageProcessed;
use App\Domain\Exception\WeatherRetrievingException;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WeatherSubscriber implements EventSubscriberInterface
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
            SfImageProcessed::NAME => 'onProcessImage',
        ];
    }

    public function onProcessImage(ImageProcessed $event): void
    {
        $image = $event->getImage();

        if ($image->needsWeatherUpdate()) {
            try {
                $retrieveImageWeather = new RetrieveImageWeather($image);
                $this->commandBus->handle($retrieveImageWeather);
                $this->logger->info(
                    'Weather for ' . $image->getFilename() . ' of gallery ' .
                    $image->getGallery()->getName() . ' retrieved'
                );
            } catch (WeatherRetrievingException $e) {
                $this->logger->error(
                    'Exception retrieving weather for image ' . $image->getFilename() .
                    ' of gallery ' . $image->getGallery()->getName() . ': ' . $e->getMessage()
                );
            }
        }
    }
}
