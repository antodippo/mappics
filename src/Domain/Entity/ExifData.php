<?php
declare(strict_types = 1);

namespace App\Domain\Entity;

class ExifData
{
    /** @var float|null */
    private $latitude;

    /** @var float|null */
    private $longitude;

    /** @var float|null */
    private $altitude;

    /** @var string|null */
    private $make;

    /** @var string|null */
    private $model;

    /** @var string|null */
    private $exposure;

    /** @var string|null */
    private $aperture;

    /** @var string|null */
    private $focalLength;

    /** @var string|null */
    private $ISO;

    /** @var \DateTime|null */
    private $takenAt;

    public function __construct(
        ?float $latitude,
        ?float $longitude,
        ?float $altitude,
        ?string $make,
        ?string $model,
        ?string $exposure,
        ?string $aperture,
        ?string $focalLength,
        ?string $ISO,
        ?\DateTime $takenAt
    ) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->altitude = $altitude;
        $this->make = $make;
        $this->model = $model;
        $this->exposure = $exposure;
        $this->aperture = $aperture;
        $this->focalLength = $focalLength;
        $this->ISO = $ISO;
        $this->takenAt = $takenAt;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function setTakenAt(?\DateTime $takenAt): void
    {
        $this->takenAt = $takenAt;
    }

    public function getLatitude(): ?float
    {
        return (float) $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return (float) $this->longitude;
    }

    public function getAltitude(): ?float
    {
        return (float) $this->altitude;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getTakenAt(): ?\DateTime
    {
        return $this->takenAt;
    }

    public function hasGeoCoordinates(): bool
    {
        return !is_null($this->longitude) && !is_null($this->latitude);
    }
}
