<?php
declare(strict_types = 1);

namespace App\Infrastructure\Service;

use App\Application\Service\FileFinder;
use App\Infrastructure\Entity\SplFileInfo;
use Nette\Utils\Finder;

class NetteFileFinder implements FileFinder
{
    /** @var string */
    private $galleriesPath;

    /** @var array */
    private $imageFilesExtensions;

    public function __construct(string $galleriesPath, array $imageFilesExtensions)
    {
        $this->galleriesPath = $galleriesPath;
        $this->imageFilesExtensions = $imageFilesExtensions;
    }

    public function findGalleries(): array
    {
        $galleries = Finder::findDirectories()->in($this->galleriesPath);

        $galleriesArray = [];
        foreach ($galleries as $gallery) {
            $galleriesArray[] = SplFileInfo::fromBaseSplFileInfo($gallery);
        }
        sort($galleriesArray, SORT_STRING);

        return $galleriesArray;
    }

    public function findImagesInPath(string $path): array
    {
        $masks = [];
        foreach ($this->imageFilesExtensions as $imageFilesExtension) {
            $masks[] = '*.' . $imageFilesExtension;
        }
        $imageFiles = Finder::findFiles($masks)->in($path);

        $imagesArray = [];
        foreach ($imageFiles as $imageFile) {
            $imagesArray[] = SplFileInfo::fromBaseSplFileInfo($imageFile);
        }
        sort($imagesArray, SORT_STRING);

        return $imagesArray;
    }
}
