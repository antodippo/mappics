<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\CommandHandler\RetrieveImageGeoDescriptionHandler;
use App\Application\Service\GeoInfoRetriever;
use App\Domain\Command\RetrieveImageGeoDescription;
use App\Domain\Entity\Image;
use App\Domain\Entity\GeoDescription;
use App\Domain\Repository\ImageRepository;
use PHPUnit\Framework\TestCase;

class RetrieveImageGeoDescriptionHandlerTest extends TestCase
{

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var GeoInfoRetriever
     */
    private $geoInfoRetriever;

    public function setUp(): void
    {
        $this->imageRepository = \Phake::mock(ImageRepository::class);
        $this->geoInfoRetriever = \Phake::mock(GeoInfoRetriever::class);
    }

    public function test_handle()
    {
        $image = \Phake::mock(Image::class);
        $geoDescription = new GeoDescription('Foo', 'Foo Fighters');
        \Phake::when($this->geoInfoRetriever)->retrieveImageGeoInfo($image)->thenReturn($geoDescription);

        $command = new RetrieveImageGeoDescription($image);
        $retrieveImageGeoDescriptionHandler = new RetrieveImageGeoDescriptionHandler($this->imageRepository, $this->geoInfoRetriever);
        $retrieveImageGeoDescriptionHandler->handle($command);

        \Phake::verify($this->geoInfoRetriever, \Phake::times(1))->retrieveImageGeoInfo($image);
        \Phake::verify($image, \Phake::times(1))->updateDescription($geoDescription);
        \Phake::verify($this->imageRepository)->add($image);
    }
}
