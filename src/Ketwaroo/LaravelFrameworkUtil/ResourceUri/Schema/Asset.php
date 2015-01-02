<?php

namespace Ketwaroo\LaravelFrameworkUtil\ResourceUri\Schema;

use Ketwaroo\LaravelFrameworkUtil\Package;

/**
 * 
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class Asset extends SchemaAbstract
{

    protected $isparsed = false;

    /**
     * 
     * @return \Ketwaroo\LaravelFrameworkUtil\ResourceUri\Schema\Asset
     * @throws \InvalidArgumentException
     */
    public function parse()
    {
        if($this->isparsed)
            return $this;

        $path = $this->getLocation();

        if((Package::isPackageNamespaceString($path)))
        {
            $this->setResolvedPath(Package::detectPackageAssetPath($path));
            $this->setResolvedUrl(Package::detectPackageAssetUrl($path));
        }
        else
        {
            throw new \InvalidArgumentException("[$path] is not a valid asset definition."); //@message
            // any other uri including pass thru should be parsed by their correct handlers.
        }

        $this->isparsed = true;
        return $this;
    }

    /**
     * 
     * @return int timestamp
     */
    public function getCreatedTime()
    {
        if(!$this->isparsed)
        {
            $this->parse();
        }
        return filectime($this->getResolvedPath());
    }

    /**
     * 
     * @return int timestamp
     */
    public function getModifiedTime()
    {
        if(!$this->isparsed)
        {
            $this->parse();
        }
        return filemtime($this->getResolvedPath());
    }

    /**
     * 
     * @return int bytes
     */
    public function getSize()
    {
        if(!$this->isparsed)
        {
            $this->parse();
        }
        return filesize($this->getResolvedPath());
    }

}
