<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\ExifData;
use PHPUnit\Framework\TestCase;

class ExifDataTest extends TestCase
{
    public function test_itReturnsCorrectValues(): void
    {
        $exifData = new ExifData(1, 2, 3, 'test-make', 'test-model', 4, 5, 6, 7, new \DateTimeImmutable('2077-11-24 12:34:56'));

        $this->assertSame(1.0, $exifData->getLatitude());
        $this->assertSame(2.0, $exifData->getLongitude());
        $this->assertSame(3.0, $exifData->getAltitude());
        $this->assertSame('test-make', $exifData->getMake());
        $this->assertSame('test-model', $exifData->getModel());
        $this->assertSame('4', $exifData->getExposure());
        $this->assertSame('5', $exifData->getAperture());
        $this->assertSame('6', $exifData->getFocalLength());
        $this->assertSame('7', $exifData->getISO());
        $this->assertEquals(new \DateTimeImmutable('2077-11-24 12:34:56'), $exifData->getTakenAt());
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

    public function test_setCoordinates()
    {
        $exifData = new ExifData(1, 2, 3, 'test-make', 'test-model', 4, 5, 6, 7, new \DateTimeImmutable('2077-11-24 12:34:56'));
        $exifData->setLatitude(12.3);
        $exifData->setLongitude(45.6);

        $this->assertEquals(12.3, $exifData->getLatitude());
        $this->assertEquals(45.6, $exifData->getLongitude());
    }

    public function test_setTakenAt()
    {
        $exifData = new ExifData(1, 2, 3, 'test-make', 'test-model', 4, 5, 6, 7, new \DateTimeImmutable('2077-11-24 12:34:56'));
        $exifData->setTakenAt(new \DateTimeImmutable('2000-01-01 01:01:01'));

        $this->assertEquals(new \DateTimeImmutable('2000-01-01 01:01:01'), $exifData->getTakenAt());
    }
}
