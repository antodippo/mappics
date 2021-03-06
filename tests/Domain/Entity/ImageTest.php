<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\ExifData;
use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Entity\Weather;
use App\Domain\Entity\GeoDescription;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    /**
     * @dataProvider getResizingInfo
     */
    public function test_needsResizing(
        string $resizedFilename,
        string $thumbnailFilename,
        bool $expectedResult
    ) {
        $image = new Image(
            'image-id',
            'image-filename',
            new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery'),
            new ExifData(1.0, 1.0, null, null, null, null, null, null, null, null)
        );
        $image->updateResizedImagesFilename($resizedFilename, $thumbnailFilename);

        $this->assertEquals($image->needsResizing(), $expectedResult);
    }

    public function getResizingInfo()
    {
        return [
           [ 'some-filename', 'some-filename', false ],
           [ '', 'some-filename', true ],
           [ 'some-filename', '', true ],
           [ '', '', true ],
        ];
    }

    /**
     * @dataProvider getDescriptionInfo
     */
    public function test_needsDescriptionUpdate(
        ?string $shortDescription,
        ?string $longDescription,
        ?float $latitude,
        ?float $longitude,
        bool $expectedResult
    ) {
        $image = new Image(
            'image-id',
            'image-filename',
            new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery'),
            new ExifData($latitude, $longitude, null, null, null, null, null, null, null, null)
        );
        $image->updateGeoDescription(new GeoDescription($shortDescription, $longDescription));

        $this->assertEquals($image->needsDescriptionUpdate(), $expectedResult);
    }

    public function getDescriptionInfo()
    {
        return [
            [ 'some-short-description', 'some-long-description', 1.0, 1.0, false ],
            [ null, 'some-long-description', 1.0, 1.0, true ],
            [ 'some-short-description', null, 1.0, 1.0, true ],
            [ null, null, 1.0, 1.0, true ],
            [ null, null, null, null, false ],
        ];
    }

    /**
     * @dataProvider getWeatherInfo
     */
    public function test_needsWeatherUpdate(
        ?Weather $weather,
        ?float $latitude,
        ?float $longitude,
        ?\DateTimeImmutable $takenAt,
        bool $expectedResult
    ) {
        $image = new Image(
            'image-id',
            'image-filename',
            new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery'),
            new ExifData($latitude, $longitude, null, null, null, null, null, null, null, $takenAt)
        );
        if ($weather) {
            $image->recordWeather($weather);
        }

        $this->assertEquals($image->needsWeatherUpdate(), $expectedResult);
    }

    public function getWeatherInfo()
    {
        return [
            [ new Weather('some-weather', null, null, null, null), 1.0, 1.0, new CarbonImmutable(), false ],
            [ null, 1.0, 1.0, new CarbonImmutable(), true ],
            [ null, null, null, new CarbonImmutable(), false ],
            [ null, 1.0, 1.0, null, false ]
        ];
    }

    /**
     * @dataProvider getCoordinates
     */
    public function test_hasExifGeoCoordinates(
        ?float $latitude,
        ?float $longitude,
        bool $expectedResult
    ) {
        $image = new Image(
            'image-id',
            'image-filename',
            new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery'),
            new ExifData($latitude, $longitude, null, null, null, null, null, null, null, null)
        );

        $this->assertEquals($image->hasExifGeoCoordinates(), $expectedResult);
    }

    public function getCoordinates()
    {
        return [
            [ 1.0, 1.0, true ],
            [ null, 1.0, false ],
            [ 1.0, null, false ],
            [ null, null, false ]
        ];
    }

    public function test_setDescriptions()
    {
        $image = new Image(
            'image-id',
            'image-filename',
            new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery'),
            new ExifData(12, 34, null, null, null, null, null, null, null, null)
        );
        $image->setDescription('New description');
        $image->setLongDescription('New long description');

        $this->assertEquals('New description', $image->getDescription());
        $this->assertEquals('New long description', $image->getLongDescription());
    }
}
