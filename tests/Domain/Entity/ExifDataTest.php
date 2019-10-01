<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\ExifData;
use PHPUnit\Framework\TestCase;

class ExifDataTest extends TestCase
{
    public function test_itReturnsCorrectValues(): void
    {
        $exifData = new ExifData(1, 2, 3, 'test-make', 'test-model', 4, 5, 6, 7, new \DateTime('2077-11-24 12:34:56'));

        $this->assertEquals(1.0, $exifData->getLatitude());
        $this->assertEquals(2.0, $exifData->getLongitude());
        $this->assertEquals(3.0, $exifData->getAltitude());
        $this->assertEquals('test-make', $exifData->getMake());
        $this->assertEquals('test-model', $exifData->getModel());
        $this->assertEquals(new \DateTime('2077-11-24 12:34:56'), $exifData->getTakenAt());
        $this->assertTrue($exifData->hasGeoCoordinates());
    }

    /**
     * @dataProvider getGeoCoordinatesValues
     */
    public function test_itDetectsIfCoordinatesArePresent($latitude, $longitude, $expectedResult): void
    {
        $exifData = new ExifData($latitude, $longitude, null, null, null, null, null, null, null, null);

        $this->assertEquals($expectedResult, $exifData->hasGeoCoordinates());
    }

    public function getGeoCoordinatesValues()
    {
        return [
            [null, null, false],
            [null, 1, false],
            [1, null, false],
            [1, 1, true],
        ];
    }
}
