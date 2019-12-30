<?php
declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Application\Service\ExifReader;
use App\Domain\Entity\ExifData;
use App\Domain\Entity\FileInfo;
use ExifReader\Reader;

class AntoDippoExifReader implements ExifReader
{
    /** @var Reader */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getExifData(FileInfo $imageFile): ExifData
    {
        $exifData = $this->reader->read($imageFile->getRealPath());

        return new ExifData(
            $exifData->getGeoLocation()->getLatitude()->getFloat(),
            $exifData->getGeoLocation()->getLongitude()->getFloat(),
            $exifData->getGeoLocation()->getAltitude()->getFloat(),
            (string) $exifData->getCameraData()->getMaker(),
            (string) $exifData->getCameraData()->getModel(),
            (string) $exifData->getCameraData()->getExposureTime(),
            (string) $exifData->getCameraData()->getAperture(),
            (string) $exifData->getCameraData()->getFocalLength(),
            (string) $exifData->getCameraData()->getISOSpeed(),
            $exifData->getFileData()->getTakenDate()->getDateTime()
        );
    }
}