<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Migration;

use Illuminate\Database\Migrations\Migration as laravelMigration;

/**
 * Description of Migration
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */
class Migration extends laravelMigration
{

    /**
     * loads and executes an sql file were a _prefix_ exists that can be substituted
     * @param string $file sql file
     * @param string $prefix [a-z0-9]_ needs trailing underscore
     * @param string $prefixPlaceholder default `_prefix_`
     * @return boolean
     */
    protected function importprefixedSQLFile($file, $prefix = '', $prefixPlaceholder = '_prefix_')
    {
        if(empty($prefix))
        {
            // autodetect somehow?
            // use package sub name            
            $class  = get_called_class();
            // get the package in vendor/package
            list(, $package) = explode('/', \Cccisd\Util\Package::inWhichPackageAmI(ccc_reflect($class)->getFileName()));
            $prefix = snake_case($package);
        }

        $sql = $this->replacePrefix(file_get_contents($file), $prefix, $prefixPlaceholder);

        return \DB::unprepared($sql);
    }

    public function replacePrefix($string, $prefix = '', $prefixPlaceholder = '_prefix_')
    {
        return str_replace($prefixPlaceholder, trim($prefix), $string);
    }

    public function sanitiseForTableName($str)
    {
        return preg_replace('~[^a-z0-9]+~', '', strtolower($str));
    }

}
