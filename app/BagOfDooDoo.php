<?php


namespace Bot;


class BagOfDooDoo
{
    public static $classes;

    public static function make(string $class)
    {
        if ($concrete = isset(self::$classes[$class])) {
            if ($concrete instanceof \Closure) {
                // assuming it doesn't have any dependencies /shrug
                // reflection and all that is for nerds
                return $concrete();
            }

            return $concrete;
        }
    }

    /**
     * Register a shitty class bruh
     * @param string $class
     * @param $concrete |Closure|Class
     */
    public static function register(string $class, $concrete)
    {
        self::$classes[$class] = $concrete;
    }

}