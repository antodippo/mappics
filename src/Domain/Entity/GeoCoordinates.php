<?php
declare(strict_types = 1);

namespace App\Domain\Entity;

use Webmozart\Assert\Assert;

class GeoCoordinates
{
    const MIN_LATITUDE = -90.0;
    const MAX_LATITUDE = 90.0;
    const MIN_LONGITUDE = -180.0;
    const MAX_LONGITUDE = 180.0;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        Assert::range($latitude, self::MIN_LATITUDE, self::MAX_LATITUDE);
        Assert::range($longitude, self::MIN_LONGITUDE, self::MAX_LONGITUDE);

        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}
