<?php

namespace App\Tests\Infrastructure\Service;

use App\Application\Service\GeoInfoRetriever;
use App\Domain\Entity\Image;
use App\Domain\Exception\GeoInfoRetrievingException;
use App\Domain\Entity\GeoDescription;

class StubGeoInfoRetriever implements GeoInfoRetriever
{
    public function retrieveImageGeoInfo(Image $image): GeoDescription
    {
        if (! $image->hasExifGeoCoordinates()) {
            throw new GeoInfoRetrievingException('Missing Exif geo coordinates');
        }

        return new GeoDescription(
            'Colosseum, Rome',
            'The beautiful Colosseum, in the beautiful city of Rome'
        );
    }
}
