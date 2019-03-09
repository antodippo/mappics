<?php
declare(strict_types = 1);

namespace App\Infrastructure\Service;

use App\Application\Service\UuidGenerator;
use Ramsey\Uuid\Uuid;

class RamseyUuidGenerator implements UuidGenerator
{
    public function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}
