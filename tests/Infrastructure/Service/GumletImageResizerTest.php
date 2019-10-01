<?php

namespace App\Tests\Infrastructure\Service;

use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
use App\Infrastructure\Service\GumletImageResizer;
use PHPUnit\Framework\TestCase;

class GumletImageResizerTest extends TestCase
{

    /** @var Image */
    private $image;

    public function setUp(): void
    {
        $gallery = \Phake::mock(Gallery::class);
        \Phake::when($gallery)->getName()->thenReturn('Azores');

        $this->image = \Phake::mock(Image::class);
        \Phake::when($this->image)->getFilename()->thenReturn('DSC_0892.JPG');
        \Phake::when($this->image)->getGallery()->thenReturn($gallery);
    }

    public function test_itResizesImages()
    {
        $galleriesPath = 'tests/Infrastructure/Service/DataFixtures/galleries';
        $publicGalleriesPath = 'tests/Infrastructure/Service/DataFixtures/public/galleries';
        $gumletImageResizer = new GumletImageResizer($galleriesPath, $publicGalleriesPath);
        $resizedPath = $gumletImageResizer->resize($this->image);

        $this->assertEquals('Azores/resized/DSC_0892.JPG', $resizedPath);
        $this->assertFileExists($publicGalleriesPath . '/' . $resizedPath);
    }

    public function test_itThrowsExeptionWhenResizing()
    {
        $this->expectException('\App\Domain\Exception\ImageResizingException');
        $publicGalleriesPath = 'tests/Infrastructure/Service/DataFixtures/public/galleries';
        $gumletImageResizer = new GumletImageResizer('wrong/path', $publicGalleriesPath);
        $gumletImageResizer->resize($this->image);
    }

    public function test_itCreatesThumbnails()
    {
        $galleriesPath = 'tests/Infrastructure/Service/DataFixtures/galleries';
        $publicGalleriesPath = 'tests/Infrastructure/Service/DataFixtures/public/galleries';
        $gumletImageResizer = new GumletImageResizer($galleriesPath, $publicGalleriesPath);
        $thumbnailPath = $gumletImageResizer->createThumbnail($this->image);

        $this->assertEquals('Azores/thumbnail/DSC_0892.JPG', $thumbnailPath);
        $this->assertFileExists($publicGalleriesPath . '/' . $thumbnailPath);
    }

    public function test_itThrowsExeptionWhenCreatingThumbanail()
    {
        $this->expectException('\App\Domain\Exception\ImageResizingException');
        $publicGalleriesPath = 'tests/Infrastructure/Service/DataFixtures/public/galleries';
        $gumletImageResizer = new GumletImageResizer('wrong/path', $publicGalleriesPath);
        $gumletImageResizer->createThumbnail($this->image);
    }

    public function tearDown(): void
    {
        @unlink(__DIR__ . '/DataFixtures/public/galleries/Azores/resized/DSC_0892.JPG');
        @unlink(__DIR__ . '/DataFixtures/public/galleries/Azores/thumbnail/DSC_0892.JPG');
        @rmdir(__DIR__ . '/DataFixtures/public/galleries/Azores/resized');
        @rmdir(__DIR__ . '/DataFixtures/public/galleries/Azores/thumbnail');
        @rmdir(__DIR__ . '/DataFixtures/public/galleries/Azores');
    }
}
