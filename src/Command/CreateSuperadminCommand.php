<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-superadmin',
    description: "Ajout d'un super administrateur",
)]
class CreateSuperadminCommand extends Command {

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $io->writeln(''); //Ajout d'un retour a la ligne
        $io->title("Ajout d'un super administrateur");

        $command = $this->getApplication()->find('doctrine:migrations:up-to-date');
        if ($command->run($input, new NullOutput) != 0) {
            throw new RuntimeException("
                Toutes les migrations ne sont pas jouées. Ré-exécutez les migrations ou le setup avant de relancer la création d'un super administrateur
            ");
            return Command::FAILURE;
        }

        $questionLastName = new Question('Nom de famille du compte super-administrateur');
        $questionLastName->setValidator(function ($answer) {
            if (!is_string($answer) || trim($answer) == '' || empty($answer)) {
                throw new RuntimeException(
                    "Le nom de famille est obligatoire"
                );
            }
            return $answer;
        });

        $lastname = $io->askQuestion($questionLastName);

        $questionFirstName = new Question('Prénom du compte super-administrateur');
        $questionFirstName->setValidator(function ($answer) {
            if (!is_string($answer) || trim($answer) == '' || empty($answer)) {
                throw new RuntimeException(
                    "Le prénom est obligatoire"
                );
            }
            return $answer;
        });

        $firstname = $io->askQuestion($questionFirstName);

        $questionEmail = new Question('Email du compte super-administrateur');
        $questionEmail->setValidator(function ($answer) {
            if (!is_string($answer) || trim($answer) == '' || empty($answer)) {
                throw new RuntimeException(
                    "L'adresse email est obligatoire"
                );
            } elseif (count($this->validator->validate($answer, new EmailConstraint())) > 0) {
                throw new RuntimeException(
                    "Ce n'est pas une adresse email valide"
                );
            } elseif ($this->userRepository->count(['email' => $answer]) > 0) {
                throw new RuntimeException(
                    "Un compte existe déjà avec cette adresse email"
                );
            }
            return strtolower($answer);
        });

        $email = $io->askQuestion($questionEmail);

        $questionPassword = new Question('Mot de passe du compte super-administrateur');
        $questionPassword->setHidden(true);
        $questionPassword->setValidator(function ($answer) {
            if (!is_string($answer) || trim($answer) == '' || empty($answer)) {
                throw new \RuntimeException(
                    "Le mot de passe est obligatoire"
                );
            }

            return $answer;
        });

        $user = (new User())
            ->setLastname($lastname)
            ->setFirstname($firstname)
            ->setEmail($email)
            ->setFullRoles();

        $this->userRepository->upgradePassword($user, $this->userPasswordHasher->hashPassword($user, $io->askQuestion($questionPassword)));

        $io->success("Le compte super-administrateur '$email' à été créer. Vous pouvez vous connecter.");
        return Command::SUCCESS;
    }
}
