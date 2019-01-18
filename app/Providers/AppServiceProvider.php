<?php

namespace App\Providers;

use GuzzleHttp\Client;

/**
 * Class AppServiceProvider registers general app requirements to the container.
 */
class AppServiceProvider implements \Pimple\ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple\Container $pimple A container instance
     */
    public function register(\Pimple\Container $pimple)
    {

        $pimple[\React\EventLoop\LoopInterface::class] = function () {
            return \React\EventLoop\Factory::create();
        };

        $pimple[Client::class] = function () {
            return new Client([
                'curl' => [CURLOPT_SSL_VERIFYPEER => false],
                'verify' => false
            ]);
        };

        $pimple[\Slack\RealTimeClient::class] = function ($c) {
            $client = new \Slack\RealTimeClient($c[\React\EventLoop\LoopInterface::class], $c[\GuzzleHttp\Client::class]);
            $client->setToken($c['config']['services']['slack']['token']);
            return $client;
        };
    }
}