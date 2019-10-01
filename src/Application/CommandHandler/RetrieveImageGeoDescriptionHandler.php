<?php
declare(strict_types = 1);

namespace App\Application\CommandHandler;

use App\Application\Service\GeoInfoRetriever;
use App\Domain\Command\RetrieveImageGeoDescription;
use App\Domain\Repository\ImageRepository;

class RetrieveImageGeoDescriptionHandler
{

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var GeoInfoRetriever
     */
    private $geoInfoRetriever;

    /**
     * RetrieveImageGeoDescriptionHandler constructor.
     * @param ImageRepository $imageRepository
     * @param GeoInfoRetriever $geoInfoRetriever
     */
    public function __construct(ImageRepository $imageRepository, GeoInfoRetriever $geoInfoRetriever)
    {
        $this->imageRepository = $imageRepository;
        $this->geoInfoRetriever = $geoInfoRetriever;
    }

    public function handle(RetrieveImageGeoDescription $retrieveImageGeoDescription): void
    {
        $image = $retrieveImageGeoDescription->getImage();
        $geoDescription = $this->geoInfoRetriever->retrieveImageGeoInfo($image);
        $image->updateDescription($geoDescription);
        $this->imageRepository->add($image);
    }
}
