<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\ResourceUri\Schema;

/**
 * Description of Http
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class Http extends SchemaAbstract
{

    protected $headers = [];

    /**
     * HTTP and HTTPs should just pass through.
     * 
     * @todo Protocol mismatch. Should we catch it here and try to correct.
     * @param string $location the full URL, ideally.
     */
    public function parse()
    {

        $this->setResolvedUrl($this->getLocation())
                ->setResolvedPath($this->getLocation()); // can't really determine path so we use the same.
    }

    public function getCreatedTime()
    {
        return $this->getModifiedTime(); // we don't have created time?
    }

    public function getModifiedTime()
    {
        $headers = $this->getHttpHeaders();
        if(!isset($headers['Last-Modified']))
        {
            return time();
        }
        return strtotime($headers['Last-Modified']);
    }

    public function getSize()
    {
        $headers = $this->getHttpHeaders();
        if(!isset($headers['Content-Length']))
        {
            return 0;
        }
        return intval($headers['Content-Length']);
    }

    protected function getHttpHeaders()
    {
        if(empty($this->headers))
        {
            $this->headers = get_headers($this->getLocation(), 1);
        }
        return $this->headers;
    }

}
