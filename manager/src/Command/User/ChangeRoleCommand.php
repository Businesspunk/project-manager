<?php

namespace App\Command\User;

use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use App\Model\User\UseCase;
use App\Model\User\Entity\User\Role;

class ChangeRoleCommand extends Command
{
    private $userFetcher;
    private $handler;
    public function __construct(UserFetcher $userFetcher, UseCase\Role\Handler $handler)
    {
        $this->userFetcher = $userFetcher;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:role')
            ->setDescription('Change user role');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $io->ask('E-mail');

        if (!$user = $this->userFetcher->findByEmail($email)) {
            throw new UserNotFoundException('');
        }

        $roles = [Role::ROLE_ADMIN, Role::ROLE_USER];
        $role = $io->askQuestion(new ChoiceQuestion('Role', $roles));

        $command = new UseCase\Role\Command($user->id, $role);
        $this->handler->handle($command);

        $io->success('Done');
        return Command::SUCCESS;
    }
}