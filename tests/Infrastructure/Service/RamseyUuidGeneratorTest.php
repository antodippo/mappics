<?php

namespace App\Tests\Infrastructure\Service;

use App\Infrastructure\Service\RamseyUuidGenerator;
use PHPUnit\Framework\TestCase;

class RamseyUuidGeneratorTest extends TestCase
{
    public function test_itGetsUuid()
    {
        $uuidGenerator = new RamseyUuidGenerator();
        $uuid1 = $uuidGenerator->generateUuid();
        $uuid2 = $uuidGenerator->generateUuid();

        $this->assertRegExp('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $uuid1);
        $this->assertRegExp('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $uuid2);
        $this->assertNotEquals($uuid1, $uuid2);
    }
}
