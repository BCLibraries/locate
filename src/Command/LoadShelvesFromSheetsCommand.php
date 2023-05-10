<?php

namespace App\Command;

use App\Importer\SheetsReader;
use App\Service\CallNoNormalizer\CallNumberNormalizer;
use Google\Exception;
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

    public function __construct(SheetsReader $reader, CallNumberNormalizer $normalizer)
    {
        $this->reader = $reader;
        $this->normalizer = $normalizer;
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
        $sheet = $this->reader->read();
        $io = new SymfonyStyle($input, $output);
        $io->success("synced shelves");
        return self::SUCCESS;
    }
}
