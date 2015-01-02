<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Xml;

/**
 * Extends SimpleDOM.
 * @TODO Add.. stuff. This is a placeholder for future improvements.
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class SimplerDOM extends SimpleDOM
{

    public function setAttribute($name, $value, $namespace = null)
    {
        if(isset($this[$name]))
        {
            $this[$name] = $value;
        }
        else
        {
            $this->addAttribute($name, $value);
        }
        return $this;
    }

    /**
     * Get all attributes of current element as associative array.
     * @return array 
     */
    public function getAttributes()
    {
        $tmp = (array) $this->attributes();
        return empty($tmp['@attributes']) ? array() : $tmp['@attributes'];
    }

    /**
     * 
     * @param type $paths
     * @return type
     */
    public function matchXPathFirst($paths)
    {

        foreach((array) $paths as $path)
        {
            $test = $this->xpath($path);

            if(empty($test))
            {
                continue;
            }

            return $test; // return first non empty
        }


        return array();
    }

    public function matchXPathAll($paths)
    {
        $path = '(' . implode(') and (', (array) $paths) . ')';

        return $this->xpath($path);
    }

    /**
     * randomly picks one or more matched items.
     * Warning; will return single instance if num entires = 1 and array of entries if greater.
     * 
     * @param string|array $path
     * @param int $numEntries number of entries to pick
     * @return SimplerDOM|array
     */
    public function matchXPathRandom($path, $numEntries = 1)
    {
        $nodes = $this->matchXPathAll($path);

        return array_rand($nodes, $numEntries);
    }
    
    /**
     * If current node was matched by xpath, this function flattens it and removes
     * references to the parent xml document so further xpath can per performed
     * without matching parent elements
     * 
     * @return SimplerDOM
     */
    public function flatten()
    {
        $tmp = $this->asXML();

        return \Ketwaroo\LaravelFrameworkUtil\XML::fromString($tmp);
    }
    /**
     * 
     * @param string $filename
     * @return SimplerDOM
     */
    public static function loadXmlFile($filename)
    {
        return new static(file_get_contents($filename));
    }

}
