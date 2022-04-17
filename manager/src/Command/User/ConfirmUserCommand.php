<?php

namespace App\Command\User;

use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use App\Model\User\UseCase\SignUp\Confirm\Manually;

class ConfirmUserCommand extends Command
{
    private $userFetcher;
    private $handler;
    public function __construct(UserFetcher $userFetcher, Manually\Handler $handler)
    {
        $this->userFetcher = $userFetcher;
        $this->handler = $handler;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:confirm')
            ->setDescription('Confirm user manually');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $io->ask('E-mail');

        if (!$user = $this->userFetcher->findByEmail($email)) {
            throw new UserNotFoundException('');
        }

        $command = new Manually\Command($user->id);
        $this->handler->handle($command);

        $io->success('Done');

        return Command::SUCCESS;
    }
}