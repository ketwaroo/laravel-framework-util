<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Util;

/**
 * Description of Util
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class Variable
{

    /**
     * GUID generator
     * 
     * @stolen http://guid.us/GUID/PHP
     * @see http://guid.us/GUID/PHP
     * @return string
     */
    public static function GUID()
    {
        if(function_exists('com_create_guid'))
        {
            return com_create_guid();
        }
        else
        {
            //mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid   = chr(123)// "{"
                    . substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12)
                    . chr(125); // "}"
            return $uuid;
        }
    }

    /**
     * safely test existence of a constant and returns it's value.
     * returns null if constant not defined.
     * Warning: will also return null if existing constant is defined as null.
     * @param string $name
     * @return mixed scalar constant value..
     */
    public static function getConstant($name)
    {
        if(defined($name))
            return constant($name);
        return null;
    }

    /**
     * merges a param array onto a defaults array
     * @param type $params key=>value associative input
     * @param type $defaults default values.
     * @param bool $strict if true params will be limited to keys present in defaults.
     * @return array
     */
    public static function parseopt($params, $defaults = array(), $strict = true)
    {
        if($strict)
        {
            $params = array_intersect_key($params, $defaults);
        }
        return array_merge($defaults, $params);
    }

    /**
     * 
     * @see array_filter
     * @param array $inArray
     * @param array $requiredKeys
     * @param callable $filterCallback optional filter to apply to keys present in array.
     * @return array returns missing fields.
     */
    public static function checkRequiredFields($inArray, $requiredKeys, $filterCallback = null)
    {

        // gets what is missing from required once figured out what inArray already
        $tmp = array_diff($requiredKeys, array_intersect(array_keys($inArray), $requiredKeys));

        if(!empty($filterCallback) && is_callable($filterCallback))
        {
            $tmp = array_filter($tmp, $filterCallback);
        }

        return $tmp;
    }

    /**
     * test the truthiness&trade; of a string 
     * @param mixed $var variable to test
     * @return bool truthiness&trade;
     */
    public static function isTruthy($var)
    {

        $var      = strtolower(strval($var));
        $truthies = array(
            '1',
            'true',
            'yes',
            'y',
            'on',
        );

        return in_array($var, $truthies);
    }

    /**
     * 
     * @param mixed $var may not handle objects very well.
     * @return string base64 encoded
     */
    public static function base64Encode($var)
    {
        return base64_encode(json_encode($var));
    }

    /**
     * 
     * @param string $base64EncodedString base64 encoded string
     * @param boolean $assoc Used by json_decode. returns object instead of assoc array if false.
     * @return mixed
     */
    public static function base64Decode($base64EncodedString, $assoc = true)
    {
        return json_decode(base64_decode($base64EncodedString), $assoc);
    }

    /**
     * returns !empty. used for filtering.
     * @param mixed $var
     * @return boolean
     */
    public static function notEmpty($var)
    {
        return !empty($var);
    }

    /**
     * returns !is_null. used for filtering.
     * @param mixed $var
     * @return boolean
     */
    public static function notNull($var)
    {
        return !is_null($var);
    }

    /**
     * extract some data from an array give key(s)
     * can return single value if $keys is scalar.
     * @param mixed $keys
     * @param array $source reference to source array.
     * @return mixed
     */
    public static function extractData(&$source, $keys)
    {
        if(empty($keys))
            return $source;

        if(is_string($keys))
        {
            return isset($source[$keys]) ? $source[$keys] : null;
        }

        return array_intersect_key($source, array_flip((array) $keys));
    }

    /**
     * sets/ merged data with an array.
     * 
     * @param array $target
     * @param array|string $key
     * @param mixed $value
     * @return array
     */
    public static function setData(&$target, $key, $value = NULL)
    {
        if(is_array($key))
        {
            $target = self::parseopt($key, $target, false);
        }
        else
        {
            $target[$key] = $value;
        }
        return $target;
    }

    /**
     * extending the php parse url to handle some unusual cases
     * 
     * <code>
     * moo:///path/to/stuff.file ->     
     * [
     *  'scheme'=>'moo',
     *  'path'=> '/path/to/stuff.file',
     * ]
     * instead of returning false
     * 
     * meow://../relative/path.to -> 
     * [
     *  'scheme'=>'meow',
     *  'path'=> '../relative/path.to',
     * ]
     * instead of returning '..' as the host
     * <code>
     * @param string $url
     * @return array|boolean
     */
    public static function parseUrl($url)
    {
        $tmp = parse_url($url);

        if(empty($tmp)) // fail once could be triple slash
        {
            if(preg_match('~^(([a-z0-9\-]+)://)~', $url, $m))
            {
                $scheme        = $m[2];
                $url           = str_replace($m[1], '', $url);
                $tmp           = parse_url($url);
                // should usually return path
                $tmp['scheme'] = $scheme;
            }
            else
            {
                return $tmp;
            }
        }

        $host = array_get($tmp, 'host', '');

        if(strcmp($host, '..') === 0) // is relative uri
        {
            $tmp['path'] = $host . array_get($tmp, 'path', ''); // it's all really the path.
            unset($tmp['host']);
        }

        return $tmp;
    }

    /**
     * generates a reflection class for an object.
     * @todo add caching possibly.
     * @param object|string $obj
     * @return \Reflector|\ReflectionClass|\ReflectionFunction|null
     */
    public static function getReflectionClass($obj)
    {
        try
        {
            // try first as it can be a class path string.
            return new \ReflectionClass($obj);
        }
        catch(\Exception $exc)
        {
            throw new \InvalidArgumentException('Input is not an loadable object.', __LINE__, $exc);
        }
    }

    /**
     * generates a reflection class for an closure or callable.
     * @todo add caching possibly.
     * @param object|string $obj
     * @return \Reflector|\ReflectionClass|\ReflectionFunction|null
     */
    public static function getReflectionFunction($obj)
    {
        try
        {
            return new \ReflectionFunction($obj);
        }
        catch(\Exception $exc)
        {
            throw new \InvalidArgumentException('Input is not an reflectable function.', __LINE__, $exc);
        }
    }

}
