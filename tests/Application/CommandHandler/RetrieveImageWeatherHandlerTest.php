<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\CommandHandler\RetrieveImageWeatherHandler;
use App\Application\Service\WeatherRetriever;
use App\Domain\Command\RetrieveImageWeather;
use App\Domain\Entity\Image;
use App\Domain\Entity\Weather;
use App\Domain\Repository\ImageRepository;
use PHPUnit\Framework\TestCase;

class RetrieveImageWeatherHandlerTest extends TestCase
{
    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var WeatherRetriever
     */
    private $weatherRetriever;

    public function setUp(): void
    {
        $this->imageRepository = \Phake::mock(ImageRepository::class);
        $this->weatherRetriever = \Phake::mock(WeatherRetriever::class);
    }

    public function test_handle()
    {
        $image = \Phake::mock(Image::class);
        $weather = \Phake::mock(Weather::class);
        \Phake::when($this->weatherRetriever)->retrieveImageWeather($image)->thenReturn($weather);

        $command = new RetrieveImageWeather($image);
        $retrieveImageWeatherHandler = new RetrieveImageWeatherHandler($this->imageRepository, $this->weatherRetriever);
        $retrieveImageWeatherHandler->handle($command);

        \Phake::verify($this->weatherRetriever, \Phake::times(1))->retrieveImageWeather($image);
        \Phake::verify($image, \Phake::times(1))->recordWeather($weather);
        \Phake::verify($this->imageRepository)->add($image);
    }
}
