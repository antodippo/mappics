<?php
declare(strict_types = 1);

namespace App\Infrastructure\Console;

use App\Application\Service\FileFinder;
use App\Domain\Entity\FileInfo;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ProgressBarHelper
{
    /** @var ProgressBar */
    private $galleriesProgressBar;

    /** @var array */
    private $singleGalleryProgressBars;

    /** @var FileFinder */
    private $fileFinder;

    public function __construct(FileFinder $fileFinder)
    {
        $this->fileFinder = $fileFinder;
    }

    public function createGalleriesProgressBar(OutputInterface $output, int $galleriesCount): void
    {
        if ($output instanceof ConsoleOutput) {
            $output = $output->section();
        }
        $this->galleriesProgressBar = new ProgressBar($output, $galleriesCount);
        $this->galleriesProgressBar->setFormat("<fg=white;bg=blue>Galleries progress: %current%/%max% [%bar%] %percent:3s%% </>");
        $this->galleriesProgressBar->start();
    }

    public function advanceGalleriesProgressBar(): void
    {
        $this->galleriesProgressBar->advance();
    }

    public function endGalleriesProgressBar(): void
    {
        $this->galleriesProgressBar->finish();
    }

    public function createSingleGalleryProgressBar(OutputInterface $output, FileInfo $galleryInfo): void
    {
        if ($output instanceof ConsoleOutput) {
            $output = $output->section();
        }
        $imagesCount = count($this->fileFinder->findImagesInPath($galleryInfo->getRealPath()));
        $galleryName = $galleryInfo->getFilename();
        $this->singleGalleryProgressBars[$galleryName] = new ProgressBar($output, $imagesCount);
        $this->singleGalleryProgressBars[$galleryName]->setFormat(" [{$galleryName}] %current%/%max% [%bar%] %percent:3s%% ");
        $this->singleGalleryProgressBars[$galleryName]->start();
    }

    public function advanceSingleGalleryProgressBar(string $galleryName): void
    {
        $this->singleGalleryProgressBars[$galleryName]->advance();
    }

    public function endSingleGalleryProgressBar(string $galleryName): void
    {
        $this->singleGalleryProgressBars[$galleryName]->finish();
    }
}
