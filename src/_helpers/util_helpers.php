<?php

/**
 * Loose helper functions in global namespace
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */

/**
 * tests if we are in laravel debug mode.
 */
function yk_is_debug()
{
    return \Cccisd\Util\Debug::envIsDebug();
}

/**
 * variable dumper
 */
function prnt()
{
    if(yk_is_debug())
    {
        call_user_func_array('\Cccisd\Util\Debug::prnt', func_get_args());
    }
}

/**
 * variable dumper then die;
 */
function prntd()
{
    if(yk_is_debug())
    {
        call_user_func_array('\Cccisd\Util\Debug::prntd', func_get_args());
    }
}

/**
 * gets a reflection class
 * @param object|string $obj
 * @return \ReflectionClass
 * @throws \InvalidArgumentException if input is not a loadable object.
 */
function yk_reflect($obj)
{
    return \Cccisd\Util\Variable::getReflectionClass($obj);
}

/**
 * gets a reflection class
 * @param mixed $callable will a
 * @return \ReflectionFunction
 * @throws \InvalidArgumentException if input is not a loadable object.
 */
function yk_reflect_func($callable)
{
    return \Cccisd\Util\Variable::getReflectionFunction($callable);
}

/**
 * merges a param array onto a defaults array
 * @param type $params key=>value associative input
 * @param type $defaults default values.
 * @param bool $strict if true params will be limited to keys present in defaults.
 * @return array
 */
function yk_parseopt($params, $defaults = array(), $strict = true)
{
    return \Cccisd\Util\Variable::parseopt($params, $defaults, $strict);
}

/**
 * generate the expected service provider class name from package name.
 * @param string $packageName
 * @return string fully qualified class name of the service provider
 */
function yk_package_get_serviceprovider_class($packageName)
{
    return \Cccisd\Util\Package::getPackageServiceProviderClassName($packageName);
}

/**
 * generate the expected root namespace from package name.
 * @param string $packageName
 * @return string namespace of package.
 */
function yk_package_get_namespace($packageName)
{
    return \Cccisd\Util\Package::getPackageNamespace($packageName);
}

/**
 * gets an instance of the Laravel NamespacedItemResolver to parse a namespaced item.
 * 
 * The return array is most useful when used with the list() language construct
 * 
 * @see \Illuminate\Support\NamespacedItemResolver
 * @param string $item 
 * @return array with 3 components namespace, group and item 
 */
function yk_resolve_namespaced($item)
{
    return \Cccisd\Util\Single\NamespacedItemResolver::instance()->parseKey($item);
}

/**
 * shortcut to resource uri resolver.
 * @param string $def vendor/package::path/to/file.ext
 * @return string url.
 */
function yk_resource_url($def)
{
    return \Ketwaroo\LaravelFrameworkUtil\ResourceUri\ResourceUri::instance()
                    ->resolveResourceUri($def)
                    ->getResolvedUrl();
}

/**
 * 
 * @see Cccisd\Util\Variable::parseUrl()
 * @param type $url
 * @return type
 */
function yk_parse_url($url)
{
    return \Cccisd\Util\Variable::parseUrl($url);
}

/**
 * combination of array_merge recursive and array_replace_recursive
 * merges if the values in first are arrays, replaces if not.
 * 
 * @todo variable number of params
 * @param array $first
 * @param array $second
 * @return array
 */
function yk_array_fuse_recursive($first, $second)
{
    if(is_array($second))
    {
        foreach($second as $k => $v)
        {
            if(array_key_exists($k, $first))
            {
                if(!is_array($v))
                {
                    $first[$k] = $second[$k];
                }
                else
                {
                    $first[$k] = yk_array_fuse_recursive($first[$k], $v);
                }
            }
            else
            {
                $first[$k]=$v;
            }
        }
    }
    else
    {
        return $second;
    }

    return $first;
}
