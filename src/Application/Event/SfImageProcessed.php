<?php
declare(strict_types = 1);

namespace App\Application\Event;

use App\Domain\Entity\Image;
use App\Domain\Event\ImageProcessed;
use Symfony\Contracts\EventDispatcher\Event;

class SfImageProcessed extends Event implements ImageProcessed
{
    /**
     * @var Image
     */
    private $image;

    /**
     * ImageProcessed constructor.
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }
}
