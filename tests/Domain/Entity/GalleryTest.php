<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\ExifData;
use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Entity\GeoCoordinates;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase
{

    /**
     * @dataProvider getImageExifCoordinates
     */
    public function test_itGetsImagesMeanCoordinates(array $coordinates, GeoCoordinates $expectedMeanCoordinates)
    {
        $gallery = new Gallery('fake-id', 'fake/path', 'Fake Gallery', 'fake-gallery');

        foreach ($coordinates as $imageGeoCoordinates) {
            $exifData = new ExifData(
                $imageGeoCoordinates->latitude,
                $imageGeoCoordinates->longitude,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null
            );
            $image = new Image('fake-id', 'fake-name', $gallery, $exifData);
            $gallery->addImage($image);
        }

        $this->assertEquals($expectedMeanCoordinates, $gallery->getImagesMeanCoordinates());
    }

    public function getImageExifCoordinates()
    {
        return [
            [
                [
                    new GeoCoordinates(0.0, 0.0),
                    new GeoCoordinates(0.0, 10.0),
                    new GeoCoordinates(10.0, 0.0),
                    new GeoCoordinates(10.0, 10.0)
                ],
                new GeoCoordinates(5.0, 5.0)
            ],
            [
                [
                    new GeoCoordinates(0.0, 0.0),
                    new GeoCoordinates(0.0, 10.0),
                    new GeoCoordinates(10.0, 0.0),
                    new GeoCoordinates(20.0, 10.0)
                ],
                new GeoCoordinates(10.0, 5.0)
            ],
            [
                [
                    new GeoCoordinates(-10.0, -10.0),
                    new GeoCoordinates(-10.0, -30.0),
                    new GeoCoordinates(-30.0, -20.0),
                    new GeoCoordinates(-15.0, -15.0)
                ],
                new GeoCoordinates(-20.0, -20.0)
            ]
        ];
    }

    public function test_itReturnsOrderedImagesArray()
    {
        Carbon::setTestNow(new \DateTime());

        $gallery = new Gallery('fake-id', 'fake/path', 'Fake Gallery', 'fake-gallery');

        $exifData = new ExifData(0.0, 0.0, null, null, null, null, null, null, null, null);
        $imageAAA = new Image('AAA', 'AAA.JPG', $gallery, $exifData);
        $imageBBB = new Image('BBB', 'BBB.JPG', $gallery, $exifData);
        $imageCCC = new Image('CCC', 'CCC.JPG', $gallery, $exifData);

        $gallery->addImage($imageBBB);
        $gallery->addImage($imageCCC);
        $gallery->addImage($imageAAA);

        $expectedImagesArrayCollection = new ArrayCollection([$imageAAA, $imageBBB, $imageCCC]);
        $this->assertEquals($expectedImagesArrayCollection, $gallery->getImages());

        Carbon::setTestNow();
    }
}
