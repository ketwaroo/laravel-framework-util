<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Util;

/**
 * Description of XML
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */
class XML
{

    /**
     * attempts to convert an associative array to a "xml" tag string.
     * 
     * @param array $data
     * @return string
     */
    public static function arrayToXMLTags(array $data)
    {
        $xml = '';
        foreach($data as $k => $v)
        {
            if(is_array($v))
            {
                $xml .= call_user_func(__METHOD__, $v); // recurse
            }
            else
            {
                if(is_null($v))
                {
                    $v = 'NULL';
                }
                $xml.= Html::buildTag($k, strval($v));
            }
        }
        return $xml;
    }

    /**
     * 
     * @param string $file
     * @return Xml\SimplerDOM
     * @throws Exception
     */
    public static function readFile($file)
    {
        if(!is_file($file))
            throw new Exception('Could not load xml ' . $file);

        return static::fromString(file_get_contents($file));
    }

    public static function fromString($xmlString)
    {
        return new Xml\SimplerDOM($xmlString, LIBXML_COMPACT | LIBXML_NOEMPTYTAG);
    }

}
