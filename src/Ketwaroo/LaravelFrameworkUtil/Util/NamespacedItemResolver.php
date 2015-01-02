<?php

/**
 * 
 */
namespace Ketwaroo\LaravelFrameworkUtil\Util;

use Illuminate\Support\NamespacedItemResolver as LaravelNamespaceResolver;

/**
 * get a singleton instance of Illuminate\Support\NamespacedItemResolver
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class NamespacedItemResolver extends LaravelNamespaceResolver
{

    use \Ketwaroo\LaravelFrameworkUtil\PatternsTraitSingleton;
    

    /**
     * 
     * @return NamespacedItemResolver
     */
    public static function instance()
    {
        return self::getInstance();
    }

}
