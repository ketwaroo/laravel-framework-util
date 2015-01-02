<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Patterns;

/**
 * Description of TraitSimpleVarEncode
 *
 * @author "Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>"
 */
trait TraitSimpleVarEncode
{

    /**
     * 
     * @param mixed $var
     * @return string
     */
    public function encodeVar($var)
    {
        return json_encode($var);
    }

    /**
     * 
     * @param string $str
     * @return mixed
     */
    public function decodeVar($str)
    {
        return json_decode($str, true);
    }

}
