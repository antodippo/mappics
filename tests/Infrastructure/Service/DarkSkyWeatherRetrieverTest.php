<?php

namespace App\Tests\Infrastructure\Service;

use App\Domain\Entity\ExifData;
use App\Domain\Entity\Image;
use App\Domain\Entity\Weather;
use App\Infrastructure\Service\DarkSkyWeatherRetriever;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class DarkSkyWeatherRetrieverTest extends TestCase
{

    /**
     * @var ClientInterface
     */
    private $httpClient;

    public function setUp(): void
    {
        $this->httpClient = \Phake::mock(ClientInterface::class);
    }

    public function test_retrieveImageWeather()
    {
        $exifData = \Phake::mock(ExifData::class);
        \Phake::when($exifData)->getLatitude()->thenReturn('111');
        \Phake::when($exifData)->getLongitude()->thenReturn('222');
        \Phake::when($exifData)->getTakenAt()->thenReturn(new \DateTimeImmutable('2018-01-01 00:00:00'));

        $image = \Phake::mock(Image::class);
        \Phake::when($image)->getExifData()->thenReturn($exifData);

        $jsonBody = '
        {
            "currently": {
                "summary": "Mostly Cloudy",
                "temperature": 8.1,
                "humidity": 0.48,
                "pressure": 1018.21,
                "windSpeed": 1.52
            }
        }';

        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(200);
        \Phake::when($response)->getBody()->thenReturn($jsonBody);

        \Phake::when($this->httpClient)
            ->request('GET', 'https://api.darksky.net/forecast/123abc/111,222,1514764800?units=si')
            ->thenReturn($response);

        $darkSkyWeatherRetriever = new DarkSkyWeatherRetriever($this->httpClient, '123abc');
        $weather = $darkSkyWeatherRetriever->retrieveImageWeather($image);

        \Phake::verify($this->httpClient, \Phake::times(1))
            ->request('GET', 'https://api.darksky.net/forecast/123abc/111,222,1514764800?units=si');

        $this->assertEquals(
            new Weather(
                'Mostly Cloudy',
                '8.1',
                '0.48',
                '1018.21',
                '1.52'
            ),
            $weather
        );
    }

    public function test_retrieveImageWeather_withErrorStatusCode()
    {
        $this->expectException('\App\Domain\Exception\WeatherRetrievingException');

        $exifData = \Phake::mock(ExifData::class);
        \Phake::when($exifData)->getLatitude()->thenReturn('111');
        \Phake::when($exifData)->getLongitude()->thenReturn('222');
        \Phake::when($exifData)->getTakenAt()->thenReturn(new \DateTimeImmutable('2018-01-01 00:00:00'));

        $image = \Phake::mock(Image::class);
        \Phake::when($image)->getExifData()->thenReturn($exifData);

        $jsonBody = '{
            "code": 500,
            "error": "Error description"
        }';

        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(500);
        \Phake::when($response)->getBody()->thenReturn($jsonBody);

        \Phake::when($this->httpClient)
            ->request('GET', 'https://api.darksky.net/forecast/123abc/111,222,1514764800?units=si')
            ->thenReturn($response);

        $darkSkyWeatherRetriever = new DarkSkyWeatherRetriever($this->httpClient, '123abc');
        $darkSkyWeatherRetriever->retrieveImageWeather($image);

        \Phake::verify($this->httpClient, \Phake::times(1))
            ->request('GET', 'https://api.darksky.net/forecast/123abc/111,222,1514764800?units=si');
    }
}
