<?php

namespace Ketwaroo\LaravelFrameworkUtil\Util;

/**
 * Some sterner text utils than the laravel built-in ones.
 * @package ketwaroo/laravel-framework-util
 * @author Ketwaroo D. Yaasir
 */
class Text
{

    /**
     * converts a bit of text to lower dash form.
     * convert "It's alive!!! non-stick" to "its-alive-non-stick"
     * commonly used for urls
     * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
     * @param type $str
     * @return string lower-dash-string
     */
    public static function toLowerDash($str)
    {
        return strtolower(preg_replace(array(
            '~([a-z])([A-Z])~', // split camel case
            '~[^a-z0-9 ]+~i',
            '~ +~',
                        )
                        , array(
            '\1 $2',
            ' ',
            '-')
                        , self::unaccent((string) $str)));
    }

    /**
     * converts a bit of text to lower snake case form.
     * @param string $str
     * @return string
     */
    public static function toSnakeCase($str)
    {
        return str_replace('-', '_', static::toLowerDash($str));
    }

    /**
     * convert "It's alive!!! non-stick" to "itsAliveNonStick"
     * @param type $str
     * @return string
     */
    public static function toCamelCase($str)
    {
        return lcfirst(self::toUpperCaseJoined($str));
    }

    /**
     * convert "It's alive!!! non-stick" to "ItsAliveNonStick"
     * @param type $str
     * @param string $glu if joined by another string
     * @return type
     */
    public static function toUpperCaseJoined($str, $glu = '')
    {
        return str_replace(' ', $glu, ucwords(str_replace('-', ' ', self::toLowerDash($str))));
    }

    /**
     * Attempts to unaccent a string 
     * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
     * @param $str input string
     * @return string input string without accent
     */
    public static function unaccent($str)
    {
        $encoding = mb_detect_encoding($str);

        return iconv($encoding, 'ASCII//TRANSLIT', $str);
    }

}
