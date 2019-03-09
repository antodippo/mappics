<?php

namespace App\Tests\Infrastructure\Service;

use App\Application\Service\WeatherRetriever;
use App\Domain\Entity\Image;
use App\Domain\Entity\Weather;
use App\Domain\Exception\WeatherRetrievingException;

class StubWeatherRetriever implements WeatherRetriever
{
    public function retrieveImageWeather(Image $image): Weather
    {
        if (! $image->hasExifGeoCoordinates()) {
            throw new WeatherRetrievingException('Missing Exif geo coordinates');
        }

        return new Weather(
            'Sunny day!',
            '20',
            '55',
            '1024',
            '12'
        );
    }
}
