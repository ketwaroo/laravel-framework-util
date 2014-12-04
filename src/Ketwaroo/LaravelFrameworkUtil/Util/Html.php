<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Util;

/**
 * CMS HTML class.
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 * @package cccisd
 * @subpackage tools
 */
class Html
{

    /**
     * builds an a tag
     * @param string $url
     * @param string $title
     * @param array $attribs
     * @return string
     */
    public static function buildLink($url, $title = '', $attribs = array())
    {
        if(empty($title))
        {
            $title = $url;
        }

        $defaults = array(
            'title' => $title,
            'href'  => $url,
        );

        $attribs = CMS_Utils::parseopt($attribs, $defaults, false);

        return self::buildTag('a', $title, $attribs);
    }

    /**
     * converts key=.value pairs to html tag attributes.
     * @param array $attribs
     * @return string
     */
    public static function buildHTMLAttribs($attribs = array())
    {
        if(empty($attribs) or ! is_array($attribs))
        {
            return '';
        }

        $tmp = array();

        foreach($attribs as $k => $v)
        {
            $tmp[] = $k . '="' . self::encode($v) . '"';
        }

        return ' ' . implode(' ', $tmp);
    }

    /**
     * builds css style string.
     * @param array $attribs
     * @param boolean $encode
     * @return string
     */
    public static function buildCSSAttribs($attribs = array(), $encode = true)
    {
        if(empty($attribs) or ! is_array($attribs))
        {
            return '';
        }

        $tmp = array();

        foreach($attribs as $k => $v)
        {
            $tmp[] = strval($k) . ':' . strval($v) . '';
        }

        if($encode)
        {
            return self::encode(implode(';', $tmp));
        }
        else
        {
            return implode(';', $tmp);
        }
    }

    public static function buildCssTag($src, $media = 'all', $attribs = [])
    {

        return static::buildTag('link', '', yk_parseopt($attribs, [
                    'media' => 'all',
                    'href'  => $src,
                    'type'  => 'text/css',
                    'rel'   => 'stylesheet',
                                ], false), false, true);
    }

    public static function buildScriptTag($src, $attribs = [])
    {
        return static::buildTag('script', '', yk_parseopt($attribs, [
                    'src' => $src,
                                ], false), false, false);
    }

    /**
     * converts key=>value arry into html5 data-* attribs.
     * @param array $attribs
     * @return string
     */
    public static function html5DataAttribs($attribs)
    {
        if(empty($attribs) or ! is_array($attribs))
        {
            return '';
        }

        $tmp = array();

        foreach($attribs as $k => $v)
        {
            $tmp['data-' . CMS_Text::toLowerDash($k)] = $v;
        }

        return self::buildHTMLAttribs($tmp);
    }

    /**
     * encode string using html entities
     * @param string $string
     * @param string $encoding charset to use
     * @return string
     */
    public static function encode($string, $encoding = 'UTF-8')
    {
        return htmlentities($string, ENT_QUOTES, $encoding);
    }

    /**
     * decode html entities.
     * @param string $string
     * @param string $encoding charset to use
     * @return string
     */
    public static function decode($string, $encoding = 'UTF-8')
    {
        return html_entity_decode($string, ENT_QUOTES, $encoding);
    }

    /**
     * generic html tag builder.
     * @param string $tag
     * @param string $content
     * @param array $attribs
     * @param bool $htmlContent if true, content will not be encoded.
     * @param bool $allowShortTag if true, empty content will be displayed as a sort tag.
     * @return string
     */
    public static function buildTag($tag, $content = '', $attribs = array(), $htmlContent = false, $allowShortTag = false)
    {
        $attribs = self::buildHTMLAttribs($attribs);

        if(strlen($content) === 0 && $allowShortTag)
        {
            return "<{$tag}{$attribs} />";
        }
        else
        {
            if(!$htmlContent)
            {
                $content = self::encode($content);
            }

            return "<{$tag}{$attribs}>{$content}</{$tag}>";
        }
    }

    /**
     * simple method to protect email (or other text) from bot scanning.
     * @param string $email
     * @param int $density unsigned int. level of onbfuscation, lower int = higher protection
     * @return string
     */
    public static function protectEmailAgainstBots($email, $density = 4)
    {
        if($density < 1)
        {
            $density = 1;
        }
        return implode('<b style="display:none">aye! avast!</b>', str_split($email, intval($density)));
    }

    /**
     * Useful when embedding files within an html link.
     * @see http://tools.ietf.org/html/rfc2397
     * @see http://en.wikipedia.org/wiki/Data_URI_scheme#Web_browser_support
     * @param type $data
     * @param type $mime
     * @return string encoded data for use in a link
     */
    public static function createDataURI($data, $mime = 'text/plain')
    {
        return 'data:' . self::encode($mime) . ';base64,' . base64_encode($data);
    }

    /**
     * reads a file and returns a data URI
     * @param string $filePath
     * @return string|boolean data URII or false if failed to read file.
     */
    public static function createDataURIFromFile($filePath)
    {
        if(!is_readable($filePath))
        {
            return false;
        }

        $mime = CMS_Utils_File::determineMime($filePath);

        return self::createDataURI(file_get_contents($filePath), $mime);
    }

}
