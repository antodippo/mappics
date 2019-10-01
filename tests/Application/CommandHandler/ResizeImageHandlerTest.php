<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\CommandHandler\ResizeImageHandler;
use App\Application\Service\ImageResizer;
use App\Domain\Command\ResizeImage;
use App\Domain\Entity\Image;
use App\Domain\Repository\ImageRepository;
use PHPUnit\Framework\TestCase;

class ResizeImageHandlerTest extends TestCase
{

    /** @var ImageRepository */
    private $imageRepository;

    /** @var ImageResizer */
    private $imageResizer;

    public function setUp(): void
    {
        $this->imageRepository = \Phake::mock(ImageRepository::class);
        $this->imageResizer = \Phake::mock(ImageResizer::class);
    }

    public function test_handle()
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageResizer)->resize($image)->thenReturn('/path/to/resized/image');
        \Phake::when($this->imageResizer)->createThumbnail($image)->thenReturn('/path/to/thumbnail');

        $command = new ResizeImage($image);
        $resizeImageHandler = new ResizeImageHandler($this->imageRepository, $this->imageResizer);
        $resizeImageHandler->handle($command);

        \Phake::verify($this->imageResizer, \Phake::times(1))->resize($image);
        \Phake::verify($this->imageResizer, \Phake::times(1))->createThumbnail($image);
        \Phake::verify($image, \Phake::times(1))->updateResizedImagesFilename(
            '/path/to/resized/image',
            '/path/to/thumbnail'
        );
        \Phake::verify($this->imageRepository)->add($image);
    }
}
