<?php
declare(strict_types = 1);

namespace App\Domain\Entity;

class GeoDescription
{
    /** @var string|null */
    public $shortDescription;

    /** @var string|null */
    public $longDescription;

    public function __construct(?string $shortDescription, ?string $longDescription)
    {
        $this->shortDescription = $shortDescription;
        $this->longDescription = $longDescription;
    }
}
