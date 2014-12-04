<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\ServiceProvider;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Cccisd\Util\Package;

/**
 * Description of ServiceProviderAbstract
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */
abstract class ServiceProviderAbstract extends LaravelServiceProvider
{

    protected $_packageFullName
            , $_vendorName
            , $_packageName
            , $_packageNamespaceName
            , $_packageBasePath

    ;

    const DEFAULT_CONFIG_FILE = 'config';

    public function boot()
    {
        parent::boot();

        $this->_detectPackage();

        $this->package($this->getPackageName(), $this->getPackageName());

        $baseDir = Package::detectPackageBasePath($this->getPackageName());

        // register custom routes
        if(is_file($baseDir . '/src/routes.php'))
        {
            require_once $baseDir . '/src/routes.php';
        }

        // add view location.+ namespaced
        if(is_dir($baseDir . '/src/views'))
        {

            $viewLocations = [
                app_path('views' . Package::getPublishedFileSubPath(rtrim('/', $this->getPackageName() . '::'))), // try published first.
                $baseDir . '/src/views',
            ];

            foreach($viewLocations as $viewLocation)
            {
                \View::addLocation($viewLocation);
            }
            \View::addNamespace($this->getPackageName(), $viewLocations); // for loading view specific to a package.
        }

        $this->bootstrapEvents();
    }

    /**
     * @return string package 
     */
    public function getVendorName()
    {

        return $this->_vendorName;
    }

    /**
     * @return string package 
     */
    public function getPackageSubName()
    {

        return $this->_packageName;
    }

    /**
     * @return string vendor/package format
     */
    public function getPackageName()
    {

        return $this->_packageFullName;
    }

    /**
     * 
     * @return string Vendor\Package
     */
    public function getPackageNamespaceName()
    {
        return $this->_packageNamespaceName;
    }

    /**
     * 
     * @return string
     */
    public function getPackageBasePath()
    {
        return $this->_packageBasePath;
    }

    /**
     * registers things that happen at boot.
     */
    public function bootstrapEvents()
    {
        
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    protected function _detectPackage()
    {

        $this->_packageFullName      = Package::inWhichPackageAmI(ccc_reflect($this)->getFileName());
        $this->_packageNamespaceName = ccc_reflect($this)->getNamespaceName();
        list($this->_vendorName, $this->_packageName) = explode('/', $this->_packageFullName);

        $this->_packageBasePath = Package::detectPackageBasePath($this->_packageFullName);
    }

    /**
     * 
     * @param string $key
     * @param mixed $default optional
     * @param string $file file to read from. defaults to 'config' 
     * @return mixed
     */
    public function readPackageConfig($key, $default = NUll)
    {
        $key = $this->sanitiseConfigKey($key);
        return \Config::get($key, $default);
    }

    /**
     * read the `local` instance of the file
     * @param string $key
     * @param mixed $default optional
     * @param string $file file to read from. defaults to 'config' 
     * @return mixed
     */
    public function readPackageInstanceConfig($key, $default = NUll)
    {
        if(
                ($r      = \Route::current()) && ($prefix = $r->getPrefix())
        )
        {

            $prefix = trim($prefix, '/');

            list($packageName, $file, $localkey) = $this->sanitiseConfigKey($key, true);

            if(!empty($prefix))
            {
                $prefix .= '.';
            }

            $localkey = "{$packageName}::{$prefix}{$file}.{$localkey}";

            $local = $this->readPackageConfig($localkey, $default);

            if(!is_null($local))
            {
                return $local;
            }
        }

        return $this->readPackageConfig($key, $default);
    }

    /**
     * 
     * if returnParts is true, returns array with 3 parts
     *  (packageName,configfile,key[.subkey[.subsubkey]])
     * else return string
     *  vendor/package::configfile.key[.subkey[.subsubkey]]
     * 
     * @param string $key
     * @param boolean $returnParts
     * @return string|array
     */
    public function sanitiseConfigKey($key, $returnParts = false)
    {
        list($packageName, $file, $localkey) = ccc_resolve_namespaced($key);

        if(empty($packageName))
        {
            $packageName = $this->getPackageName();
        }

        if(is_null($localkey) && !empty($file))
        {
            $localkey = $file;
            $file     = static::DEFAULT_CONFIG_FILE;
        }

        if(!$returnParts)
        {
            return "{$packageName}::{$file}.{$localkey}";
        }

        return array(
            $packageName,
            $file,
            $localkey,
        );
    }

}
