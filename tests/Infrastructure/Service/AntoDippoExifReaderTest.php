<?php

namespace App\Tests\Infrastructure\Service;

use App\Domain\Entity\ExifData;
use App\Infrastructure\Entity\SplFileInfo;
use App\Infrastructure\Service\AntoDippoExifReader;
use Carbon\Carbon;
use ExifReader\Reader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo as BaseSplFileInfo;

class AntoDippoExifReaderTest extends TestCase
{
    /** @var Reader */
    private $reader;

    public function setUp(): void
    {
        $this->reader = new Reader();
    }

    public function test_itGetsExifData()
    {
        Carbon::setTestNow(new \DateTime());
        $file = SplFileInfo::fromBaseSplFileInfo(
            new BaseSplFileInfo(
                dirname(__FILE__) . '/DataFixtures/galleries/Azores/DSC_0892.JPG',
                dirname(__FILE__) . '/DataFixtures/galleries/Azores',
                dirname(__FILE__) . '/DataFixtures/galleries/Azores/DSC_0892.JPG'
            )
        );
        $exifReader = new AntoDippoExifReader($this->reader);
        $exifData = $exifReader->getExifData($file);
        $expectedExifData = new ExifData(
            37.839184,
            -25.793507,
            616,
            "Sony",
            "F5121",
            0.001,
            "f/",
            4.23,
            40,
            new \DateTimeImmutable('2017-08-24T12:07:14')
        );
        $this->assertEquals($expectedExifData, $exifData);
    }
    public function test_itThrowsException_withoutGPSData()
    {
        Carbon::setTestNow(new \DateTime());
        $file = SplFileInfo::fromBaseSplFileInfo(
            new BaseSplFileInfo(
                dirname(__FILE__) . '/DataFixtures/galleries/Italy/DSC_0401.JPG',
                dirname(__FILE__) . '/DataFixtures/galleries/Italy',
                dirname(__FILE__) . '/DataFixtures/galleries/Italy/DSC_0401.JPG'
            )
        );
        $exifReader = new AntoDippoExifReader($this->reader);
        $exifData = $exifReader->getExifData($file);
        $expectedExifData = new ExifData(
            null,
            null,
            null,
            "Sony",
            "F5121",
            0.0005,
            "f/",
            4.23,
            40,
            new \DateTimeImmutable('2017-03-26T14:31:47')
        );
        $this->assertEquals($expectedExifData, $exifData);
    }
}
