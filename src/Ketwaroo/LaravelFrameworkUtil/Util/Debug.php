<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Util;

/**
 * Debugging utils
 * @todo note to self; add stack trace parsing util.
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class Debug
{

    /**
     * attempts to reasonably find out if we are in debug mode.
     * @return boolean 
     */
    public static function envIsDebug()
    {
        static $result = null; // cache

        if(is_null($result))
        {
            if(!(\Config::get('app.debug'))) // shouldn't argue with that one.
            {
                $result = FALSE;
            }
            else
            {
                $result = TRUE;
            }
            // there may still be other tests
//            else
//            {
//                $tests  = array_filter(array(
//                    !empty($_ENV['DEBUG']) or ! empty($_ENV['debug']),
//                    !empty($_ENV['LARAVEL_DEBUG']) or ! empty($_ENV['laravel_debug']),
//                    Variable::getConstant('DEBUG'),
//                    Variable::getConstant('_DEBUG'),
//                    Variable::getConstant('LARAVEL_DEBUG'),
//                    Variable::getConstant('DEBUG_MODE'),
//                    (app()->environment() === 'local'),
//                    (PHP_SAPI === 'cli-server'),
//                ));
//                $result = !empty($tests);
//            }
            // or not..
        }

        return $result;
    }

    /**
     * Short hand variable dumper. takes any number of arguments.
     * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
     */
    public static function prnt()
    {
        $args = func_get_args();

        foreach($args as $a)
        {
            echo '<pre class="prnt" style="border:1px dotted #888;margin:1em auto;padding:1em;">';
            if(is_object($a))
            {
                echo '<h2>Vars</h2>';
                print_r($a);
                echo '<h3>Methods</h3>';
                print_r(get_class_methods($a));
            }
            elseif(is_array($a))
            {
                print_r($a);
            }
            else
            {
                var_dump($a);
            }
            echo '</pre>';
        }
    }

    /**
     * Variable dumper then die;
     * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
     */
    public static function prntd()
    {
        $tmp = func_get_args();
        call_user_func_array('prnt', $tmp);
        die;
    }

}
