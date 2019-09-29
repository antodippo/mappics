<?php
declare(strict_types = 1);

namespace App\Infrastructure\Console;

use App\Application\Event\SfGalleryProcessed;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdvanceGalleriesProgressSubscriber implements EventSubscriberInterface
{
    /** @var ProgressBarHelper */
    private $progressBarHelper;

    public function __construct(ProgressBarHelper $progressBarHelper)
    {
        $this->progressBarHelper = $progressBarHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SfGalleryProcessed::class => 'advanceGalleriesProgress',
        ];
    }

    public function advanceGalleriesProgress(SfGalleryProcessed $galleryProcessed): void
    {
        $this->progressBarHelper->advanceGalleriesProgressBar();
        $this->progressBarHelper->endSingleGalleryProgressBar($galleryProcessed->getGallery()->getName());
    }
}
