<?php
declare(strict_types = 1);

namespace App\Infrastructure\Service;

use App\Application\Service\GeoInfoRetriever;
use App\Domain\Entity\Image;
use App\Domain\Exception\GeoInfoRetrievingException;
use App\Domain\Entity\GeoDescription;
use GuzzleHttp\ClientInterface;

class OSMGeoInfoRetriever implements GeoInfoRetriever
{
    const OSM_ENDPOINT = 'https://nominatim.openstreetmap.org/reverse';

    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function retrieveImageGeoInfo(Image $image): GeoDescription
    {
        if (! $image->hasExifGeoCoordinates()) {
            throw new GeoInfoRetrievingException('Missing Exif geo coordinates');
        }

        $url = self::OSM_ENDPOINT . '?format=jsonv2' .
            '&lat=' . $image->getExifData()->getLatitude() .
            '&lon=' . $image->getExifData()->getLongitude();

        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() == 200) {
            $responseBody = json_decode((string) $response->getBody());
            if (isset($responseBody->error)) {
                throw new GeoInfoRetrievingException($responseBody->error);
            }

            $shortDescription = $responseBody->name;
            if (empty($shortDescription)) {
                $longDescriptionArray = explode(',', $responseBody->display_name);
                if (count($longDescriptionArray) > 1) {
                    $shortDescription = $longDescriptionArray[0] . ',' . $longDescriptionArray[1];
                }
            }

            $longDescription = $responseBody->display_name;

            return new GeoDescription($shortDescription, $longDescription);
        } else {
            throw new GeoInfoRetrievingException();
        }
    }
}
