<?php

namespace App\Command;

use App\Entity\MapImage;
use App\Exception\BadCommandArgumentException;
use App\Importer\ShelflistImporter;
use App\Repository\MapImageRepository;
use App\Repository\MapRepository;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to import a shelflist
 *
 * Usage: ./bin console locate:import-shelflist [--mapfile map_file] map_code shelflist_file
 *
 * Import a shelf list into the database
 *
 * Arguments:
 *
 *     map_code the letter code for the map to update
 *     shelflist_file the shelflist file
 *
 * Options:
 *
 *     --mapfile (optional) - The map image file
 *
 * @package App\Command
 */
class LocateImportShelflistCommand extends Command
{
    protected static $defaultName = 'locate:import-shelflist';

    /** @var ShelflistImporter */
    private $importer;

    /** @var MapImageRepository */
    private $maps;

    public function __construct(ShelflistImporter $importer, MapRepository $maps)
    {
        $this->importer = $importer;
        $this->maps = $maps;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import a shelflist file')
            ->addArgument('map', InputArgument::REQUIRED, 'Map code')
            ->addArgument('shelflist', InputArgument::REQUIRED, 'Shelflist file')
            ->addOption('mapfile', null, InputOption::VALUE_OPTIONAL, 'Map image file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->runImport($input);
            $io->success('Successfully added ');
        } catch (Exception $e) {
            $io->error($e->getMessage());
        }

        return 0;
    }

    private function runImport(InputInterface $input): void
    {
        $map_code = $input->getArgument('map');
        $shelflist = $input->getArgument('shelflist');
        $mapfile = $input->getOption('mapfile');

        $map = $this->maps->find($map_code);

        if ($map === null) {
            throw new BadCommandArgumentException("Couldn't find map $map_code");
        }

        $report = $this->importer->import($shelflist, $map, $mapfile);
    }

    /**
     * @param $map_code
     * @param $mapfile
     * @return MapImage
     */
    private function buildMap($map_code, $mapfile): MapImage
    {
        // If the user is creating a map, require a map file
        if (!$mapfile) {
            throw new BadCommandArgumentException("Can't create new map without map file");
        }

        $map = new MapImage();
        $map->setCode($map_code);
        $map->setFilename($mapfile);

        return $map;
    }

}
