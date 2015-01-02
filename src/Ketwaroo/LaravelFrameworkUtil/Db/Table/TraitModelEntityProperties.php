<?php

namespace Ketwaroo\LaravelFrameworkUtil\Db\Table;

use Ketwaroo\LaravelFrameworkUtil\Db\Table\AbstractTableEntityProp;

/**
 * 
 * @author "Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>"
 */
Trait TraitModelEntityProperties
{

    /**
     * @return string the unique label for the entity type.
     */
    abstract protected function getEntityType();

    /**
     * get instance of an entityprop table local to the app's database.
     * @return AbstractTableEntityProp 
     */
    abstract public function getEntityPropTable();

    public function getEntityProperty($label, $default = NULL)
    {
        /* @var $this AbstractTable */
        return $this->getEntityPropTable()->getEntityProperty($this->getPrimaryKeyValue(), $this->getEntityType(), $label, $default);
    }

    public function setEntityProperty($label, $value)
    {
        /* @var $this AbstractTable */
        return $this->getEntityPropTable()->setEntityProperty($this->getPrimaryKeyValue(), $this->getEntityType(), $label, $value);
    }

    public function getEntityProperties($propLabels)
    {
        /* @var $this AbstractTable */
        return $this->getEntityPropTable()->getEntityProperties($this->getPrimaryKeyValue(), $this->getEntityType(), $propLabels);
    }

    public function setEntityProperties($props)
    {
        /* @var $this AbstractTable */
        return $this->getEntityPropTable()->setEntityProperties($this->getPrimaryKeyValue(), $this->getEntityType(), $props);
    }

}
