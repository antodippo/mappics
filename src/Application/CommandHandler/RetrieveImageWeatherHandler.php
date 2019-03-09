<?php
declare(strict_types = 1);

namespace App\Application\CommandHandler;

use App\Application\Service\WeatherRetriever;
use App\Domain\Command\RetrieveImageWeather;
use App\Domain\Repository\ImageRepository;

class RetrieveImageWeatherHandler
{

    /**
     * @var WeatherRetriever
     */
    private $weatherRetriever;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * WeatherSubscriber constructor.
     * @param WeatherRetriever $weatherRetriever
     * @param ImageRepository $imageRepository
     */
    public function __construct(ImageRepository $imageRepository, WeatherRetriever $weatherRetriever)
    {
        $this->imageRepository = $imageRepository;
        $this->weatherRetriever = $weatherRetriever;
    }

    public function handle(RetrieveImageWeather $retrieveImageWeather)
    {
        $image = $retrieveImageWeather->getImage();
        $weather = $this->weatherRetriever->retrieveImageWeather($image);
        $image->recordWeather($weather);
        $this->imageRepository->add($image);
    }
}
