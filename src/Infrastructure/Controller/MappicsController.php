<?php
declare(strict_types = 1);

namespace App\Infrastructure\Controller;

use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Repository\GalleryRepository;
use App\Domain\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class MappicsController
{

    /** @var GalleryRepository */
    private $galleryRepository;

    /** @var ImageRepository */
    private $imageRepository;

    /** @var Twig_Environment */
    private $templating;

    /** @var string */
    private $mapboxApiKey;

    public function __construct(
        GalleryRepository $galleryRepository,
        ImageRepository $imageRepository,
        Twig_Environment $templating,
        string $mapboxApiKey
    ) {
        $this->galleryRepository = $galleryRepository;
        $this->imageRepository = $imageRepository;
        $this->templating = $templating;
        $this->mapboxApiKey = $mapboxApiKey;
    }

    public function galleries(): Response
    {
        /** @var Gallery[] $galleries */
        $galleries = $this->galleryRepository->findAll();

        $dataArray = [];
        foreach ($galleries as $gallery) {
            $geoCoordinates = $gallery->getImagesMeanCoordinates();
            $dataArray[] = [
                $geoCoordinates->latitude,
                $geoCoordinates->longitude,
                $this->templating->render('popups/gallery.html.twig', [ 'gallery' => $gallery ])
            ];
        }

        $responseBody = $this->templating->render(
            'mappics/galleries.html.twig',
            [
                'galleries' => $galleries,
                'dataArray' => $dataArray,
                'mapboxApiKey' => $this->mapboxApiKey
            ]
        );

        return new Response($responseBody);
    }

    public function gallery(string $gallerySlug): Response
    {
        $gallery = $this->galleryRepository->findBySlug($gallerySlug);
        /** @var Image[] $images */
        $images = $gallery->getImages();

        $dataArray = [];
        foreach ($images as $image) {
            if ($image->hasExifGeoCoordinates()) {
                $dataArray[] = [
                    $image->getExifData()->getLatitude(),
                    $image->getExifData()->getLongitude(),
                    $image->getThumbnailFilename(),
                    $this->templating->render('popups/image.html.twig', [ 'image' => $image ])
                ];
            }
        }

        $responseBody = $this->templating->render(
            'mappics/gallery.html.twig',
            [
                'galleries' => $this->galleryRepository->findAll(),
                'gallery' => $gallery,
                'dataArray' => $dataArray,
                'mapboxApiKey' => $this->mapboxApiKey
            ]
        );

        return new Response($responseBody);
    }

    public function worldmap(): Response
    {
        /** @var Gallery[] $galleries */
        $galleries = $this->galleryRepository->findAll();

        $dataArray = [];
        foreach ($galleries as $gallery) {
            $images = $gallery->getImages();
            foreach ($images as $image) {
                if ($image->hasExifGeoCoordinates()) {
                    $dataArray[] = [
                        $image->getExifData()->getLatitude(),
                        $image->getExifData()->getLongitude(),
                        $image->getThumbnailFilename(),
                        $this->templating->render('popups/image.html.twig', [ 'image' => $image ])
                    ];
                }
            }
        }

        $responseBody = $this->templating->render(
            'mappics/worldmap.html.twig',
            [
                'galleries' => $galleries,
                'dataArray' => $dataArray,
                'mapboxApiKey' => $this->mapboxApiKey
            ]
        );

        return new Response($responseBody);
    }

    public function imageModal(string $imageId): Response
    {
        $image = $this->imageRepository->findById($imageId);
        $responseBody = $this->templating->render('modal/image.html.twig', [ 'image' => $image, 'mapboxApiKey' => $this->mapboxApiKey ]);

        return new Response($responseBody, 200);
    }

    public function about(): Response
    {
        /** @var Gallery[] $galleries */
        $galleries = $this->galleryRepository->findAll();
        $responseBody = $this->templating->render('mappics/about.html.twig', ['galleries' => $galleries]);

        return new Response($responseBody, 200);
    }
}
