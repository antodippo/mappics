<?php
declare(strict_types = 1);

namespace App\Application\CommandHandler;

use App\Application\Service\ImageResizer;
use App\Domain\Command\ResizeImage;
use App\Domain\Repository\ImageRepository;

class ResizeImageHandler
{

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var ImageResizer
     */
    private $imageResizer;

    /**
     * ResizeImageHandler constructor.
     * @param ImageRepository $imageRepository
     * @param ImageResizer $imageResizer
     */
    public function __construct(ImageRepository $imageRepository, ImageResizer $imageResizer)
    {
        $this->imageRepository = $imageRepository;
        $this->imageResizer = $imageResizer;
    }

    public function handle(ResizeImage $resizeImage)
    {
        $image = $resizeImage->getImage();
        $image->updateResizedImagesFilename(
            $this->imageResizer->resize($image),
            $this->imageResizer->createThumbnail($image)
        );
        $this->imageRepository->add($image);
    }
}
