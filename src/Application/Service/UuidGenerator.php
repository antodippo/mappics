<?php
declare(strict_types = 1);

namespace App\Application\Service;

interface UuidGenerator
{
    public function generateUuid(): string;
}
