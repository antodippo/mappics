<?php

namespace App\Tests\Domain\Model;

use App\Domain\Entity\GeoCoordinates;
use PHPUnit\Framework\TestCase;

class GeoCoordinatesTest extends TestCase
{
    public function test_itHandlesGeoCoordinates()
    {
        $latitude = 12.3;
        $longitude = -12.3;
        $geoCoordinates = new GeoCoordinates($latitude, $longitude);

        $this->assertEquals(12.3, $geoCoordinates->latitude);
        $this->assertEquals(-12.3, $geoCoordinates->longitude);
    }

    /**
     * @dataProvider getLatitudesOutOfRange
     */
    public function test_itThrowsExeptionForLatitudeOutOfRange($latitude)
    {
        $this->expectException('\InvalidArgumentException');
        $geoCoordinates = new GeoCoordinates($latitude, 0.0);
    }

    public function getLatitudesOutOfRange()
    {
        return [
          [ -91.123 ],
          [ 91.123 ]
        ];
    }

    /**
     * @dataProvider getLongitudesOutOfRange
     */
    public function test_itThrowsExeptionForLongitudesOutOfRange($longitude)
    {
        $this->expectException('\InvalidArgumentException');
        $geoCoordinates = new GeoCoordinates(0.0, $longitude);
    }

    public function getLongitudesOutOfRange()
    {
        return [
            [ -181.123 ],
            [ 181.123 ]
        ];
    }
}
