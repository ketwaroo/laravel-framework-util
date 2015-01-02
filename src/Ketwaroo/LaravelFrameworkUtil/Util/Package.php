<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Util;

/**
 * Description of Helpers
 * @todo make facade possibly
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class Package
{

    protected static $locatorCache = array();

    /**
     * tries to figure out in which package we are from laravel directory structure convention.
     * 
     * @param string $iAmHere required; __DIR__ or __FILE__ constant supplied where the function is called.
     * @return string vendor/package
     */
    public static function inWhichPackageAmI($iAmHere)
    {
        $basePath = realpath(base_path());

        $iAmHere  = File::unixifyPath($iAmHere);
        $basePath = File::unixifyPath($basePath);

        $iAmHere = str_replace(array(
            $basePath . '/', // remove basepath
            'workbench/',
            'vendor/',
            'app/config/packages', // published configs            
                ), '', $iAmHere);

        list($vendor, $package) = explode('/', $iAmHere, 3);

        $packageName = $vendor . '/' . $package;

        return $packageName;
    }

    /**
     * tests if a package can be loaded.
     * @param string $packageName vendor/package
     * @return boolean
     */
    public static function isPackageAvailable($packageName)
    {
        $packageClass = static::getPackageServiceProviderClassName($packageName);
        return class_exists($packageClass);
    }

    /**
     * get the package namespace name.
     * @param string $packageName
     * @return string
     */
    public static function getPackageNamespace($packageName)
    {
        $packageClass = static::getPackageServiceProviderClassName($packageName);
        // make sure the namespace is absolute.
        return trim('\\' . yk_reflect($packageClass)->getNamespaceName(), '\\');
    }

    /**
     * attempts to detect base path of a package.
     * @param string $packageName vendor/package
     * @return string|boolean false if can't be detected..
     */
    public static function detectPackageBasePath($packageName)
    {
        try
        {
            $packageClass = static::getPackageServiceProviderClassName($packageName);

            $path = yk_reflect($packageClass)->getFileName();

            $path = File::unixifyPath($path);

            if(strpos($path, $packageName) !== false)
            {
                return substr($path, 0, (strpos($path, $packageName) + strlen($packageName))); // close enough
            }
            else
            {
                return false;
            }
        }
        catch(Exception $e)
        {
            //@todo maybe log exception
            return false;
        }
    }

    /**
     * gets the full class name for a package service provider
     * @param string $packageName vendor/package format
     * @return string
     */
    public static function getPackageServiceProviderClassName($packageName)
    {
        // @todo validate input string a bit
        $pieces = explode('/', $packageName);

        $pieces[] = $pieces[1] . ' Service Provider';

        return implode('\\', array_map('studly_case', $pieces));
    }

    /**
     * 
     * @param string $packageName vendor/package
     * @return \Illuminate\Support\ServiceProvider|\Ketwaroo\LaravelFrameworkUtil\ServiceProvider\ServiceProviderAbstract|null
     * @throws \InvalidArgumentException
     */
    public static function getServiceProviderByPackageName($packageName)
    {

        $classPath  = static::getPackageServiceProviderClassName($packageName);
        $registered = static::loadServiceProviderByClassPath($classPath);

        if(($registered instanceof \Illuminate\Support\ServiceProvider))
        {
            return $registered;
        }

        throw new \InvalidArgumentException('Package ' . $packageName . ' is not a valid laravel package');
    }

    public static function loadServiceProvider($packageName)
    {
        $cls = static::getPackageServiceProviderClassName($packageName);
        return static::loadServiceProviderByClassPath($cls);
    }

    /**
     * handles registering and binsing of deferred service providers
     * @return \Illuminate\Support\ServiceProvider 
     */
    public static function loadServiceProviderByClassPath($providerClassPath)
    {
        $app = app();

        $providers = $app->getLoadedProviders();

        if(!array_key_exists($providerClassPath, $providers))
        {
            $app->register($providerClassPath);
        }

        $provider = $app->getRegistered($providerClassPath);

        if($provider instanceof \Illuminate\Support\ServiceProvider)
        {
            return $provider;
        }
    }

    /**
     * test if the supplied string matches the vendor/package::stuff format
     * which would allow it to be parsed by namespace parser
     * @param string $def
     * @return boolean
     */
    public static function isPackageNamespaceString($def)
    {
        return (preg_match('~^[a-z0-9\-]+/[a-z0-9\-]+\:\:.*?~', (string) $def)) ? true : false;
    }
    
    
    /**
     * get the relative
     * @param string $def namespaced string vendor/package::path/to/file.ext
     * @return boolean|string
     */
    public static function getPackagedFileSubPath($def)
    {
        if(!(static::isPackageNamespaceString($def)))
        {
            return false;
        }

        list($packageName, $path) = static::sanitisePackagePathDefinition($def);

        return rtrim("/{$packageName}{$path}", '/');
    }

    /**
     * 
     * @param string $def namespaced string vendor/package::path/to/file.ext
     * @return boolean|string
     */
    public static function getPublishedFileSubPath($def)
    {
        $file = static::getPackagedFileSubPath($def);
        return empty($file) ? false : "/packages{$file}";
    }

    /**
     * 
     * @param string vendor/package::patt/to/file.extension
     * @return type
     */
    public static function detectPackageAssetPath($def)
    {
        $pub = static::getPublishedAssetPath($def);

        return is_file($pub) ? $pub : static::getPackagedAssetPath($def);
    }



    /**
     * attempts to get path to a *published* asset file.
     * 
     * @param string $def vendor/package::path/to/file.extension
     * @return string|boolean
     */
    public static function getPublishedAssetPath($def, $mock = true)
    {
        if(file_exists($def)) // so it detects directories too.
        {
            return $def;
        }

        $file = static::getPublishedFileSubPath($def);

        if(empty($file))
        {
            return false;
        }

        $file = public_path($file);

        if($mock)
        {
            return $file;
        }
        elseif(is_file($file))
        {
            return $file;
        }
        return false;
    }

    /**
     * attempts to get path to a file located within a package.
     * 
     * 
     * @param string $def vendor/package::path/to/file.extension
     * @todo refactor with the rest of the path detection functions
     * @return string|boolean
     */
    public static function getPackageFilePath($def, $mock = true)
    {
        if(file_exists($def)) // mah well
        {
            return $def;
        }

        list($packageName, $path) = static::sanitisePackagePathDefinition($def);

        $basePath = static::detectPackageBasePath($packageName);

        $f = "{$basePath}{$path}";

        if($mock)
        {
            return $f;
        }

        return file_exists($f) ? $f : FALSE;
    }

    /**
     * attempts to get path to a *UNpublished* asset file.
     * 
     * @param string $def vendor/package::path/to/file.extension
     * @return string|boolean
     */
    public static function getPackagedAssetPath($def)
    {
        if(file_exists($def))
        {
            return $def;
        }

        if(!(static::isPackageNamespaceString($def)))
        {
            return false;
        }

        list($packageName, $path) = static::sanitisePackagePathDefinition($def);

        $pubfile = static::detectPackageBasePath($packageName) . "/public{$path}";

        return file_exists($pubfile) ? $pubfile : false;
    }

    public static function detectPackageAssetUrl($def)
    {
        $pub = static::getPackageAssetUrl($def, NULL, false);

        return $pub ? $pub : static::getUnpublishedAssetUrl($def);
    }

    /**
     * attempts to generate url to a published asset for a package
     * @param string $def vendor/package::path/to/file.extension
     * @return string|boolean
     */
    public static function getPackageAssetUrl($def, $secure = NULL, $mock = true)
    {
        if(filter_var($def, FILTER_VALIDATE_URL)) // already url
        {
            return $def;
        }

        $test = static::getPublishedAssetPath($def, $mock);
        if(empty($test))
        {
            return false;
        }

        $subfile = static::getPublishedFileSubPath($def);

        return empty($subfile) ? false : asset($subfile, $secure);
    }

    /**
     * attempts to generate url to a unbublished asset for a package
     * uses route to generate url.
     * @param string $def vendor/package::path/to/file.extension
     * @return string|boolean
     */
    public static function getUnpublishedAssetUrl($def)
    {
        if(!(static::isPackageNamespaceString($def)))
        {
            return false;
        }

        list($packageName, $file) = static::sanitisePackagePathDefinition($def);

        list($vendor, $package) = explode('/', $packageName);

        $file = trim($file, '/');

        //let's try this then.
        return route(\Ketwaroo\LaravelFrameworkUtil\Constant::CONFIGKEY_DEV_ASSET_ROUTE, compact('vendor', 'package', 'file'));
    }

    /**
     * 
     * @param string $class
     * @return Package\Locator
     */
    public static function getPackageLocator($class)
    {
        if(!isset(static::$locatorCache[$class]))
        {
            static::$locatorCache[$class] = app()->make(__CLASS__ . '\\Locator', array(
                'class' => $class,
            ));
        }

        return static::$locatorCache[$class];
    }

    /**
     * recudes a namespaced
     * @param string $def vendor/pacage::path/to/file.ext
     * @return array [$packageName,$path]
     */
    public static function sanitisePackagePathDefinition($def)
    {
        list($packageName, $path, $ext) = yk_resolve_namespaced($def);

        // fix the namespaced thing whendealing with only package name.
        // otherwise seems to behave fine.
        if(!isset($packageName) && !isset($ext) && isset($path))
        {
            $packageName = $path;
            $path        = NULL;
        }

        if(!empty($path))
        {
            $path = '/' . ltrim($path, '/');
        }

        if(!empty($ext))
        {
            $ext = '.' . $ext;
        }

        return array($packageName, "{$path}{$ext}");
    }

}
