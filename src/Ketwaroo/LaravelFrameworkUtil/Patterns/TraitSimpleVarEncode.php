<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Patterns;

/**
 * Description of TraitSimpleVarEncode
 *
 * @author "Yaasir Ketwaroo <ketwaroo@3cisd.com>"
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
