<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Filter;

use Ketwaroo\LaravelFrameworkUtil\Patterns\AbstractBaseModel;
use Ketwaroo\LaravelFrameworkUtil\Filter\FilterPayload;

/**
 * Akin to content filters in wordpress.
 * laravel has hooks but no filters.
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
class Filter extends AbstractBaseModel
{

    use \Ketwaroo\LaravelFrameworkUtil\Patterns\TraitSingleton;

    /**
     * Usage:
     * <code><pre>
     * 
     *  use Ketwaroo\LaravelFrameworkUtil\Filter\Filter as ContentFilter;
     *  use Ketwaroo\LaravelFrameworkUtil\Filter\FilterPayload;
     * 
     *  ContentFilter::instance()->enqueue('event',function(FilterPayload $payload,...){ ... });
     * 
     * </pre></code>
     * 
     * The callable should take Ketwaroo\LaravelFrameworkUtil\Filter\FilterPayload as first parameter.
     * Filter payload is passed as object to leverage the fact that objects are passed as reference by default.
     * This saves on some memory and allows us to add functionality to the payload object later on.
     * 
     * 
     * @param type $event
     * @param \Closure|callable $filter
     * @param int $priority default=0
     * @return \Ketwaroo\LaravelFrameworkUtil\Filter\Filter
     */
    public function enqueue($event, $filter, $priority = 0)
    {
        $event = $this->protectFilterEvents($event);

        \Event::listen($event, $filter, $priority);

        return $this;
    }

    /**
     * 
     * @param string $event
     * @param mixed $subject1[,$subject2,...] content we are processing can have multiple arguments
     * @return mixed
     */
    public function render($event, $subject)
    {
        $params = func_get_args();
        array_shift($params); // remove first element, we already have that.

        $events = $this->protectFilterEvents($event);

        array_shift($params); // second param is the subject.

        $payload = new FilterPayload($subject, $params);

        array_unshift($params, $payload);

        foreach($events as $event) // the protectfilter returns an array. should only be one though.
        {
            $result = \Event::fire($event, $params); // do the actual thing.

            $payload->incrementFilterCount(count($result));
        }
        return $payload->subject;
    }

    /**
     * obfuscates the filter events a bit so they do not collide with regular
     * hook events that laravel handles.
     * 
     * @param type $event
     * @return type
     */
    protected function protectFilterEvents($event)
    {
        $event = (array) $event;
        array_walk($event, function(&$item)
        {
            $item = 'cccisd.filter.' . $item;
        });

        return $event;
    }

    /**
     * 
     * @return \Ketwaroo\LaravelFrameworkUtil\Filter\Filter
     */
    public static function instance()
    {
        return self::getInstance();
    }

}
