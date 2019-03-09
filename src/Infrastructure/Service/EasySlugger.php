<?php
declare(strict_types = 1);

namespace App\Infrastructure\Service;

use App\Application\Service\Slugger;

class EasySlugger implements Slugger
{
    public function slugify(string $string): string
    {
        return \EasySlugger\Slugger::slugify($string);
    }
}
