<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Filter;

use Ketwaroo\LaravelFrameworkUtil\Patterns\AbstractBaseModel;

/**
 * Description of FilterPayload
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */
class FilterPayload
{

    use \Ketwaroo\LaravelFrameworkUtil\Patterns\TraitBaseModel;

    /**
     * payload being operated on by the filter.
     * @var mixed 
     */
    public $subject;
    protected $filterCount = 0;

    public function __construct($subject, $extra = array())
    {
        $this->subject = $subject;
        $this->_setData($extra);
    }

    public function incrementFilterCount($by = 1)
    {
        $this->filterCount += $by;
        return $this;
    }

    public function getFilterCount()
    {
        return $this->filterCount;
    }

}
