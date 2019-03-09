<?php
declare(strict_types = 1);

namespace App\Application\Service;

use App\Domain\Entity\Image;
use App\Domain\Entity\GeoDescription;

interface GeoInfoRetriever
{
    public function retrieveImageGeoInfo(Image $image): GeoDescription;
}
