<?php

namespace App\Providers;



class DatabaseServiceProvider implements \Pimple\ServiceProviderInterface
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
        // Creates an empty SQLite file, if it doesn't exist..
//        (new \SQLite3(__DIR__ . '/storage/db.sqlite'));


        $pimple[\Doctrine\ORM\Configuration::class] = function () {
            return \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/../app'), true);
        };

        $pimple[\Doctrine\ORM\EntityManager::class] = function ($c) {
            return \Doctrine\ORM\EntityManager::create(config('database'), $c[\Doctrine\ORM\Configuration::class]);
        };
    }
}