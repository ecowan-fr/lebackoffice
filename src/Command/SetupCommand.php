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
    description: '1er setup du backoffice',
)]
class SetupCommand extends Command {
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        if ($_ENV['APP_ENV'] === 'dev') {
            $io->warning('Vous allez déployer le backoffice by Ecowan sur un environnement de développement.');
        } else {
            $io->warning('Vous allez déployer le backoffice by Ecowan sur un environnement de production.');
        }

        if (!$io->confirm('Voulez-vous continuer ?', false)) {
            return Command::INVALID;
        }

        $NullOutput = new NullOutput;

        if ($_ENV['APP_ENV'] === 'dev') {
            if (!$io->confirm("Docker est-il installé ?")) {
                return Command::INVALID;
            }
            if (!$io->confirm("Les conteneurs sont-ils lancés ?")) {
                return Command::INVALID;
            }

            $progress = $io->createProgressBar('3');
            $progress->start();
        } else {
            if (!$io->confirm("La base de données est-elle accessible ?")) {
                return Command::INVALID;
            }

            $progress = $io->createProgressBar('4');
            $progress->start();

            // Création de la base de donnée
            $io->writeln(''); //Ajout d'un retour a la ligne
            $io->title("Création de la base de donnée");
            $command = $this->getApplication()->find('doctrine:database:create');
            if ($command->run($input, $output) != 0) {
                throw new RuntimeException("Impossible de créer la base de données !");
                return Command::FAILURE;
            }

            $progress->advance();
        }

        // Ajout des migrations
        $io->writeln(''); //Ajout d'un retour a la ligne
        $io->title("Ajout des migrations dans la base de donnée");
        $migrationInput = new ArrayInput(['--no-interaction' => true]);
        $migrationInput->setInteractive(false);
        $command = $this->getApplication()->find('doctrine:migrations:migrate');
        if ($command->run($migrationInput, $output) != 0) {
            throw new RuntimeException("Impossible d'ajouter les migrations !");
            return Command::FAILURE;
        }

        $progress->advance();

        //Création des certificats JWT
        $io->writeln(''); //Ajout d'un retour a la ligne
        $io->title("Création d'une pair de clé RSA pour les tokens JWT");
        $jwtInput = new ArrayInput(['--overwrite' => true]);
        $command = $this->getApplication()->find('lexik:jwt:generate-keypair');
        if ($command->run($jwtInput, $output) != 0) {
            throw new RuntimeException("Impossible de créer une pair de clé SSL ! Essayez d'installer openssl et relancer le script en root.");
            return Command::FAILURE;
        }

        $progress->advance();

        //Création d'un super admin
        $io->writeln(''); //Ajout d'un retour a la ligne
        $command = $this->getApplication()->find('app:create-superadmin');
        if ($command->run($input, $output) != 0) {
            throw new RuntimeException("Impossible d'ajouter le super administrateur !");
            return Command::FAILURE;
        }

        $progress->advance();

        $io->writeln(''); //Ajout d'un retour a la ligne
        $io->success("Le SETUP est terminé. Vous pouvez vous rendre sur votre instance du backoffice by Ecowan");
        return Command::SUCCESS;
    }
}
