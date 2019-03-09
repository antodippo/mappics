<?php
declare(strict_types = 1);

namespace App\Domain\Entity;

interface FileInfo
{
    public function getFilename(): string;

    public function getRealPath(): string;

    public function __toString(): string;
}
