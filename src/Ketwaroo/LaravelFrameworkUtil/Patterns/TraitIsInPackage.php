<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Patterns;

use \Ketwaroo\LaravelFrameworkUtil\Package as PackageUtil;

/**
 * Description of TraitIsInPackage
 * @experimental
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
trait TraitIsInPackage
{

    /**
     * 
     * @return type
     */
    public static function isInPackage()
    {
        $pak = static::getPackageName();
        return !empty($pak); // should be string or empty
    }

    public static function is3CPackage()
    {
        $pak = static::getPacakgeServiceProviderInstance();

        return $pak instanceof \Ketwaroo\LaravelFrameworkUtil\ServiceProvider\ServiceProviderAbstract;
    }

    /**
     * detect if backage is still in workbench.
     * useful when developing.
     * @return boolean
     */
    public static function isPackageWorkbenched()
    {
        return 0 === strpos(static::getPacakgeServiceProviderInstance()->getPackageBasePath(), base_path() . '/workbench/');
    }

    /**
     * @return \Illuminate\Support\ServiceProvider|\Ketwaroo\LaravelFrameworkUtil\ServiceProvider\ServiceProviderAbstract
     */
    protected static function getPacakgeServiceProviderInstance()
    {
        return PackageUtil::getPackageLocator(__CLASS__)->getDetectedPackageServiceProvider();
    }

    /**
     * 
     * @return string vendor/package
     */
    public static function getPackageName()
    {
        return PackageUtil::getPackageLocator(__CLASS__)->getDetectedPackage();
    }

    /**
     * 
     * @param type $key
     * @param type $default
     * @return null
     */
    protected static function readPackageInstanceConfig($key, $default = NULL)
    {
        if(!(static::is3CPackage()))
            return NULL;
        return static::getPacakgeServiceProviderInstance()->readPackageInstanceConfig($key, $default);
    }

    /**
     * 
     * @param type $key
     * @param type $default
     * @return null
     */
    protected static function readPackageConfig($key, $default = NULL)
    {
        if(!(static::is3CPackage()))
            return NULL;

        return static::getPacakgeServiceProviderInstance()->readPackageConfig($key, $default);
    }

    /**
     * 
     * @param string $path optional subpath
     * @return string 
     */
    public static function getPackageAssetPath($path = '')
    {
        return PackageUtil::getPackageLocator(__CLASS__)->getPackageAssetPath($path);
    }

    /**
     * 
     * @param string $path optional subpath
     * @return string url
     */
    public static function getPackageAssetUrl($path = '')
    {
        return PackageUtil::getPackageLocator(__CLASS__)->getPackageAssetUrl($path);
    }

    public static function getPackageBasePath()
    {
        return PackageUtil::getPackageLocator(__CLASS__)->getPackageBasepath();
    }

    /**
     * 
     * @param string $path optional subpath
     * @return string url
     */
    public static function getPackageFilePath($path = '')
    {
        return PackageUtil::getPackageLocator(__CLASS__)->getPackageFilePath($path);
    }

}
