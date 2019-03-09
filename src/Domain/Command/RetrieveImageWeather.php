<?php
declare(strict_types = 1);

namespace App\Domain\Command;

use App\Domain\Entity\Image;

class RetrieveImageWeather
{

    /**
     * @var Image
     */
    private $image;

    /**
     * RetrieveImageGeoDescription constructor.
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
