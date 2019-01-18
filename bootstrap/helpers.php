<?php
if (!function_exists('dd')) {
    function dd(...$vars)
    {
        var_dump($vars);
        die;
    }
}

if (!function_exists('resolve')) {
    function resolve($class)
    {
        return \App\Facades\Container::resolve($class);
    }
}

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        $config = \App\Facades\Container::resolve('config');
        if (isset($config[$key])) {
            return $config[$key];
        }
        return $default;
    }
}