<?php

namespace Container;

class Container
{
    private static $instances = [];

    private static $methods = [];
    private static $container = [];

    public function __construct()
    {
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): Container
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    public static function set($name, $value)
    {
        if (self::has($name)) {
            throw new \Exception("Cannot override exists `{$name}` key.");
        }

        self::$container[$name] = $value;
    }

    public static function get($name, $default = null)
    {
        if (self::has($name)) {
            return self::$container[$name];
        }

        return $default;
    }

    public static function has($name): bool
    {
        return array_key_exists($name, self::$container);
    }

    public static function remove($name): void
    {
        if (self::has($name)) {
            unset(self::$container[$name]);
        }
    }

    public static function clear(): void
    {
        self::$container = [];
    }

    public static function map(string $method, $func): void
    {
        if (self::methodExists($method)) {
            throw new \Exception("Cannot override an existing `{$method}` method.");
        }

        self::$methods[$method] = $func;
    }

    public static function mapOnce(string $method, $func): void
    {
        if (self::methodExists($method)) {
            throw new \Exception("Cannot override an existing `{$method}` method.");
        }

        self::$methods[$method] = is_callable($func) ? call_user_func($func) : $func;
    }

    private static function methodExists($method) 
    {
        return method_exists(self::class, $method) || array_key_exists($method, self::$methods) ? true : false;
    }

    public function __call($method, $args)
    {
        $value = self::$methods[$method];
        return is_callable($value) ? call_user_func_array($value, $args) : $value;
    }

    public static function __callStatic($method, $args)
    {
        $value = self::$methods[$method];
        return is_callable($value) ? call_user_func_array($value, $args) : $value;
    }

    public function __get($name)
    {
        return self::get($name);
    }

    public function __set($name, $value)
    {
        return self::set($name, $value);
    }

    public function __isset($name)
    {
        return self::has($name);
    }

    public function __unset($name)
    {
        self::remove($name);
    }
}
