<?php




class BagOfDooDoo
{
    public static $classes;

    public static function make(string $class)
    {
        if (isset(static::$classes[$class])) {
            $concrete = static::$classes[$class];
            if ($concrete instanceof \Closure) {
                // assuming it doesn't have any dependencies /shrug
                return $concrete();
            }

            return $concrete;
        }
        return null;
    }

    /**
     * Register a class to the container.
     * @param string $class
     * @param $concrete |Closure|Class
     */
    public static function register(string $class, $concrete)
    {
        static::$classes[$class] = $concrete;
    }

}