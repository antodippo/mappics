<?php
declare(strict_types = 1);

namespace App\Domain\Exception;

class GalleryNotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = 'Gallery not found';
}
