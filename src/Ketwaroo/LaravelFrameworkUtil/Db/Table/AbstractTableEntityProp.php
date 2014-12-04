<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Db\Table;

use Ketwaroo\LaravelFrameworkUtil\Util\Text;

/**
 * Description of TableEntityProp
 *
 * @author "Yaasir Ketwaroo <ketwaroo@3cisd.com>"
 */
abstract class AbstractTableEntityProp extends \Ketwaroo\LaravelFrameworkUtil\Db\AbstractTable
{

    use \Ketwaroo\LaravelFrameworkUtil\Patterns\TraitSimpleVarEncode,
        \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $cachePrefix;

    /**
     * 
     * @return string
     */
    protected function getCachePrefix()
    {
        if(!isset($this->cachePrefix))
        {
            $this->cachePrefix = Text::toLowerDash($this->getTablePrefix() . ' entity prop');
        }

        return $this->cachePrefix;
    }

    /**
     * build a cache key
     * dot notation of prefix.arg1.arg2..argN
     * @return string
     */
    protected function makeEntityPropertyCacheKey()
    {
        return implode('.', array_merge([$this->getCachePrefix()], func_get_args()));
    }

    /**
     * 
     * @param int $entityId
     * @param string $entityType
     * @param type $propLabel
     * @return boolean
     */
    public function hasEntityProperty($entityId, $entityType, $propLabel)
    {
//        if(($cached = \Cache::has($this->makePropertyCacheKey($entityId, $entityType, $propLabel))))
//        {
//            return $cached;
//        }

        return static::where(array(
                            'entity_id'   => $entityId,
                            'entity_type' => $entityType,
                            'prop_label'  => $propLabel,
                        ))
                        ->exists();
    }

    /**
     * 
     * @param int $entityId
     * @param string $entityType
     * @param type $propLabels
     * @return array
     */
    public function hasEntityProperties($entityId, $entityType, $propLabels)
    {
        return $this->select(['prop_label'])->where(array(
                            'entity_id'   => $entityId,
                            'entity_type' => $entityType,
                        ))
                        ->whereIn('prop_label', $propLabels)
                        ->lists('prop_label');
    }

    public function getEntityProperty($entityId, $entityType, $propLabel, $default = NULL)
    {
        $result = $this->select(['prop_value'])->where(array(
                    'entity_id'   => $entityId,
                    'entity_type' => $entityType,
                    'prop_label'  => $propLabel,
                ))
                ->whereNull('deleted_at')
                ->first();

        if(!empty($result))
        {
            return $this->decodeVar($result->prop_value);
        }

        return $default;
    }

    /**
     * 
     * @param type $entityId
     * @param type $entityType
     * @param type $propLabel
     * @param type $propValue
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function setEntityProperty($entityId, $entityType, $propLabel, $propValue)
    {
        $test = static::withTrashed()
                        ->where(array(
                            'entity_id'   => $entityId,
                            'entity_type' => $entityType,
                            'prop_label'  => $propLabel,
                        ))->first();

        if(empty($test)) //insert
        {
            return static::create(array(
                        'entity_id'   => $entityId,
                        'entity_type' => $entityType,
                        'prop_label'  => $propLabel,
                        'prop_value'  => $this->encodeVar($propValue),
            ));
        }

        // undelete if deleted.
        if($test->trashed())
        {
            $test->restore();
        }

        $test->setAttribute('prop_value', $this->encodeVar($propValue));

        return $test->save();
    }

    public function getEntityProperties($entityId, $entityType, $propLabels)
    {
        $tmp  = $this->select(['prop_label', 'prop_value'])->where(array(
                    'entity_id'   => $entityId,
                    'entity_type' => $entityType,
                ))
                ->whereIn('prop_label', $propLabels)
                ->lists('prop_value', 'prop_label');
        $self = $this;

        array_walk($tmp, function(&$v, $k)use($self)
        {
            $v = $self->decodeVar($v);
        });

        return $tmp;
    }

    /**
     * 
     * @todo return something else?
     * @param type $entityId
     * @param type $entityType
     * @param array $props key=>value pairs
     */
    public function setEntityProperties($entityId, $entityType, $props)
    {
        foreach($props as $propLabel => $propValue)
        {
            $this->setEntityProperty($entityId, $entityType, $propLabel, $propValue);
        }

        return $this;
    }

    protected function declareGuardedAttributes()
    {
        return [
            $this->getPrimaryKeyName(),
        ];
    }

    public static function getPrimaryKeyName()
    {
        return 'entityprop_id';
    }

    public static function getTableShortName()
    {
        return 'entityprop';
    }

    public static function getTableName()
    {
        return static::getTablePrefix() . 'entityprop';
    }

    /**
     * creates the table if not exist.
     * 
     * @return \Illuminate\Database\Schema\Blueprint|null
     */
    public function buildTable()
    {
        if(!(\Schema::hasTable($this->getTableName())))
        {
            $create = \Schema::create($this->getTableName(), function(\Illuminate\Database\Schema\Blueprint $table)
                    {
                        // columns
                        $table->unsignedInteger($this->getPrimaryKeyName(), true);
                        $table->unsignedInteger('entity_id', true);
                        $table->string('entity_type', 32);
                        $table->string('prop_label', 64);
                        $table->longText('prop_value', 64);

                        // indexes
                        $table->primary($this->getPrimaryKeyName());
                        $table->index([
                            'entity_id',
                            'entity_type',
                            'prop_label',
                        ]);

                        // meta columns
                        $table->softDeletes();
                        $table->timestamps();

                        return $table;
                    });

            return $create;
        }
    }

}
