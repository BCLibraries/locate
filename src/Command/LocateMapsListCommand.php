<?php

namespace App\Command;

use App\Repository\MapImageRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to list available maps
 *
 * Example: ./bin console locate:maps:list
 *
 * Lists maps (map code, file name, and description) available to Locate.
 *
 * @package App\Command
 */
class LocateMapsListCommand extends Command
{
    protected static $defaultName = 'locate:maps:list';

    /**
     * @var MapImageRepository
     */
    private $maps;

    public function __construct(MapImageRepository $maps)
    {
        parent::__construct();
        $this->maps = $maps;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('List available map codes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $rows = [];

        $maps = $this->maps->findAll();
        foreach ($maps as $map) {
            $row[] = [$map->getCode(), $map->getFilename(), $map->getDescription()];
        }

        $io->title('Available Maps');
        $io->table(['Code', 'Image', 'Description'], $rows);

        return 0;
    }
}
