<?php

//
// Oussama Elgoumri
// contact@sec4ar.com
//
// Thu Mar  2 19:12:12 WET 2017
//

use OussamaElgoumri\Config\Config;

if (!function_exists('config')) {
    /**
     * Get and set configuration values.
     *
     * @param string    $path
     * @param mixed     $value
     *
     * @return mixed
     */
    function config($path, $value = '') 
    {
        $inst = Config::getInstance();

        if (!empty($value)) {
            $inst->set($path, $value);
        } else {
            return $inst->get($path);
        }
    }
}

if (!function_exists('Config__load')) {
    /**
     * Load configuration.
     *
     * @param string    $filename
     * @param array     $defaults
     */
    function Config__load($filename = '', $defaults = [])
    {
        Config::getInstance()->load($filename, $defaults);
    }
}

if (!function_exists('Config__get')) {
    /**
     * Get value from the configuration.
     *
     * @param  string    $path
     * @return mixed
     */
    function Config__get($path)
    {
        return Config::getInstance()->get($path);
    }
}

if (!function_exists('Config__set')) {
    /**
     * Set configuration value.
     *
     * @param string    $path
     * @param mixed     $value
     */
    function Config__set($path, $value)
    {
        Config::getInstance()->set($path, $value);
    }
}
