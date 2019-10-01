<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\ExifData;
use App\Domain\Entity\Weather;
use PHPUnit\Framework\TestCase;

class WeatherTest extends TestCase
{
    public function test_itReturnsCorrectValues(): void
    {
        $weather = new Weather('description', 1, 2, 3, 4);

        $this->assertEquals('description', $weather->getDescription());
        $this->assertEquals(1.0, $weather->getTemperature());
        $this->assertEquals(2.0, $weather->getHumidity());
        $this->assertEquals(3.0, $weather->getPressure());
        $this->assertEquals(4.0, $weather->getWindSpeed());
        $this->assertFalse($weather->isUndefined());
    }

    public function test_itReturnsUndefined(): void
    {
        $weather = new Weather(null, null, null, null, null);

        $this->assertTrue($weather->isUndefined());
    }
}
