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

        $gpsCoordinates = explode(',', (string) $exifReadData->getGPS());
        if (count($gpsCoordinates) <= 1) {
            throw new MissingGeoCoordinatesException();
        }

        $gpsAltitude = array_key_exists('GPSAltitude', $exifRawData) ?
            explode('/', (string) $exifRawData['GPSAltitude']) : null;

        return new ExifData(
            (float) $gpsCoordinates[0],
            (float) $gpsCoordinates[1],
            (float) $gpsAltitude ? $gpsAltitude[0] / $gpsAltitude[1] : null,
            isset($exifRawData['Make']) ? $exifRawData['Make'] : null,
            isset($exifRawData['Model']) ? $exifRawData['Model'] : null,
            (string) $exifReadData->getExposureMilliseconds(),
            (string) $exifReadData->getAperture(),
            (string) $exifReadData->getFocalLength(),
            (string) $exifReadData->getIso(),
            $exifReadData->getCreationDate() ?: null
        );
    }
}
