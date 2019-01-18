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
        $pimple[Client::class] = function () {
            return new Client([
                'curl' => [CURLOPT_SSL_VERIFYPEER => false],
                'verify' => false
            ]);
        };
    }
}