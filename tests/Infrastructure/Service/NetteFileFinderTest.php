<?php

namespace App\Tests\Infrastructure\Service;

use App\Application\Service\FileFinder;
use App\Infrastructure\Service\NetteFileFinder;
use PHPUnit\Framework\TestCase;

class NetteFileFinderTest extends TestCase
{
    /** @var FileFinder */
    private $fileFinder;

    public function setUp()
    {
        $this->fileFinder = new NetteFileFinder('tests/Infrastructure/Service/DataFixtures/galleries/', ['jpg', 'png']);
    }

    public function test_itFindsDirectories()
    {
        $galleriesArray = $this->fileFinder->findGalleries();

        $this->assertCount(4, $galleriesArray);
        $this->assertEquals('Molise', array_pop($galleriesArray)->getFilename());
        $this->assertEquals('Italy', array_pop($galleriesArray)->getFilename());
        $this->assertEquals('Iceland', array_pop($galleriesArray)->getFilename());
        $this->assertEquals('Azores', array_pop($galleriesArray)->getFilename());
    }

    public function test_itFindsAllImageFiles()
    {
        $filesArray = $this->fileFinder
            ->findImagesInPath('tests/Infrastructure/Service/DataFixtures/galleries/Iceland/');

        $this->assertCount(2, $filesArray);
        $this->assertEquals('DSC_0243.JPG', array_pop($filesArray)->getFilename());
        $this->assertEquals('DSC_0114.JPG', array_pop($filesArray)->getFilename());
    }

    public function test_itDoesntFindImageFiles_inMolise()
    {
        $filesArray = $this->fileFinder
            ->findImagesInPath('tests/Infrastructure/Service/DataFixtures/galleries/Molise/');

        $this->assertCount(0, $filesArray);
    }
}
