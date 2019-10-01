<?php
declare(strict_types = 1);

namespace App\Infrastructure\Service;

use App\Application\Service\WeatherRetriever;
use App\Domain\Entity\Image;
use App\Domain\Entity\Weather;
use App\Domain\Exception\WeatherRetrievingException;
use GuzzleHttp\ClientInterface;

class DarkSkyWeatherRetriever implements WeatherRetriever
{
    const DARKSKY_ENDPOINT = 'https://api.darksky.net/forecast/';

    /** @var ClientInterface */
    private $client;

    /** @var string */
    private $apiKey;

    public function __construct(ClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function retrieveImageWeather(Image $image): Weather
    {
        $timestamp = $image->getExifData()->getTakenAt() instanceof \DateTime ?
            $image->getExifData()->getTakenAt()->getTimestamp() : null;

        $url = self::DARKSKY_ENDPOINT .
            $this->apiKey . '/' .
            $image->getExifData()->getLatitude() . ',' .
            $image->getExifData()->getLongitude() . ',' .
            $timestamp . '?units=si';

        $response = $this->client->request('GET', $url);
        $responseBody = json_decode((string) $response->getBody());

        if ($response->getStatusCode() == 200 && isset($responseBody->currently->summary)) {
            $weather = new Weather(
                $responseBody->currently->summary,
                isset($responseBody->currently->temperature) ? $responseBody->currently->temperature : null,
                isset($responseBody->currently->humidity) ? $responseBody->currently->humidity : null,
                isset($responseBody->currently->pressure) ? $responseBody->currently->pressure : null,
                isset($responseBody->currently->windSpeed) ? $responseBody->currently->windSpeed : null
            );

            return $weather;
        } else {
            throw new WeatherRetrievingException('URL: ' . $url);
        }
    }
}
