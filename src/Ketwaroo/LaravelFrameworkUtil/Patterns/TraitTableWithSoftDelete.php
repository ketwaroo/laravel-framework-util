<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Patterns;

use Illuminate\Database\Eloquent\Model;

/**
 * Fixes what laravel's soft delete was missing.
 * only works with cccisd/framework tables though.
 *
 * @author "Yaasir Ketwaroo <ketwaroo@3cisd.com>"
 */
trait TraitTableWithSoftDelete
{

    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    public static function bootTraitTableWithSoftDelete()
    {
        static::saving(function(Model $model)
        {
            // if we are saving, we SHOULD be undeleting targets wherever possible.
            // run a test if not trashed

            if(!($model->exists)) // should be insert but we could have softdeleted collision
            {
                $testActuallyExists = $model->withTrashed()
                        ->where($model->getDirty())
                        ->first();
                
                if(!empty($testActuallyExists)) // no need for further processing if really a new insert.
                {

                    $dirty = array_keys($model->getDirty());

                    // test if there would be collision on insert
                    // had to add doctrine/dbal package.
                    $testCollision = \Ketwaroo\LaravelFrameworkUtil\Db\SchemaManager::detectUniqueCollision($model->getTable(), $dirty);

                    if($testCollision)
                    {
                        // mimicking the restore function.

                        $model->exists = true;
                        $model->setAttribute($model->getPrimaryKeyName(), $testActuallyExists->getPrimaryKeyValue()); // needed for update.
                        $model->setAttribute($model->getDeletedAtColumn(), NULL);
                    }
                }
            }
        });
    }

}
