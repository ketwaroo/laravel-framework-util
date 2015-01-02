<?php

/**
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */

namespace Ketwaroo\LaravelFrameworkUtil\Package;

use Ketwaroo\LaravelFrameworkUtil\Package\Package;

/**
 * Description of Locator
 *
 * @author "Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>"
 */
class Locator
{

    /**
     *
     * @var \Illuminate\Support\ServiceProvider 
     */
    protected $_detectedPackageServiceProvider = NULL;
    protected $_detectedPackage                = NULL;
    protected $parserAssetPaths                = array();

    public function __construct($class)
    {
        $packageName = Package::inWhichPackageAmI(yk_reflect($class)->getFileName()); // should get first user of the trait.

        $this->setDetectedPackage($packageName)
                ->setDetectedPackageServiceProvider(Package::getServiceProviderByPackageName($packageName));
    }

    public function getDetectedPackageServiceProvider()
    {
        return $this->_detectedPackageServiceProvider;
    }

    public function getDetectedPackage()
    {
        return $this->_detectedPackage;
    }

    protected function setDetectedPackageServiceProvider(\Illuminate\Support\ServiceProvider $detectedPackageServiceProvider)
    {
        $this->_detectedPackageServiceProvider = $detectedPackageServiceProvider;
        return $this;
    }

    protected function setDetectedPackage($detectedPackage)
    {
        $this->_detectedPackage = $detectedPackage;
        return $this;
    }

    /**
     * get the filesystem path of a file within the assets public folder.
     * @param string $path path of file within public folder
     * @return string|boolean
     */
    public function getPackageAssetPath($path)
    {
        return Package::detectPackageAssetPath($this->getDetectedPackage() . '::' . $path);
    }

    /**
     * 
     * @param type $path
     * @return string|boolean
     */
    public function getPackageAssetUrl($path)
    {
        return Package::detectPackageAssetUrl($this->getDetectedPackage() . '::' . $path);
    }

    /**
     * 
     * @return type
     */
    public function getPackageBasepath()
    {
        return Package::detectPackageBasePath($this->getDetectedPackage());
    }

    /**
     * 
     * @param type $file
     * @return type
     */
    public function getPackageFilePath($file = '')
    {
        return Package::getPackageFilePath($this->getDetectedPackage() . '::' . $file);
    }

}
