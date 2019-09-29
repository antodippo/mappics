<?php

namespace App\Tests\Application\Subscriber;

use App\Application\Event\SfImageProcessed;
use App\Application\Subscriber\GeoInfoSubscriber;
use App\Domain\Command\RetrieveImageGeoDescription;
use App\Domain\Entity\Image;
use App\Domain\Exception\GeoInfoRetrievingException;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GeoInfoSubscriberTest extends TestCase
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
     * @var GeoInfoSubscriber
     */
    private $geoInfoSubscriber;

    public function setUp()
    {
        $this->commandBus = \Phake::mock(CommandBus::class);
        $this->logger = \Phake::mock(LoggerInterface::class);
        $this->geoInfoSubscriber = new GeoInfoSubscriber($this->commandBus, $this->logger);
    }

    public function test_getSubscribedEvents()
    {
        $expectedSubscribedEvents = [
            SfImageProcessed::class => 'onProcessImage',
        ];
        $subscribedEvents = GeoInfoSubscriber::getSubscribedEvents();
        $this->assertEquals($expectedSubscribedEvents, $subscribedEvents);
    }

    public function test_onProcessImage()
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($image)->needsDescriptionUpdate()->thenReturn(true);
        $event = new SfImageProcessed($image);
        $this->geoInfoSubscriber->onProcessImage($event);

        $command = new RetrieveImageGeoDescription($image);
        \Phake::verify($this->commandBus, \Phake::times(1))->handle($command);
    }

    public function test_itLogsException()
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($image)->needsDescriptionUpdate()->thenReturn(true);
        $command = new RetrieveImageGeoDescription($image);
        \Phake::when($this->commandBus)->handle($command)->thenThrow(new GeoInfoRetrievingException());

        $event = new SfImageProcessed($image);
        $this->geoInfoSubscriber->onProcessImage($event);

        \Phake::verify($this->logger, \Phake::times(1))->error(\Phake::anyParameters());
    }
}
