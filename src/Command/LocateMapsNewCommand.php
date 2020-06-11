<?php

namespace App\Command;

use App\Entity\Library;
use App\Entity\Map;
use App\Exception\BadCommandArgumentException;
use App\Importer\MapImageImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command to list create a map
 *
 * Usage: ./bin console locate:maps:new map_file
 *
 * Loads a new Locate map.
 *
 * Arguments:
 *
 *     map_file - full path to map image file
 *
 * @package App\Command
 */
class LocateMapsNewCommand extends Command
{
    protected static $defaultName = 'locate:maps:new';

    private const NEW_MAP_CODE = 'NEW';

    /** @var EntityManagerInterface */
    private $entity_manager;

    /** @var ValidatorInterface */
    private $validator;

    /**  @var MapImageImporter */
    private $importer;

    /** @var CommandLineAuthorization */
    private $auth;

    public function __construct(
        EntityManagerInterface $entity_manager,
        ValidatorInterface $validator,
        MapImageImporter $importer,
        CommandLineAuthorization $auth
    ) {
        parent::__construct(self::$defaultName);
        $this->entity_manager = $entity_manager;
        $this->validator = $validator;
        $this->importer = $importer;
        $this->auth = $auth;
    }

    protected function configure(): void
    {
        $this->setDescription('Load a new map')
            ->addArgument('map_file', InputArgument::REQUIRED, 'Full path to map image file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->auth->isAuthorized()) {
            $io->error('You are not authorized to create new libraries.');
            return 1;
        }

        // Get the library to modify.
        $library = $this->askWhichLibrary($io);

        // Get the map to modify.
        $map = $this->askWhichMap($io, $library);

        // Import the map file.
        $file_path = $input->getArgument('map_file');
        $map_image_file = $this->importer->import($file_path, $map->getCode());
        $map->setImage($map_image_file);

        // Save everything to the database.
        $this->entity_manager->persist($map);
        $this->entity_manager->persist($library);
        $this->entity_manager->flush();

        // Toot your horn.
        $io->success("Added {$map_image_file->getOriginalFilename()} to {$map->getCode()}:{$map->getLabel()}");

        return 0;
    }

    /**
     * Which library are we adding a map to?
     *
     * @param SymfonyStyle $io
     * @return Library
     */
    private function askWhichLibrary(SymfonyStyle $io): Library
    {
        $which_library_question = 'Choose a library to add the map to ';
        $library_code = $io->choice($which_library_question, $this->libraryOptions());
        $library = $this->entity_manager->getRepository('App:Library')->findOneByCode($library_code);

        // No library? Something has gone terribly wrong.
        if ($library === null) {
            throw new \RuntimeException("Invalid library code ($library_code) selected.");
        }

        return $library;
    }

    /**
     * Which map are we changing? Or are we adding a new one?
     *
     * @param SymfonyStyle $io
     * @param Library $library
     * @return Map
     */
    private function askWhichMap(SymfonyStyle $io, Library $library): Map
    {
        // Ask for the map
        $which_map_question = 'Choose a map to replace, or select ' . self::NEW_MAP_CODE . ' to add a new map';
        $map_code = $io->choice($which_map_question, $this->mapOptions($library));

        // If they asked for a new map, generate one. Otherwise load it from the database.
        if ($map_code === self::NEW_MAP_CODE) {
            $map = $this->askForNewMap($library, $io);
        } else {
            $map = $this->entity_manager->getRepository(Map::class)->findOneByCode($map_code);
        }

        return $map;
    }

    /**
     * Prompt for a newly created map
     *
     * @param Library $library
     * @param SymfonyStyle $io
     * @return Map
     */
    private function askForNewMap(Library $library, SymfonyStyle $io): Map
    {
        // Ask for necessary info.
        $io->section('Creating new map');
        $code = $io->ask('Give the map a code (e.g. "LVL3_WEST")');
        $label = $io->ask('Give the map a human-readable label (e.g. Level 3 West)');

        // Build and validate map.
        $map = new Map($library, $code, $label);
        $this->validator->validate($map);

        return $map;
    }

    /**
     * Build list of library choices
     *
     * @return array hash with format [library_code => library_label, etc...]
     */
    private function libraryOptions(): array
    {
        $libraries = $this->entity_manager->getRepository('App:Library')->findAll();

        if ($libraries === []) {
            $message = 'No libraries available. Please create a library using locate:libraries:new.';
            throw new BadCommandArgumentException($message);
        }

        $options = [];
        foreach ($libraries as $library) {
            $options[$library->getCode()] = $library->getLabel();
        }

        return $options;
    }

    /**
     * Build list of map choices, including a new map
     *
     * @param Library $library
     * @return array hash with format [map_code => map_label,...,'NEW' => 'Add a new map']
     */
    private function mapOptions(Library $library): array
    {
        $options = [];
        foreach ($library->getMaps() as $map) {
            $options[$map->getCode()] = $map->getLabel();
        }
        $options[self::NEW_MAP_CODE] = 'Add a new map';
        return $options;
    }
}