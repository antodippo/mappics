<?php
declare(strict_types = 1);

namespace App\Infrastructure\Entity;

use App\Domain\Entity\FileInfo;

class SplFileInfo implements FileInfo
{
    /** @var \SplFileInfo */
    private $baseSplFileInfo;

    private function __construct(\SplFileInfo $baseSplFileInfo)
    {
        $this->baseSplFileInfo = $baseSplFileInfo;
    }

    public static function fromBaseSplFileInfo(\SplFileInfo $baseSplFileInfo): SplFileInfo
    {
        return new self($baseSplFileInfo);
    }

    public function getFilename(): string
    {
        return $this->baseSplFileInfo->getFilename();
    }

    public function getRealPath(): string
    {
        return $this->baseSplFileInfo->getRealPath() ?: '';
    }

    public function __toString(): string
    {
        return $this->baseSplFileInfo->getFilename();
    }
}
