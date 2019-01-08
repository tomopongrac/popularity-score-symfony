<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Trikoder\Bundle\OAuth2Bundle\Manager\ClientManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Manager\Doctrine\ClientManager;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;

class OauthClientCreateCommand extends Command
{
    protected static $defaultName = 'app:oauth-client:create';
    /**
     * @var ClientManager
     */
    private $clientManager;

    /**
     * OauthClientCreateCommand constructor.
     */
    public function __construct(ClientManagerInterface $clientManager)
    {
        parent::__construct();

        $this->clientManager = $clientManager;
    }


    protected function configure()
    {
        $this
            ->setName('app:oauth-client:create')
            ->setDescription('Create a new OAuth client')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Client Credentials');

        // Create a new client
        $client = new Client('Client-' . bin2hex(random_bytes(6)), bin2hex(random_bytes(32)));
        $grant = new Grant('client_credentials');
        $client->setGrants($grant);
        $scope = new Scope('read');
        $client->setScopes($scope);
        $client->setActive(1);

        // Save the client
        $this->clientManager->save($client);

        // Give the credentials back to the user
        $headers = ['Client ID', 'Client Secret'];
        $rows = [
            [$client->getIdentifier(), $client->getSecret()],
        ];

        $io->table($headers, $rows);

        return 0;
    }
}
