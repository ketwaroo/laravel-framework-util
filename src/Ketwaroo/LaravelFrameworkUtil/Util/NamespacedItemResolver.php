<?php

/**
 * @copyright (c) 2014, 3C Institute
 */
namespace Ketwaroo\LaravelFrameworkUtil\Util;

use Illuminate\Support\NamespacedItemResolver as LaravelNamespaceResolver;

/**
 * get a singleton instance of Illuminate\Support\NamespacedItemResolver
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
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
