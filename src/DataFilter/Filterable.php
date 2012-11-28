<?php


/*
 * This file is part of DataFilter.
 *
 * (c) Ulrich Kautz <ulrich.kautz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DataFilter;

/**
 * Basic predefined validation rules
 *
 * @author Ulrich Kautz <ulrich.kautz@gmail.com>
 */

abstract class Filterable
{

    /**
     * @var array
     */
    protected $preFilters;

    /**
     * @var array
     */
    protected $postFilters;

    /**
     * Add multiple filters at once
     *
     * @param string  $position  Either "pre" or "post"
     * @param array   $filters   List of filters
     *
     * @throws \InvalidArgumentException
     */
    public function addFilters($position, $filters)
    {
        // oops, invalid position
        if (!in_array($position, array('pre', 'post'))) {
            throw new \InvalidArgumentException("Cannot add filters to '$position'. Use 'pre' or 'post'");
        }

        // single filter
        if (is_callable($filters)) {
            $filters = array($filters);
        }

        // determine accessor
        $var = $position. 'Filters';
        if (!$this->$var) {
            $this->$var = array();
        }

        // add all filters
        foreach ($filters as $num => $filter) {

            // callable, not clsure
            if (is_callable($filter) && is_array($filter)) { // && !($filter instanceof \Closure)) {
                $cb = $filter;
                $filter = function($in) use($cb) {
                    return call_user_func_array($cb, array($in));
                };
            }

            // from string (predefined filter)
            elseif (is_string($filter)) {
                $method = 'filter'. $filter;
                $df = $this instanceof \DataFilter\Profile ? $this : $this->dataFilter;
                $foundFilter = false;
                $args = $this instanceof \DataFilter\Profile
                    ? array(null, $this)               // data filter
                    : array($this, $this->dataFilter); // attribute
                foreach ($df->getPredefinedFilterClasses() as $className) {
                    if (is_callable($className, $method) && method_exists($className, $method)) {
                        $foundFilter = true;
                        $filter = call_user_func_array(array($className, $method), $args);
                        break;
                    }
                }
                if (!$foundFilter) {
                    $filterName = $this instanceof \DataFilter\Profile
                        ? 'global '. $position. '-filter'
                        : 'rule "'. $this->name. '", attrib "'. $this->attrib->getName(). '"'
                            . ' as '. $position. '-filter';
                    throw new \InvalidArgumentException(
                        'Could not use filter "'. $filter. '" for '. $filterName. ' because no '
                        . 'predefined filter class found implementing "'. $method. '()"'
                    );
                }
            }

            // oops, invalild filter
            if (!is_callable($filter)) {
                throw new \InvalidArgumentException(
                    "Filter '$num' for attribute '". $this->name. "' is not a callable!"
                );
            }
            // convert oldschool filter to closure
            if (!($filter instanceof \Closure)) {
                $args = $this instanceof \DataFilter\Profile
                    ? array(null, $this)               // data filter
                    : array($this, $this->dataFilter); // attribute
                $filter =  call_user_func_array($filter, $args);
            }

            // add filter
            array_push($this->$var, $filter);
        }
    }

    /**
     * Add multiple pre-filters at once
     *
     * @param array   $filters   List of filters
     *
     * @throws \InvalidArgumentException
     */
    public function addPreFilters($filters)
    {
        return $this->addFilters('pre', $filters);
    }

    /**
     * Add multiple post-filters at once
     *
     * @param array   $filters   List of filters
     *
     * @throws \InvalidArgumentException
     */
    public function addPostFilters($filters)
    {
        return $this->addFilters('post', $filters);
    }


    /**
     * Runs filter on input
     *
     * @param string  $position  Either "pre" or "post"
     * @param string  $input     The input
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function applyFilter($position, $input)
    {
        // oops, invalid position
        if (!in_array($position, array('pre', 'post'))) {
            throw new \InvalidArgumentException("Cannot add filters to '$position'. Use 'pre' or 'post'");
        }

        // determine accessor
        $var = $position. 'Filters';

        if (!$this->$var) {
            return $input;
        }

        foreach ($this->$var as $filter) {
            $input = $filter($input);
        }
        return $input;
    }

}
