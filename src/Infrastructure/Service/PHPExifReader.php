<?php
declare(strict_types = 1);

namespace App\Infrastructure\Service;

use App\Application\Service\ExifReader;
use App\Domain\Entity\ExifData;
use App\Domain\Entity\FileInfo;
use App\Domain\Exception\MissingGeoCoordinatesException;
use PHPExif\Reader\Reader;

class PHPExifReader implements ExifReader
{
    public function getExifData(FileInfo $imageFile): ExifData
    {
        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $exifReadData = $reader->read($imageFile->getRealPath());
        $exifRawData = $exifReadData->getRawData();

        $gpsCoordinates = $exifReadData->getGPS();
        if (! is_array($gpsCoordinates)) {
            $gpsCoordinates = explode(',', (string) $gpsCoordinates);
        }
        if (count($gpsCoordinates) > 1) {
            $latitude = (float) $gpsCoordinates[0];
            $longitude = (float) $gpsCoordinates[1];
        } else {
            $latitude = $longitude = null;
        }

        $gpsAltitude = array_key_exists('GPSAltitude', $exifRawData) ?
            explode('/', (string) $exifRawData['GPSAltitude']) : null;

        return new ExifData(
            $latitude,
            $longitude,
            $gpsAltitude ? (float) ($gpsAltitude[0] / $gpsAltitude[1]) : null,
            isset($exifRawData['Make']) ? $exifRawData['Make'] : null,
            isset($exifRawData['Model']) ? $exifRawData['Model'] : null,
            (string) $exifReadData->getExposureMilliseconds(),
            (string) $exifReadData->getAperture(),
            (string) $exifReadData->getFocalLength(),
            (string) $exifReadData->getIso(),
            ($exifReadData->getCreationDate() instanceof \DateTime) ? $exifReadData->getCreationDate() : null
        );
    }
}
