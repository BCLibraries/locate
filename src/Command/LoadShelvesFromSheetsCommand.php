<?php

namespace App\Command;

use App\Entity\Shelf;
use App\Importer\SheetsReader;
use App\Importer\Spreadsheet;
use App\Importer\SpreadsheetRow;
use App\Repository\ShelfRepository;
use App\Service\CallNoNormalizer\CallNumberNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadShelvesFromSheetsCommand extends Command
{
    protected static $defaultName = 'locate:load-from-sheets';
    private SheetsReader $reader;
    private CallNumberNormalizer $normalizer;
    private EntityManagerInterface $entityManager;
    private ShelfRepository $repository;

    private const BACKUP_DIR = __DIR__ . '/../../backups';

    public function __construct(SheetsReader $reader, CallNumberNormalizer $normalizer, EntityManagerInterface $entityManager, ShelfRepository $repository)
    {
        $this->reader = $reader;
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setDescription('Load shelves from Google Sheets')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Display changes without saving');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $sheet = $this->reader->read();
        if ($this->sheetHasNotBeenLoaded($sheet)) {
            $this->loadNewValue($sheet);
            $this->saveSheet($sheet);
            $io->success("loaded new values from Sheet");

        } else {
            $io->info("no new values to load");
        }
        return self::SUCCESS;
    }

    /**
     * Have we previously saved this Sheet?
     *
     * To prevent database churn, we only load Sheets that have new data in them. We track
     * new data by fingerprinting each version of the Sheet and looking for the fingerprint
     * in the file name of a file in the backup directory.
     *
     * @throws \Exception
     */
    private function sheetHasNotBeenLoaded(Spreadsheet $sheet): bool
    {
        $fingerprint = $sheet->fingerprint();
        foreach (scandir(self::BACKUP_DIR) as $file) {
            if (str_contains($file, $fingerprint)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Save the downloaded Sheet to a CSV file
     *
     * Most importantly, we are storing the
     *
     * @throws \Exception
     */
    private function saveSheet(Spreadsheet $sheet): void
    {
        $timestamp = date('Ymd-His');
        $backup_filename = "{$timestamp}-{$sheet->fingerprint()}.csv";
        file_put_contents(self::BACKUP_DIR . '/' . $backup_filename, $sheet->asCSV());
    }

    /**
     * Load values from a Google Sheet into the database.
     */
    private function loadNewValue(Spreadsheet $sheet): void
    {
        foreach ($sheet->nextRow() as $row) {
            $shelf = $this->repository->find($row->getId());
            if ($shelf) {
                $this->updateShelf($shelf, $row);
            } else {
                // For now, error out if we find a new shelf. @todo add new shelf?
                throw new \RuntimeException("Found shelf in Sheet but not database: {$shelf->getId()}");
            }
            $this->entityManager->flush();
        }
    }

    /**
     * Update a single shelf
     */
    private function updateShelf(Shelf $shelf, SpreadsheetRow $row): void
    {
        $end_call_number = $row->getEndCallNumber();
        $start_call_number = $row->getStartCallNumber();
        $normalized_end_call_number = $this->normalizer->normalize($end_call_number);
        $normalized_start_call_number = $this->normalizer->normalize($start_call_number);

        $shelf->setCode($row->getCode());
        $shelf->setStartCallNumber($start_call_number);
        $shelf->setEndCallNumber($end_call_number);
        $shelf->setStartSortCallNumber($normalized_start_call_number);
        $shelf->setEndSortCallNumber($normalized_end_call_number);
    }
}
