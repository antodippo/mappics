<?php
declare(strict_types = 1);

namespace App\Application\CommandHandler;

use App\Application\Event\SfGalleryProcessed;
use App\Application\Service\Slugger;
use App\Application\Service\UuidGenerator;
use App\Domain\Command\ProcessGallery;
use App\Domain\Entity\Gallery;
use App\Domain\Exception\GalleryNotFoundException;
use App\Domain\Repository\GalleryRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProcessGalleryHandler
{
    /**
     * @var GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var UuidGenerator
     */
    private $uuidGenerator;
    /**
     * @var Slugger
     */
    private $slugger;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    /**
     * ProcessGalleryHandler constructor.
     * @param GalleryRepository $galleryRepository
     * @param UuidGenerator $uuidGenerator
     * @param Slugger $slugger
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        GalleryRepository $galleryRepository,
        UuidGenerator $uuidGenerator,
        Slugger $slugger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->galleryRepository = $galleryRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->slugger = $slugger;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(ProcessGallery $command)
    {
        $galleryFileInfo = $command->getGalleryFileInfo();
        $name = basename($galleryFileInfo->getRealPath());

        try {
            $persistedGallery = $this->galleryRepository->findByName($name);
        } catch (GalleryNotFoundException $e) {
            $persistedGallery = new Gallery(
                $this->uuidGenerator->generateUuid(),
                $galleryFileInfo->getRealPath(),
                $name,
                $this->slugger->slugify($name)
            );
            $this->galleryRepository->add($persistedGallery);
        }

        $galleryProcessed = new SfGalleryProcessed($persistedGallery);
        $this->eventDispatcher->dispatch(SfGalleryProcessed::NAME, $galleryProcessed);
    }
}
