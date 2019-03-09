<?php

namespace App\Tests\Application\Subscriber;

use App\Application\Event\SfImageProcessed;
use App\Application\Subscriber\WeatherSubscriber;
use App\Domain\Command\RetrieveImageWeather;
use App\Domain\Entity\Image;
use App\Domain\Exception\WeatherRetrievingException;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class WeatherSubscriberTest extends TestCase
{

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var WeatherSubscriber
     */
    private $weatherSubscriber;

    public function setUp()
    {
        $this->commandBus = \Phake::mock(CommandBus::class);
        $this->logger = \Phake::mock(LoggerInterface::class);
        $this->weatherSubscriber = new WeatherSubscriber($this->commandBus, $this->logger);
    }

    public function test_getSubscribedEvents()
    {
        $expectedSubscribedEvents = [
            SfImageProcessed::NAME => 'onProcessImage',
        ];
        $subscribedEvents = WeatherSubscriber::getSubscribedEvents();
        $this->assertEquals($expectedSubscribedEvents, $subscribedEvents);
    }

    public function test_onProcessImage()
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($image)->needsWeatherUpdate()->thenReturn(true);
        $event = new SfImageProcessed($image);
        $this->weatherSubscriber->onProcessImage($event);

        $command = new RetrieveImageWeather($image);
        \Phake::verify($this->commandBus, \Phake::times(1))->handle($command);
    }

    public function test_itLogsException()
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($image)->needsWeatherUpdate()->thenReturn(true);
        $command = new RetrieveImageWeather($image);
        \Phake::when($this->commandBus)->handle($command)->thenThrow(new WeatherRetrievingException());

        $event = new SfImageProcessed($image);
        $this->weatherSubscriber->onProcessImage($event);

        \Phake::verify($this->logger, \Phake::times(1))->error(\Phake::anyParameters());
    }
}
