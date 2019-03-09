<?php
declare(strict_types = 1);

namespace App\Domain\Exception;

class ImageNotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = 'Image not found';
}
