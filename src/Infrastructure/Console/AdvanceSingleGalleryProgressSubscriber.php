<?php
declare(strict_types = 1);

namespace App\Infrastructure\Console;

use App\Application\Event\SfImageProcessed;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdvanceSingleGalleryProgressSubscriber implements EventSubscriberInterface
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
            SfImageProcessed::NAME => 'advanceSingleGalleryProgress',
        ];
    }

    public function advanceSingleGalleryProgress(SfImageProcessed $imageProcessed): void
    {
        $this->progressBarHelper->advanceSingleGalleryProgressBar($imageProcessed->getImage()->getGallery()->getName());
    }
}
