<?php

namespace App\Tests\Infrastructure\Console;

use App\Application\Event\SfImageProcessed;
use App\Domain\Entity\ExifData;
use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Infrastructure\Console\AdvanceSingleGalleryProgressSubscriber;
use App\Infrastructure\Console\ProgressBarHelper;
use PHPUnit\Framework\TestCase;

class AdvanceSingleGalleryProgressSubscriberTest extends TestCase
{
    public function test_getSubscribedEvents()
    {
        $expectedSubscribedEvents = [
            SfImageProcessed::class => 'advanceSingleGalleryProgress',
        ];
        $subscribedEvents = AdvanceSingleGalleryProgressSubscriber::getSubscribedEvents();
        $this->assertEquals($expectedSubscribedEvents, $subscribedEvents);
    }

    public function testAdvanceGalleriesProgress()
    {
        $progressBarHelper = \Phake::mock(ProgressBarHelper::class);
        $image = new Image(
            'image-id',
            'image-filename',
            new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery'),
            new ExifData(1.0, 1.0, null, null, null, null, null, null, null, null)
        );
        $imageProcessed = new SfImageProcessed($image);

        $advanceSingleGalleryProgressSubscriber = new AdvanceSingleGalleryProgressSubscriber($progressBarHelper);
        $advanceSingleGalleryProgressSubscriber->advanceSingleGalleryProgress($imageProcessed);

        \Phake::verify($progressBarHelper)->advanceSingleGalleryProgressBar('Gallery');
    }
}
