<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class OauthInstallCommand extends Command
{
    protected static $defaultName = 'app:oauth:install';

    protected function configure()
    {
        $this
            ->setDescription('Creating private and public key')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $process = new Process('mkdir var/oauth');
        $process->run();

        $process = new Process('openssl genrsa -out var/oauth/private.key 2048');
        $process->run();

        $process = new Process('chmod 600 var/oauth/private.key');
        $process->run();

        $process = new Process('openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key');
        $process->run();

        $process = new Process('chmod 600 var/oauth/public.key');
        $process->run();

        $io->success('You generated private and public key.');
    }
}
