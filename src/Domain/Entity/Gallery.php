<?php
declare(strict_types = 1);

namespace App\Domain\Entity;

use App\Domain\Entity\GeoCoordinates;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Gallery
{

    /** @var string */
    private $id;

    /** @var string */
    private $path;

    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var \DateTime */
    private $createdAt;

    /** @var ArrayCollection */
    private $images;

    public function __construct(string $id, string $path, string $name, string $slug)
    {
        $this->id = $id;
        $this->path = $path;
        $this->name = $name;
        $this->slug = $slug;
        $this->createdAt = new Carbon();
        $this->images = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getImages(): Collection
    {
        $iterator = $this->images->getIterator();
        $iterator->uasort(
            function ($a, $b) {
                return ($a->getFilename() < $b->getFilename()) ? -1 : 1;
            }
        );

        return new ArrayCollection(iterator_to_array($iterator, false));
    }

    public function addImage(Image $image)
    {
        $this->images->add($image);
    }

    public function getFrontImage(): ?Image
    {
        if ($this->images->count()) {
            return $this->images->get(array_rand($this->images->toArray()));
        }

        return null;
    }

    public function getImagesMeanCoordinates(): GeoCoordinates
    {
        $minLatitude = GeoCoordinates::MAX_LATITUDE;
        $maxLatitude = GeoCoordinates::MIN_LATITUDE;

        $minLongitude = GeoCoordinates::MAX_LONGITUDE;
        $maxLongitude = GeoCoordinates::MIN_LONGITUDE;

        foreach ($this->images as $image) {
            if ($image->hasExifGeoCoordinates()) {
                $imageLatitude = $image->getExifData()->getLatitude();
                $imageLongitude = $image->getExifData()->getLongitude();

                $minLatitude = $imageLatitude < $minLatitude ? $imageLatitude : $minLatitude;
                $maxLatitude = $imageLatitude > $maxLatitude ? $imageLatitude : $maxLatitude;

                $minLongitude = $imageLongitude < $minLongitude ? $imageLongitude : $minLongitude;
                $maxLongitude = $imageLongitude > $maxLongitude ? $imageLongitude : $maxLongitude;
            }
        }

        $meanLatitude = ($maxLatitude + $minLatitude) / 2;
        $meanLongitude = ($maxLongitude + $minLongitude) / 2;

        return new GeoCoordinates($meanLatitude, $meanLongitude);
    }
}
