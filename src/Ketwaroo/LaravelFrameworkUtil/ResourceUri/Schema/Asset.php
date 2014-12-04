<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\ResourceUri\Schema;

use Cccisd\Util\Package as PackageUtil;

/**
 * Description of FileSystem
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */
class Asset extends SchemaAbstract
{

    protected $isparsed = false;

    /**
     * 
     * @param string $path
     * @return \Cccisd\GameResource\ResourceUri\Asset
     */
    public function parse()
    {
        if($this->isparsed)
            return $this;

        $path = $this->getLocation();

        if((PackageUtil::isPackageNamespaceString($path)))
        {
            $this->setResolvedPath(PackageUtil::detectPackageAssetPath($path));
            $this->setResolvedUrl(PackageUtil::detectPackageAssetUrl($path));
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
