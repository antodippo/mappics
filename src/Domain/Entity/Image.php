<?php
declare(strict_types = 1);

namespace App\Domain\Entity;

use Carbon\Carbon;

class Image
{
    /** @var string */
    private $id;

    /** @var string */
    private $filename;

    /** @var string */
    private $resizedFilename;

    /** @var string */
    private $thumbnailFilename;

    /** @var Gallery */
    private $gallery;

    /** @var string */
    private $description;

    /** @var string */
    private $longDescription;

    /** @var ExifData */
    private $exifData;

    /** @var Weather */
    private $weather;

    /** @var \DateTime */
    private $createdAt;

    public function __construct(string $id, string $filename, Gallery $gallery, ExifData $exifData)
    {
        $this->id = $id;
        $this->filename = $filename;
        $this->gallery = $gallery;
        $this->exifData = $exifData;
        $this->createdAt = new Carbon();
    }

    public function updateResizedImagesFilename(string $resizedFilename, string $thumbnailFilename)
    {
        $this->resizedFilename = $resizedFilename;
        $this->thumbnailFilename = $thumbnailFilename;
    }

    public function updateGeoDescription(GeoDescription $geoDescription): void
    {
        $this->description = (string) $geoDescription->shortDescription;
        $this->longDescription = (string) $geoDescription->longDescription;
    }

    public function recordWeather(Weather $weather): void
    {
        $this->weather = $weather;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setLongDescription(string $longDescription): void
    {
        $this->longDescription = $longDescription;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getResizedFilename(): ?string
    {
        return $this->resizedFilename;
    }

    public function getThumbnailFilename(): ?string
    {
        return $this->thumbnailFilename;
    }

    public function getGallery(): Gallery
    {
        return $this->gallery;
    }

    public function getGalleryName(): string
    {
        return $this->gallery->getName();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function getExifData(): ExifData
    {
        return $this->exifData;
    }

    public function getWeather(): ?Weather
    {
        return $this->weather;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function needsResizing(): bool
    {
        return empty($this->getResizedFilename()) || empty($this->getThumbnailFilename());
    }

    public function needsDescriptionUpdate(): bool
    {
        return (empty($this->getDescription()) || empty($this->getLongDescription()))
            && $this->hasExifGeoCoordinates();
    }

    public function needsWeatherUpdate(): bool
    {
        return (empty($this->getWeather()) || $this->getWeather()->isUndefined())
            && $this->hasExifGeoCoordinates()
            && !is_null($this->getExifData()->getTakenAt());
    }

    public function hasExifGeoCoordinates(): bool
    {
        return !empty($this->getExifData()) && $this->getExifData()->hasGeoCoordinates();
    }
}
