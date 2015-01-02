<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Patterns;

use Illuminate\Database\Eloquent\Model;
use Ketwaroo\LaravelFrameworkUtil\Util\Variable;
/**
 * This trait should be used with a Table Model.
 * 
 * GUID strings are a possible solution to dissonant systems that need to share
 * relationships between entities.
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
trait TraitTableWithGuid
{

    /**
     * 
     * @return string The name of the GUID field in the table
     */
    public static function getGuidFieldName()
    {
        // php throws strict warning on abstract static methods
        throw new \Exception('[' . __FUNCTION__ . '] must be implemented by [' . get_called_class() . ']');
    }

    /**
     * check if allowed to setGuid
     * @return boolean
     */
    protected function allowSetGuidOnInsert()
    {
        return false;
    }

    /**
     * Only works for new objects
     * once persisted the guid cannot be changed by this model
     * @param string $guid
     * @return static
     * @throws \Exception
     */
    public function setGuid($guid)
    {
        if(!($this->allowSetGuidOnInsert()))
        {
            throw new \Exception('Overriding of Guid field is not allowed');
        }

        $f          = $this->getGuidFieldName();
        $this->{$f} = $guid;
        return $this;
    }

    public static function bootTraitTableWithGuid()
    {
        static::creating(function(Model $model)
        {
            $f = $model->getGuidFieldName();

            // if not allowed or there's no guid already set.
            if(!($model->allowSetGuidOnInsert()) || !($model->getGuidvalue()))
            {
                $model->setAttribute($f, $model->makeFreshGuid()); // new
            }
        });

        static::updating(function(Model $model)
        {
            $f = $model->getGuidFieldName();
            //@todo this may not be working as expected.
            unset($model->{$f}); // no changing the guid 
        });
    }

    /**
     * 
     * @param string $guid
     * @return $this|null
     */
    public static function findByGuid($guid)
    {
        return static::where([
                    static::getGuidFieldName() => $guid,
                ])->first();
    }

    /**
     * 
     * @param string $guid
     * @return $this
     */
    public static function findByGuidOrFail($guid)
    {

        return static::where([
                            static::getGuidFieldName() => $guid,
                        ])
                        ->firstOrFail();
    }

    /**
     * 
     * @return string
     */
    public function getGuidvalue()
    {
        return $this->{$this->getGuidFieldName()};
    }

    public static function makeFreshGuid()
    {
        return Variable::GUID();
    }

}
