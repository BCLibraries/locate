<?php

namespace App\Command;

use App\Entity\Library;
use App\Exception\BadCommandArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LocateLibrariesNewCommand extends Command
{
    protected static $defaultName = 'locate:libraries:new';

    /** @var EntityManagerInterface */
    private $em;

    /** @var ValidatorInterface */
    private $validator;

    /** @var CommandLineAuthorization */
    private $auth;

    public function __construct(
        EntityManagerInterface $entity_manager,
        ValidatorInterface $validator,
        CommandLineAuthorization $auth
    ) {
        $this->em = $entity_manager;
        parent::__construct();
        $this->validator = $validator;
        $this->auth = $auth;
    }

    protected function configure(): void
    {
        $this->setDescription('Create new library')
            ->addArgument('code', InputArgument::REQUIRED, 'Library code')
            ->addArgument('label', InputArgument::REQUIRED, 'Human-readable label');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (! $this->auth->isAuthorized()) {
            $io->error('You are not authorized to create new libraries.');
            return 1;
        }

        $code = $input->getArgument('code');
        $label = $input->getArgument('label');

        $library = new Library();
        $library->setCode($code);
        $library->setLabel($label);

        $this->validateLibrary($library);

        $this->em->persist($library);
        $this->em->flush();

        return 0;
    }

    private function validateLibrary(Library $library): void
    {
        $errors = $this->validator->validate($library);
        if ($errors->count() > 0) {
            $error_message = '';

            foreach ($errors as $error) {
                $error_message .= $error->getMessage() . " (value: \"{$error->getInvalidValue()}\")\n";
            }
            throw new BadCommandArgumentException($error_message);
        }
    }
}