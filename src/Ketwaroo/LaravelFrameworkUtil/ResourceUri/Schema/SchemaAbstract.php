<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\ResourceUri\Schema;

/**
 * Description of UriAbstract
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
abstract class SchemaAbstract
{

    protected $location,
            $resolvedPath,
            $resolvedUrl,
            $basePath,
            $baseUrl

    ;

    /**
     * @return  SchemaAbstract
     */
    abstract public function parse();

    /**
     * @return int
     */
    abstract public function getModifiedTime();

    /**
     * @return int
     */
    abstract public function getCreatedTime();

    /**
     * @return int
     */
    abstract public function getSize();

    public function __construct($location)
    {
        $this->location = (string) $location;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getResolvedPath()
    {
        return $this->resolvedPath;
    }

    public function getResolvedUrl()
    {
        return $this->resolvedUrl;
    }

    protected function setResolvedPath($resolvedPath)
    {
        $this->resolvedPath = $resolvedPath;
        return $this;
    }

    protected function setResolvedUrl($resolvedUrl)
    {
        $this->resolvedUrl = $resolvedUrl;
        return $this;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

}
