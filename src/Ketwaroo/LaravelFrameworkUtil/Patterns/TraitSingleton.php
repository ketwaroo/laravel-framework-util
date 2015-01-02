<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Patterns;

/**
 * More generic way to hold a single instance at class level.
 * This trait was written mostly to get used to explore a different way of doing things.
 * laravel framework may offer similar features.
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
trait TraitSingleton
{

    /**
     * tracks if we have an instance.
     * 
     * @var boolean|mixed
     */
    protected static $_instances = [];

    /**
     * Function used to mock singleton isntance mostly for unit testing.
     * 
     * @param string $newClass class name to replace the instance with.
     * @return object
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public static function replaceSingletonInstance($newClass)
    {
        $args = func_get_args();
        array_shift($args);
        $k    = static::makeInstanceArgHashKey($args);

        if(empty(self::$_instances[$k]))
        {
            throw new Exception('Singleton does not exist, call ::instance() first');
        }

        $callee = get_called_class();

        if(!is_a($newClass, $callee, true))
        {
            throw new \InvalidArgumentException('The injected class must be of same type as or descendant of `' . $callee . '`.');
        }

        self::$_instances[$k] = $newClass;
        return self::$_instances[$k];
    }

    protected static function makeInstanceArgHashKey($args = array())
    {
        if(!empty($args))
        {
            return get_called_class() . '_' . md5(json_encode($args));
        }
        else
        {
            return get_called_class() . '_default';
        }
    }

    /**
     * method should call self::getInstance()
     * php doc should return the correct class.
     */
    public static function instance()
    {
        throw new \Exception('This method `' . __METHOD__ . '` must be implemented by ' . get_called_class()
        . 'and call self::getInstance(). PHPDoc should reflect correct @return type.');
    }

    /**
     * return instance of class
     * @return __CLASS__
     */
    protected static function getInstance()
    {
        $callee = get_called_class();

        $args = func_get_args();

        $k = static::makeInstanceArgHashKey($args);

        if(!isset(self::$_instances[$k]) or ! is_a(self::$_instances[$k], $callee, true))
        {
            // \App::singleton($callee); // hmm..
            self::$_instances[$k] = \App::make($callee, $args);
        }

        return self::$_instances[$k];
    }

    public static function resetInjectedSigletonInstance()
    {
        self::$_instances = [];
        return self::instance();
    }

}
