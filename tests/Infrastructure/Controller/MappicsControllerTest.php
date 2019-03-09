<?php

namespace App\Tests\Infrastructure\Controller;

use App\Infrastructure\Controller\MappicsController;
use App\Domain\Entity\ExifData;
use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Domain\Repository\GalleryRepository;
use App\Domain\Repository\ImageRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class MappicsControllerTest extends TestCase
{

    /** @var GalleryRepository */
    private $galleryRepository;

    /** @var ImageRepository */
    private $imageRepository;

    /** @var Twig_Environment */
    private $templating;

    /** @var MappicsController */
    private $mappicsController;

    public function setUp()
    {
        $this->galleryRepository = \Phake::mock(GalleryRepository::class);
        $this->imageRepository = \Phake::mock(ImageRepository::class);
        $this->templating = \Phake::mock(Twig_Environment::class);

        $this->mappicsController = new MappicsController(
            $this->galleryRepository,
            $this->imageRepository,
            $this->templating,
            'fake-mapbox-api-key'
        );
    }

    public function test_galleries()
    {
        \Phake::when($this->galleryRepository)->findAll()->thenReturn(
            [
                new Gallery('gallery-id1', 'path-to-gallery1', 'Gallery1', 'gallery1'),
                new Gallery('gallery-id2', 'path-to-gallery2', 'Gallery2', 'gallery2'),
                new Gallery('gallery-id3', 'path-to-gallery3', 'Gallery3', 'gallery3')
            ]
        );

        $response = $this->mappicsController->galleries();

        \Phake::verify($this->templating, \Phake::times(3))->render('popups/gallery.html.twig', \Phake::ignoreRemaining());
        \Phake::verify($this->templating, \Phake::times(1))->render('mappics/galleries.html.twig', \Phake::ignoreRemaining());
        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_gallery()
    {
        $gallery = new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery');
        $image = new Image(
            'image-id',
            'image-filename',
            $gallery,
            new ExifData(1.0, 1.0, null, null, null, null, null, null, null, null)
        );
        $gallery->addImage($image);
        $gallery->addImage($image);
        $gallery->addImage($image);

        \Phake::when($this->galleryRepository)->findBySlug('gallery-slug')->thenReturn($gallery);

        $response = $this->mappicsController->gallery('gallery-slug');

        \Phake::verify($this->templating, \Phake::times(3))->render('popups/image.html.twig', \Phake::ignoreRemaining());
        \Phake::verify($this->templating, \Phake::times(1))->render('mappics/gallery.html.twig', \Phake::ignoreRemaining());
        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_worldmap()
    {
        $gallery = new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery');
        $image = new Image(
            'image-id',
            'image-filename',
            $gallery,
            new ExifData(1.0, 1.0, null, null, null, null, null, null, null, null)
        );
        $gallery->addImage($image);
        $gallery->addImage($image);
        $gallery->addImage($image);
        \Phake::when($this->galleryRepository)->findAll()->thenReturn([$gallery, $gallery]);

        $response = $this->mappicsController->worldmap();

        \Phake::verify($this->templating, \Phake::times(6))->render('popups/image.html.twig', \Phake::ignoreRemaining());
        \Phake::verify($this->templating, \Phake::times(1))->render('mappics/worldmap.html.twig', \Phake::ignoreRemaining());
        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_imageModal()
    {
        $image = new Image(
            'image-id',
            'image-filename',
            new Gallery('gallery-id', 'path-to-gallery', 'Gallery', 'gallery'),
            new ExifData(1.0, 1.0, null, null, null, null, null, null, null, null)
        );
        \Phake::when($this->imageRepository)->findById('image-id')->thenReturn($image);

        $response = $this->mappicsController->imageModal('image-id');

        \Phake::verify($this->templating, \Phake::times(1))->render('modal/image.html.twig', [ 'image' => $image, 'mapboxApiKey' => 'fake-mapbox-api-key' ]);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_about()
    {
        $response = $this->mappicsController->about();
        $this->assertInstanceOf(Response::class, $response);
    }
}
