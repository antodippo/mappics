<?php
declare(strict_types = 1);

namespace App\Application\Service;

use App\Domain\Entity\Image;
use App\Domain\Entity\Weather;

interface WeatherRetriever
{
    public function retrieveImageWeather(Image $image): Weather;
}
