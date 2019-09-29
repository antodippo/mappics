<?php

namespace App\Tests\Infrastructure\Console;

use App\Application\Event\SfGalleryProcessed;
use App\Domain\Entity\Gallery;
use App\Infrastructure\Console\AdvanceGalleriesProgressSubscriber;
use App\Infrastructure\Console\ProgressBarHelper;
use PHPUnit\Framework\TestCase;

class AdvanceGalleriesProgressSubscriberTest extends TestCase
{
    public function test_getSubscribedEvents()
    {
        $expectedSubscribedEvents = [
            SfGalleryProcessed::class => 'advanceGalleriesProgress',
        ];
        $subscribedEvents = AdvanceGalleriesProgressSubscriber::getSubscribedEvents();
        $this->assertEquals($expectedSubscribedEvents, $subscribedEvents);
    }

    public function testAdvanceGalleriesProgress()
    {
        $progressBarHelper = \Phake::mock(ProgressBarHelper::class);
        $gallery = new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery');
        $galleryProcessed = new SfGalleryProcessed($gallery);

        $advanceGalleriesProgressSubscriber = new AdvanceGalleriesProgressSubscriber($progressBarHelper);
        $advanceGalleriesProgressSubscriber->advanceGalleriesProgress($galleryProcessed);

        \Phake::verify($progressBarHelper)->advanceGalleriesProgressBar();
        \Phake::verify($progressBarHelper)->endSingleGalleryProgressBar('Gallery');
    }
}
