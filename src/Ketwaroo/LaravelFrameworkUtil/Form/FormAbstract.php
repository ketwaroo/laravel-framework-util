<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil\Form;

/**
 * Work in progress
 *
 * @author "Yaasir Ketwaroo <ketwaroo@3cisd.com>"
 */
abstract class FormAbstract
{

    use \Ketwaroo\LaravelFrameworkUtil\Patterns\TraitIsInPackage,
        \Ketwaroo\LaravelFrameworkUtil\Patterns\TraitBaseModel;

    protected $data            = array();
    protected $validationRules = array();

    public function __construct()
    {
        $tmp = new \Illuminate\Html\FormBuilder;
        $tmp->
    }

    abstract public function getProperties();

    abstract public function getpropertyDefaults();

    abstract public function getPropertylabels();

    abstract public function getErrorMessages();

    /**
     * 
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidator()
    {
        return \Validator::make($this->getData(), $rules);
    }

    /**
     * 
     * @return array
     */
    public function getValidationRules()
    {
        return $this->validationRules;
    }

    public function addValidatorRule($property, $rule)
    {
        // $this->getValidator()->
    }

}
