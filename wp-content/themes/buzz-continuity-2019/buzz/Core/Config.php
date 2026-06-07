<?php

namespace Firefly\Buzz\Core;

/*
 |--------------------------------------------------------------------------
 | Config
 |--------------------------------------------------------------------------
 |
 | A core file used to bind keys and values to the Theme container. Add to
 | the container using Config::bind($key, $value) then pull out of the
 | container using Config::get($key)
 |
 */

class Config
{

    /**
     * All registered keys.
     *
     * @var array
     */
    protected static $registry = [];

    /**
     * Bind a new key/value into the container.
     *
     * @param  string $key
     * @param  mixed  $value
     */
    public static function bind($key, $value)
    {
        static::$registry[$key] = $value;
    }

    /**
     * Retrieve a value from the registry.
     *
     * @param  string $key
     */
    public static function get($key)
    {
        if (! array_key_exists($key, static::$registry)) {
            throw new \Exception("No {$key} is bound in the container.");
        }
        return static::$registry[$key];
    }

}
