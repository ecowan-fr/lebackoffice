<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;

#[AsCommand(
    name: 'app:setup',
    description: 'First setup of the backoffice by Ecowan',
)]
class SetupCommand extends Command {
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $io->caution("This command is useful for the first setup. If you want to update the backoffice by ecowan, please use 'app:update'");
        $io->caution("The database will be created with this command. If it already exists in your database server, it will be deleted and then recreated. All data will be deleted.");

        if ($_ENV['APP_ENV'] === 'dev') {
            $io->warning('You are going to deploy the backoffice by Ecowan on a development environment.');
        } else {
            $io->warning('You are going to deploy the backoffice by Ecowan on a production environment.');
        }

        $io->comment('This command will use this information to create the database : ' . $_ENV['DATABASE_URL']);

        if (!$io->confirm('Do you want to continue ?', false)) {
            return Command::INVALID;
        }

        $NullOutput = new NullOutput;

        if ($_ENV['APP_ENV'] === 'dev') {
            if (!$io->confirm("Docker installed ?")) {
                return Command::INVALID;
            }

            if (!$io->confirm("All containers are launched ?")) {
                return Command::INVALID;
            }

            $fixtures = ['--append' => [true]];
        } else {
            if (!$io->confirm("The database server is reachable ?")) {
                return Command::INVALID;
            }

            $fixtures = ['--group' => ['production'], '--append' => [true]];
        }

        //Supression de la base de donnée
        $io->title("Drop of the database");
        $dropDatabaseInput = new ArrayInput(['--force' => true]);
        $command = $this->getApplication()->find('doctrine:database:drop');
        $command->run($dropDatabaseInput, $NullOutput);

        $io->success("Database droped !");

        // Ajout des migrations
        $io->title("Creation of the database");
        $command = $this->getApplication()->find('doctrine:database:create');
        if ($command->run($input, $output) != 0) {
            throw new RuntimeException("Impossible to create the new database !");
            return Command::FAILURE;
        }

        $io->success("New database created !");

        // Ajout des migrations
        $io->title("Add migrations");
        $migrationInput = new ArrayInput(['--no-interaction' => true]);
        $migrationInput->setInteractive(false);
        $command = $this->getApplication()->find('doctrine:migrations:migrate');
        if ($command->run($migrationInput, $output) != 0) {
            throw new RuntimeException("Impossible to add the migrations");
            return Command::FAILURE;
        }

        $io->success("Migrations added !");

        //Création des certificats JWT
        $io->title("Creating an RSA key pair for JWT tokens");
        $jwtInput = new ArrayInput(['--overwrite' => true]);
        $command = $this->getApplication()->find('lexik:jwt:generate-keypair');
        if ($command->run($jwtInput, $output) != 0) {
            throw new RuntimeException("Unable to create an SSL key pair! Try installing openssl and running the script as root.");
            return Command::FAILURE;
        }

        //Création d'un super admin
        $command = $this->getApplication()->find('app:create-superadmin');
        if ($command->run($input, $output) != 0) {
            throw new RuntimeException("Impossible to create a super admin !");
            return Command::FAILURE;
        }

        //Ajout des fixtures
        $io->title("Add fixtures");
        $fixturesInput = new ArrayInput($fixtures);
        $fixturesInput->setInteractive(false);
        $command = $this->getApplication()->find('doctrine:fixtures:load');
        if ($command->run($fixturesInput, $output) != 0) {
            throw new RuntimeException("Impossible to add the fixtures");
            return Command::FAILURE;
        }

        $io->writeln(''); //Ajout d'un retour a la ligne
        $io->success("Fixtures added !");

        $io->writeln(''); //Ajout d'un retour a la ligne
        $io->success("Le SETUP est terminé. Vous pouvez vous rendre sur votre instance du backoffice by Ecowan");

        return Command::SUCCESS;
    }
}
