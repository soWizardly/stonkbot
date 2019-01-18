<?php

namespace App\Facades;


class Container
{

    /**
     * @var \Pimple\Container
     */
    protected static $container;

    /**
     * @param \Pimple\Container $container
     */
    public static function setContainer(\Pimple\Container &$container)
    {
        static::$container = $container;
    }

    /**
     * @param $class
     * @return mixed
     */
    public static function resolve($class)
    {
        // TODO(vulski): Class reflection to find dependencies for the class being resolved.
//        if (class_exists($class)) {
//            $reflection = new \ReflectionClass($class);
//            $construct = $reflection->getConstructor();
//            dd($construct);
//        }

        return static::$container[$class];
    }

    public static function has($class)
    {
        return isset(static::$container [$class]);
    }
}