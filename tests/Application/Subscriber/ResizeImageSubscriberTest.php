<?php

namespace App\Tests\Application\Subscriber;

use App\Application\Event\SfImageProcessed;
use App\Domain\Command\ResizeImage;
use App\Domain\Entity\Image;
use App\Domain\Exception\ImageResizingException;
use App\Application\Subscriber\ResizeImageSubscriber;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ResizeImageSubscriberTest extends TestCase
{

    /** @var CommandBus */
    private $commandBus;

    /** @var LoggerInterface */
    private $logger;

    /** @var ResizeImageSubscriber */
    private $resizeImageSubscriber;

    public function setUp(): void
    {
        $this->commandBus = \Phake::mock(CommandBus::class);
        $this->logger = \Phake::mock(LoggerInterface::class);
        $this->resizeImageSubscriber = new ResizeImageSubscriber($this->commandBus, $this->logger);
    }

    public function test_getSubscribedEvents()
    {
        $expectedSubscribedEvents = [
            SfImageProcessed::class => 'onProcessImage',
        ];
        $subscribedEvents = ResizeImageSubscriber::getSubscribedEvents();
        $this->assertEquals($expectedSubscribedEvents, $subscribedEvents);
    }

    public function test_onProcessImage()
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($image)->needsResizing()->thenReturn(true);
        $event = new SfImageProcessed($image);
        $this->resizeImageSubscriber->onProcessImage($event);

        $command = new ResizeImage($image);
        \Phake::verify($this->commandBus, \Phake::times(1))->handle($command);
    }

    public function test_itLogsException()
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($image)->needsResizing()->thenReturn(true);
        $command = new ResizeImage($image);
        \Phake::when($this->commandBus)->handle($command)->thenThrow(new ImageResizingException());

        $event = new SfImageProcessed($image);
        $this->resizeImageSubscriber->onProcessImage($event);

        \Phake::verify($this->logger, \Phake::times(1))->error(\Phake::anyParameters());
    }
}
