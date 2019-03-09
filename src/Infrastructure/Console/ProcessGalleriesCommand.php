<?php
declare(strict_types = 1);

namespace App\Infrastructure\Console;

use App\Application\Service\FileFinder;
use App\Domain\Command\ProcessGallery;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class ProcessGalleriesCommand extends Command
{
    use LockableTrait;

    /** @var FileFinder */
    private $fileFinder;

    /** @var CommandBus */
    private $commandBus;

    /** @var LoggerInterface */
    private $logger;

    /** @var ProgressBarHelper */
    private $progressBarHelper;

    public function __construct(
        FileFinder $fileFinder,
        CommandBus $commandBus,
        LoggerInterface $logger,
        ProgressBarHelper $progressBarHelper
    ) {
        parent::__construct();
        $this->fileFinder = $fileFinder;
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->progressBarHelper = $progressBarHelper;
    }

    protected function configure(): void
    {
        $this
            ->setName('mappics:process-galleries')
            ->setDescription('Reads images files in the configured path and persists all the information')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process');

            return 0;
        }

        $stopwatch = new Stopwatch();
        $stopwatch->start('processGalleryCommand');

        $io = new SymfonyStyle($input, $output);
        $io->title('Mappics - Process galleries command');
        $this->logger->info('Process galleries command started');

        $galleries = $this->fileFinder->findGalleries();

        $this->progressBarHelper->createGalleriesProgressBar($output, count($galleries));

        foreach ($galleries as $galleryFileInfo) {
            $this->progressBarHelper->createSingleGalleryProgressBar($output, $galleryFileInfo);
            $processGallery = new ProcessGallery($galleryFileInfo);
            $this->commandBus->handle($processGallery);
        }

        $this->progressBarHelper->endGalleriesProgressBar();
        $io->newLine();
        $io->success('Galleries and images processed');
        $processGalleryCommandEvent = $stopwatch->stop('processGalleryCommand');

        $duration = ($processGalleryCommandEvent->getDuration() / 1000);
        $memory = ($processGalleryCommandEvent->getMemory() / 1024);
        $io->listing([ 'Duration: ' . $duration . ' sec', 'Memory: ' . $memory . ' kB']);
        $this->logger->info('Process galleries command ended - Duration: ' . $duration . ' sec - Memory: ' . $memory . ' kB');

        $this->release();
    }
}
