<?php

namespace App\Tests\Infrastructure\Console;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessGalleriesCommandTest extends WebTestCase
{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->application = new Application(self::$kernel);
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();

        $purger = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute([]);
    }

    public function test_execution(): void
    {
        $command = $this->application->find('mappics:process-galleries');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $galleryRepository = $this->application->getKernel()->getContainer()->get('App\Domain\Repository\GalleryRepository');
        $this->assertCount(2, $galleryRepository->findAll());

        $imageRepository = $this->application->getKernel()->getContainer()->get('App\Domain\Repository\ImageRepository');
        $this->assertCount(3, $imageRepository->findAll());

        $gallery = $galleryRepository->findByName('Italy');
        $image = $imageRepository->findByFilenameAndGallery('good.JPG', $gallery);
        $this->assertEquals('Colosseum, Rome', $image->getDescription());
        $this->assertEquals('Sunny day!', $image->getWeather()->getDescription());
        $this->assertEquals('Italy/resized/good.JPG', $image->getResizedFilename());
        $this->assertEquals('Italy/thumbnail/good.JPG', $image->getThumbnailFilename());
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/resized/good.JPG');
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/thumbnail/good.JPG');

        // Images without creation date in Exif data will not have weather info
        $image = $imageRepository->findByFilenameAndGallery('no_creation_date.JPG', $gallery);
        $this->assertEquals('Colosseum, Rome', $image->getDescription());
        $this->assertNull($image->getWeather());
        $this->assertEquals('Italy/resized/no_creation_date.JPG', $image->getResizedFilename());
        $this->assertEquals('Italy/thumbnail/no_creation_date.JPG', $image->getThumbnailFilename());
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/resized/no_creation_date.JPG');
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/thumbnail/no_creation_date.JPG');

        // Images without geo coordinates in Exif data will not have description nor weather info
        $image = $imageRepository->findByFilenameAndGallery('no_coordinates.JPG', $gallery);
        $this->assertNull($image->getDescription());
        $this->assertNull($image->getWeather());
        $this->assertEquals('Italy/resized/no_coordinates.JPG', $image->getResizedFilename());
        $this->assertEquals('Italy/thumbnail/no_coordinates.JPG', $image->getThumbnailFilename());
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/resized/no_coordinates.JPG');
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/thumbnail/no_coordinates.JPG');
    }

    public function tearDown(): void
    {
        unlink(__DIR__ . '/DataFixtures/public/galleries/Italy/resized/good.JPG');
        unlink(__DIR__ . '/DataFixtures/public/galleries/Italy/thumbnail/good.JPG');
        unlink(__DIR__ . '/DataFixtures/public/galleries/Italy/resized/no_creation_date.JPG');
        unlink(__DIR__ . '/DataFixtures/public/galleries/Italy/thumbnail/no_creation_date.JPG');
        unlink(__DIR__ . '/DataFixtures/public/galleries/Italy/resized/no_coordinates.JPG');
        unlink(__DIR__ . '/DataFixtures/public/galleries/Italy/thumbnail/no_coordinates.JPG');
        rmdir(__DIR__ . '/DataFixtures/public/galleries/Italy/resized');
        rmdir(__DIR__ . '/DataFixtures/public/galleries/Italy/thumbnail');
        rmdir(__DIR__ . '/DataFixtures/public/galleries/Italy');
    }
}
