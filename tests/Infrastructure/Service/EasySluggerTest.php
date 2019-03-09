<?php

namespace App\Tests\Infrastructure\Service;

use App\Infrastructure\Service\EasySlugger;
use PHPUnit\Framework\TestCase;

class EasySluggerTest extends TestCase
{

    /**
     * @dataProvider getStringAndSlugs
     */
    public function test_itSlugifies($string, $slug)
    {
        $slugger = new EasySlugger();
        $this->assertEquals($slug, $slugger->slugify($string));
    }

    public function getStringAndSlugs()
    {
        return [
            [ 'foo', 'foo' ],
            [ 'FOO', 'foo' ],
            [ 'foo fighters', 'foo-fighters' ],
            [ 'Foo Fighters', 'foo-fighters' ],
            [ 'Foo O\'Fighters', 'foo-ofighters' ],
        ];
    }
}
