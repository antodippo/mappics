<?php

namespace App\Tests\Infrastructure\Service;

use App\Domain\Entity\ExifData;
use App\Domain\Entity\Image;
use App\Domain\Entity\GeoDescription;
use App\Infrastructure\Service\OSMGeoInfoRetriever;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class OSMGeoInfoRetrieverTest extends TestCase
{

    /**
     * @var Client
     */
    private $httpClient;

    public function setUp()
    {
        $this->httpClient = \Phake::mock(ClientInterface::class);
    }

    public function test_retrieveImageGeoInfo()
    {
        $exifData = \Phake::mock(ExifData::class);
        \Phake::when($exifData)->getLatitude()->thenReturn('111');
        \Phake::when($exifData)->getLongitude()->thenReturn('222');

        $image = \Phake::mock(Image::class);
        \Phake::when($image)->getExifData()->thenReturn($exifData);
        \Phake::when($image)->hasExifGeoCoordinates()->thenReturn(true);

        $jsonBody = '
        {
            "name": "Christopher Columbus Monument",
            "display_name": "Christopher Columbus Monument, Spanish Parade, Townparks, Eyre Square, Galway Municipal District, Cathair na Gaillimhe, County Galway, Connacht, H91 F5TE, Ireland"
        }';

        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(200);
        \Phake::when($response)->getBody()->thenReturn($jsonBody);

        \Phake::when($this->httpClient)
            ->request('GET', 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=111&lon=222')
            ->thenReturn($response);

        $OSMGeoInfoRetriever = new OSMGeoInfoRetriever($this->httpClient);
        $geoDescription = $OSMGeoInfoRetriever->retrieveImageGeoInfo($image);

        \Phake::verify($this->httpClient, \Phake::times(1))
            ->request('GET', 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=111&lon=222');

        $this->assertEquals(
            new GeoDescription(
                'Christopher Columbus Monument',
                'Christopher Columbus Monument, Spanish Parade, Townparks, Eyre Square, Galway Municipal District, Cathair na Gaillimhe, County Galway, Connacht, H91 F5TE, Ireland'
            ),
            $geoDescription
        );
    }

    public function test_retrieveImageGeoInfo_withoutName()
    {
        $exifData = \Phake::mock(ExifData::class);
        \Phake::when($exifData)->getLatitude()->thenReturn('111');
        \Phake::when($exifData)->getLongitude()->thenReturn('222');

        $image = \Phake::mock(Image::class);
        \Phake::when($image)->getExifData()->thenReturn($exifData);
        \Phake::when($image)->hasExifGeoCoordinates()->thenReturn(true);

        $jsonBody = '
        {
            "name": "",
            "display_name": "Christopher Columbus Monument, Spanish Parade, Townparks, Eyre Square, Galway Municipal District, Cathair na Gaillimhe, County Galway, Connacht, H91 F5TE, Ireland"
        }';

        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(200);
        \Phake::when($response)->getBody()->thenReturn($jsonBody);

        \Phake::when($this->httpClient)
            ->request('GET', 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=111&lon=222')
            ->thenReturn($response);

        $OSMGeoInfoRetriever = new OSMGeoInfoRetriever($this->httpClient);
        $geoDescription = $OSMGeoInfoRetriever->retrieveImageGeoInfo($image);

        \Phake::verify($this->httpClient, \Phake::times(1))
            ->request('GET', 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=111&lon=222');

        $this->assertEquals(
            new GeoDescription(
                'Christopher Columbus Monument, Spanish Parade',
                'Christopher Columbus Monument, Spanish Parade, Townparks, Eyre Square, Galway Municipal District, Cathair na Gaillimhe, County Galway, Connacht, H91 F5TE, Ireland'
            ),
            $geoDescription
        );
    }

    /**
     * @expectedException \App\Domain\Exception\GeoInfoRetrievingException
     */
    public function test_retrieveImageGeoInfo_withErrorStatusCode()
    {
        $exifData = \Phake::mock(ExifData::class);
        \Phake::when($exifData)->getLatitude()->thenReturn('111');
        \Phake::when($exifData)->getLongitude()->thenReturn('222');

        $image = \Phake::mock(Image::class);
        \Phake::when($image)->getExifData()->thenReturn($exifData);
        \Phake::when($image)->hasExifGeoCoordinates()->thenReturn(true);

        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(500);

        \Phake::when($this->httpClient)
            ->request('GET', 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=111&lon=222')
            ->thenReturn($response);

        $OSMGeoInfoRetriever = new OSMGeoInfoRetriever($this->httpClient);
        $OSMGeoInfoRetriever->retrieveImageGeoInfo($image);

        \Phake::verify($this->httpClient, \Phake::times(1))
            ->request('GET', 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=111&lon=222');
    }

    /**
     * @expectedException \App\Domain\Exception\GeoInfoRetrievingException
     * @expectedExceptionMessage Fake error description
     */
    public function test_retrieveImageGeoInfo_withErrorBody()
    {
        $exifData = \Phake::mock(ExifData::class);
        \Phake::when($exifData)->getLatitude()->thenReturn('111');
        \Phake::when($exifData)->getLongitude()->thenReturn('222');

        $image = \Phake::mock(Image::class);
        \Phake::when($image)->getExifData()->thenReturn($exifData);
        \Phake::when($image)->hasExifGeoCoordinates()->thenReturn(true);

        $jsonBody = '
        {
            "error": "Fake error description"
        }';

        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($response)->getStatusCode()->thenReturn(200);
        \Phake::when($response)->getBody()->thenReturn($jsonBody);

        \Phake::when($this->httpClient)
            ->request('GET', 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=111&lon=222')
            ->thenReturn($response);

        $OSMGeoInfoRetriever = new OSMGeoInfoRetriever($this->httpClient);
        $OSMGeoInfoRetriever->retrieveImageGeoInfo($image);

        \Phake::verify($this->httpClient, \Phake::times(1))
            ->request('GET', 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=111&lon=222');
    }

    /**
     * @expectedException \App\Domain\Exception\GeoInfoRetrievingException
     * @expectedExceptionMessage Missing Exif geo coordinates
     */
    public function test_retrieveImageGeoInfo_withoutGeoData()
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($image)->hasExifGeoCoordinates()->thenReturn(false);

        $OSMGeoInfoRetriever = new OSMGeoInfoRetriever($this->httpClient);
        $OSMGeoInfoRetriever->retrieveImageGeoInfo($image);

        \Phake::verify($this->httpClient, \Phake::times(0))->request(\Phake::anyParameters());
    }
}
