<?php
declare(strict_types = 1);

namespace App\Application\Service;

interface Slugger
{
    public function slugify(string $string): string;
}
