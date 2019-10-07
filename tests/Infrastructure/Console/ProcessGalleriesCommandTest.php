<?php

namespace App\Tests\Infrastructure\Console;

use App\Domain\Entity\Gallery;
use App\Domain\Entity\Image;
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

        $galleries = $this->entityManager->getRepository(Gallery::class)->findAll();
        $this->assertCount(2, $galleries);

        $images = $this->entityManager->getRepository(Image::class)->findAll();
        $this->assertCount(3, $images);

        $image = $this->entityManager->getRepository(Image::class)->findOneBy(['filename' => 'good.JPG']);
        $this->assertEquals('Colosseum, Rome', $image->getDescription());
        $this->assertEquals('Sunny day!', $image->getWeather()->getDescription());
        $this->assertEquals('Italy/resized/good.JPG', $image->getResizedFilename());
        $this->assertEquals('Italy/thumbnail/good.JPG', $image->getThumbnailFilename());
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/resized/good.JPG');
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/thumbnail/good.JPG');

        // Images without creation date in Exif data will not have weather info
        $image = $this->entityManager->getRepository(Image::class)->findOneBy(['filename' => 'no_creation_date.JPG']);
        $this->assertEquals('Colosseum, Rome', $image->getDescription());
        $this->assertNull($image->getWeather());
        $this->assertEquals('Italy/resized/no_creation_date.JPG', $image->getResizedFilename());
        $this->assertEquals('Italy/thumbnail/no_creation_date.JPG', $image->getThumbnailFilename());
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/resized/no_creation_date.JPG');
        $this->assertFileExists(__DIR__ . '/DataFixtures/public/galleries/Italy/thumbnail/no_creation_date.JPG');

        // Images without geo coordinates in Exif data will not have description nor weather info
        $image = $this->entityManager->getRepository(Image::class)->findOneBy(['filename' => 'no_coordinates.JPG']);
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
