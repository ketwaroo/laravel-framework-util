<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Util;

/**
 * File utils
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 * @package cccisd/util
 */
class File
{

    /**
     * cached mime by extension map.
     * @var array 
     */
    protected static $extMimeMap = array();

    /**
     * converts bacslash into forward slash in a path because php does not care.
     * and it makes path handling uniform cross platform
     * @param string $path
     * @return string
     */
    public static function unixifyPath($path)
    {
        return preg_replace('~[\\\/]+~', '/', $path);
    }

    /**
     * attempts to determine mime for a file.
     * 
     * Note that the fileinfo methods are not that accurate.
     * 
     * @param string $file can be existing or non existing file.
     * @param string $default fallback mime if can't be determined. default: application/octet-stream
     * @return type
     */
    public static function determineMime($file, $default = 'application/octet-stream')
    {

        $basePath = Package::detectPackageBasePath(Package::inWhichPackageAmI(__FILE__));

        if(is_file($file) && function_exists('finfo_open')) // recommended way.
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type a la mimetype extension
            $mime  = finfo_file($finfo, $file);
            finfo_close($finfo);
        }
//        elseif(is_file($file) && function_exists('mime_content_type')) // deprecated way
//        {
//            $mime = @mime_content_type($file);
//        }
        else // scraping the barrel. Also works if file does not exist.
        { //@todo this method may be more accurate than fileinfo.
            if(empty(self::$extMimeMap))
            {
                self::$extMimeMap = require($basePath . '/data/mime_by_extension.php');
            }

            $ext = pathinfo($file, PATHINFO_EXTENSION);

            $mime = isset(self::$extMimeMap[$ext]) ? reset(self::$extMimeMap[$ext]) : null;
        }
        return empty($mime) ? $default : $mime;
    }

    /**
     * updates /data/mime-types with data from http://svn.apache.org/viewvc/httpd/httpd/trunk/docs/conf/mime.types?view=co
     * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
     */
    public static function refreshMimeMap()
    {
        $basePath = Package::detectPackageBasePath(Package::inWhichPackageAmI(__FILE__));

        $src     = 'http://svn.apache.org/viewvc/httpd/httpd/trunk/docs/conf/mime.types?view=co';
        $outfile = $basePath . '/data/mime_by_extension.php';

        $raw = explode("\n", file_get_contents($src));

        $out   = array();
        $count = array();

        foreach($raw as $r)
        {
            $r = trim($r);
            if(substr($r, 0, 1) === '#' || empty($r))
            {
                continue;
            }

            $a = preg_split('~\t+~', $r);

            if(!empty($a[1]))
            {
                $exts = explode(' ', $a[1]);
                $mime = trim($a[0]);
                foreach($exts as $e)
                {
                    if(isset($out[$e]))
                    {
                        $out[$e][] = $mime;
                        $count[$e] ++;
                    }
                    else
                    {
                        $out[$e] = array(
                            $mime,
                        );

                        $count[$e] = 1;
                    }
                }
            }
        }

        ksort($out);

        $outFileContent = file_get_contents($outfile);

        $outFileContent = preg_replace('~#mapStart.*?#mapEnd~is', '#mapStart' . PHP_EOL . 'return ' . var_export($out, true) . ';' . PHP_EOL . '#mapEnd', $outFileContent);

        file_put_contents($outfile, $outFileContent);

        prnt($out, $count);
    }

}
