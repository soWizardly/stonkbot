<?php

namespace App\Providers;


use App\Communication\ConnectionManager;
use App\Communication\Managers\SlackConnectionManager;
use GuzzleHttp\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use React\EventLoop\Factory;
use Slack\RealTimeClient;

class ConnectionManagerServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple[ConnectionManager::class] = function ($c) {
            $loop = Factory::create();
            $client = new RealTimeClient($loop, $c[Client::class]);
            $client->setToken(config('services')['slack']['token']);
            return (new SlackConnectionManager($client, $loop));
        };
    }
}