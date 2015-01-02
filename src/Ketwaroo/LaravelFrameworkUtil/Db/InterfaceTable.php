<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Db;

/**
 * PHP 5.3+ will give E_STRICT warnings with abstract static classes. Stupid backward
 * incompatible change but woraround is to have interfaces declare those static methods\
 * and the abstract class "implement" them.
 *
 * @author "Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>"
 */
interface InterfaceTable
{

    /**
     * returns the table prefix. format: [a-z][a-z0-9]_
     * generally unique per package
     * 
     * @return string 
     */
    public static function getTablePrefix();

    /**
     * @return string full table name with prefix.
     */
    public static function getTableName();

    public static function getTableShortName();

    /**
     * @return string
     */
    public static function getPrimaryKeyName();

    /**
     * @return mixed
     */
    public function getPrimaryKeyValue();
}
