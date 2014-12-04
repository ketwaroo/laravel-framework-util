<?php

namespace Ketwaroo\LaravelFrameworkUtil\Patterns;

/**
 * Experiment to cut down on that Facade rigmarole to get short static calls.
 * @experimental
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */
trait TraitAutoCallStatic
{

    public static function __callStatic($name, $arguments)
    {
        $callingClass = get_called_class();
        try
        {
            $instance = app()->make($callingClass);

            return call_user_func_array(array($instance, $name), $arguments);
        }
        catch(Exception $exc)
        {
            echo $exc->getTraceAsString();
        }
    }

}
