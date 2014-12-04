<?php


namespace Ketwaroo\LaravelFrameworkUtil\Patterns;

use Ketwaroo\LaravelFrameworkUtil\Util\Variable;

/**
 * Minimal attribute handling.
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */
trait TraitBaseModel
{

    protected $_data = array();

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        if(\array_key_exists($name, $this->_data))
        {
            return $this->_data[$name];
        }
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * extract some data from an array give key(s) can return single value if $keys is scalar.
     * @param type $keys
     * @return type
     */
    protected function _getData($keys = array())
    {
        return Variable::extractData($this->_data, $keys);
    }

    protected function _getDataValue($key, $default = NULL)
    {
        if(isset($this->_data[$key]))
            return $this->_data[$key];

        return $default;
    }

    /**
     * 
     * @param type $data
     * @return static
     */
    protected function _setData($key, $value = NULL)
    {
        
        Variable::setData($this->_data, $key, $value);
        return $this;
    }

    /**
     * 
     * @param type $field
     * @param bool|null $toggleTo if toggle
     * @param array $toggleValues array of 2 values, index 0 = false value, index 1 = true value.
     * @return \Sim_Common_Model|mixed
     */
    protected function _toggleOrGetField($field, $toggleTo = null, $toggleValues = array(0, 1))
    {
        if(!is_bool($toggleTo))
        {
            if(!isset($this->$field)) // has not been set yet, get falsy value
                return $toggleValues[0];
            return $this->$field;
        }
        else
        {
            $this->$field = ($toggleTo) ? $toggleValues[1] : $toggleValues[0];
            return $this;
        }
    }

}
