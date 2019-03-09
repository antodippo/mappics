<?php
declare(strict_types = 1);

namespace App\Infrastructure\Service;

use App\Application\Service\ImageResizer;
use App\Domain\Entity\Image;
use App\Domain\Exception\ImageResizingException;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;

class GumletImageResizer implements ImageResizer
{

    /** @var string */
    private $galleriesPath;

    /** @var string */
    private $publicGalleriesPath;

    public function __construct(string $galleriesPath, string $publicGalleriesPath)
    {
        $this->galleriesPath = $galleriesPath;
        $this->publicGalleriesPath = $publicGalleriesPath;
    }

    public function resize(Image $image): string
    {
        $imageFullPath = $image->getGallery()->getName() . '/' . $image->getFilename();
        $resizedDirectoryPath = $image->getGallery()->getName() . '/resized';
        $resizedFullPath = $resizedDirectoryPath . '/' . $image->getFilename();

        try {
            $imageResize = new ImageResize($this->galleriesPath . '/' . $imageFullPath);
            $imageResize->quality_jpg = 100;
            $imageResize->quality_png = 9;
            $imageResize->resizeToBestFit(2048, 1152);

            @mkdir($this->publicGalleriesPath . '/' . $image->getGallery()->getName());
            @mkdir($this->publicGalleriesPath . '/' . $resizedDirectoryPath);
            $imageResize->save($this->publicGalleriesPath . '/' . $resizedFullPath);
        } catch (ImageResizeException $e) {
            throw new ImageResizingException($e->getMessage());
        }

        return $resizedFullPath;
    }

    public function createThumbnail(Image $image): string
    {
        $imageFullPath = $image->getGallery()->getName() . '/' . $image->getFilename();
        $thumbnailDirectoryPath = $image->getGallery()->getName() . '/thumbnail';
        $thumbnailFullPath = $thumbnailDirectoryPath . '/' . $image->getFilename();

        try {
            $imageResize = new ImageResize($this->galleriesPath . '/' . $imageFullPath);
            $imageResize->resizeToBestFit(320, 240);

            @mkdir($this->publicGalleriesPath . '/' . $image->getGallery()->getName());
            @mkdir($this->publicGalleriesPath . '/' . $thumbnailDirectoryPath);
            $imageResize->save($this->publicGalleriesPath . '/' . $thumbnailFullPath);
        } catch (ImageResizeException $e) {
            throw new ImageResizingException($e->getMessage());
        }

        return $thumbnailFullPath;
    }
}
