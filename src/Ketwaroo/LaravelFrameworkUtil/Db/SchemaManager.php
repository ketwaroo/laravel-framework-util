<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Db;

/**
 * Description of SchemaManager
 *
 * @author "Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>"
 */
class SchemaManager
{

    protected static $instances = [];

    /**
     * 
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    public static function current()
    {
        return \DB::connection()->getDoctrineSchemaManager();
    }

    /**
     * 
     * @return array|\Doctrine\DBAL\Schema\Column
     */
    public static function listTableColumns($table)
    {
        return self::current()->listTableColumns($table);
    }

    /**
     * 
     * @param string $table
     * @return array of col names
     */
    public static function listTableColumnNames($table)
    {
        $tmp = static::current()->listTableColumns($table);

        return array_keys($tmp);
    }

    /**
     * 
     * @return array ofarrays ['indexname'=>['colname1',...],...]
     */
    public static function listTableUniqueFields($table)
    {
        $tmp = self::current()->listTableIndexes($table);

        $uniq = [];

        foreach ($tmp as $colname => $col)
        {
            if ($col->isPrimary() || $col->isUnique())
            {
                $uniq[$colname] = $col->getColumns();
            }
        }

        return ($uniq);
    }

    /**
     * tries to detect if the supplied field names would collide with a unique key
     * combination if inserted
     * 
     * @param type $table
     * @param type $fields
     * @return boolean true if there is a potential collision
     */
    public static function detectUniqueCollision($table, $fields)
    {
        $uniq = static::listTableUniqueFields($table);

        foreach ($uniq as $k => $cols)
        {
            $test = array_diff($cols, $fields);
            if (empty($test)) // all supplied fields would overwrite a unique constraint
            {
                return true;
            }
        }
        return false;
    }

}
