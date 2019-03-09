<?php
declare(strict_types = 1);

namespace App\Application\Event;

use App\Domain\Entity\Gallery;
use App\Domain\Event\GalleryProcessed;
use Symfony\Component\EventDispatcher\Event;

class SfGalleryProcessed extends Event implements GalleryProcessed
{
    const NAME = 'gallery.processed';

    /** @var Gallery */
    private $gallery;

    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    public function getGallery(): Gallery
    {
        return $this->gallery;
    }
}
