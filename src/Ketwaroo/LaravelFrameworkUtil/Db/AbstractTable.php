<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Db;

/**
 * Description of TableAbstract
 * 
 * @experimental
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */
abstract class AbstractTable extends \Eloquent implements InterfaceTable
{

    use \Ketwaroo\LaravelFrameworkUtil\Patterns\TraitSingleton;

    public function __construct(array $attributes = array())
    {
        $this->setupTable();
        parent::__construct($attributes);
    }

    /**
     * @return array
     */
    abstract protected function declareGuardedAttributes();


    /*
     * @return array|null
     */

    protected function declareFillableAttributes()
    {
        return [];
    }

    public function setupTable()
    {
        $this->table      = $this->getTableName();
        $this->primaryKey = $this->getPrimaryKeyName();
        $this->fillable   = $this->declareFillableAttributes();
        $this->guarded    = $this->declareGuardedAttributes();
        $this->timestamps = true;
    }

    /**
     * return primary key value
     * @return mixed
     */
    public function getPrimaryKeyValue()
    {
        return $this->{$this->primaryKey};
    }

    /**
     * @todo documentation of foreign key local key
     * @param \Ketwaroo\LaravelFrameworkUtil\Db\AbstractTable $target
     * @param string $foreignKey key in this table mocel (relative to target it is foreign)
     * @param string $localKey key in target table model
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|\Illuminate\Database\Query\Builder $target
     */
    public static function iHasOne(AbstractTable $target, $foreignKey, $localKey)
    {
        return $target->hasOne(get_called_class(), $foreignKey, $localKey);
    }

    /**
     * @todo documentation of foreign key local key
     * @param \Ketwaroo\LaravelFrameworkUtil\Db\AbstractTable $target
     * @param string $foreignKey key in this table mocel (relative to target it is foreign)
     * @param string $localKey key in target table model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Query\Builder $target
     */
    public static function iHasMany(AbstractTable $target, $foreignKey, $localKey)
    {

        return $target->hasMany(get_called_class(), $foreignKey, $localKey);
    }

    /**
     * creates an empty eloquent collection.
     * can be used to maintain consistent return parameters.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function makeEmptyCollection()
    {
        return \Illuminate\Database\Eloquent\Collection::make([]);
    }

    public function filterAttribsForTable($attribs)
    {
        $fields = $this->getTableFieldNames();
        return array_intersect_key($attribs, array_flip($fields));
    }

    public function getTableFieldNames()
    {
        return SchemaManager::listTableColumnNames($this->getTableName());
    }

    /**
     * 
     * @return \Ketwaroo\LaravelFrameworkUtil\Db\AbstractTable
     */
    public static function instance()
    {
        return static::getInstance();
    }

}
