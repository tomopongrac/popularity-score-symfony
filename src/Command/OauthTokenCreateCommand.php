<?php

namespace App\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OauthTokenCreateCommand extends Command
{
    protected static $defaultName = 'app:oauth-token:create';

    protected function configure()
    {
        $this
            ->setName('app:oauth-token:create')
            ->setDescription('Create a new OAuth token for client credentials')
            ->addOption('client_id', null, InputOption::VALUE_OPTIONAL, 'Client ID', getenv('CLIENT_ID'))
            ->addOption('secret', null, InputOption::VALUE_OPTIONAL, 'Secret', getenv('SECRET'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $client = new Client();

        $response = $client->post('http://127.0.0.1:8002/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $input->getOption('client_id'),
                'client_secret' => $input->getOption('secret'),
                'scope' => 'read',
            ],
        ]);

        $io->title('Access token');
        $output->writeln(json_decode((string) $response->getBody(), true)['access_token']);
    }
}
