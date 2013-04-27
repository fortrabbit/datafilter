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

use \DataFilter\Util as U;

/**
 * Data attribute
 *
 * Attributes are named input parameters with validation rules and filters
 *
 * @author Ulrich Kautz <ulrich.kautz@gmail.com>
 */

class Result
{

    /**
     * @var \DataFilter\Profile
     */
    protected $dataFilter;

    /**
     * @var array
     */
    protected $validAttribs;

    /**
     * @var array
     */
    protected $invalidAttribs;

    /**
     * @var array
     */
    protected $missingAttribs;

    /**
     * @var array
     */
    protected $unknownAttribs;

    /**
     * Constructor for DataFilter\Attribute
     *
     * @param string                $name        Name of the attrib (unique per data filter)
     * @param mixed                 $definition  The defnition (containing rule and stuff)
     * @param \DataFilter\Profile   $dataFilter  Parental data filter
     */
    public function __construct(\DataFilter\Profile &$dataFilter)
    {
        $this->dataFilter = $dataFilter;
    }

    /**
     * Returns all validated attributes
     *
     * @return array
     <code>
     $res = [
        'attribName' => (\DataFilter\Attribute)$obj,
        // ..
     ];
     </code>
     */
    public function getValidAttribs()
    {
        return array_combine(
            array_keys($this->validAttribs),
            array_map(function ($ref) {
                return $ref['attrib'];
            }, array_values($this->validAttribs))
        );
    }

    /**
     * Returns all validated data
     *
     * @return array
     <code>
     $res = [
        'attribName' => 'The input data',
        // ..
     ];
     </code>
     */
    public function getValidData()
    {
        return array_combine(
            array_keys($this->validAttribs),
            array_map(function ($ref) {
                return $ref['value'];
            }, array_values($this->validAttribs))
        );
    }

    /**
     * Returns all invalidated attributes
     *
     * @return array
     <code>
     $res = [
        'attribName' => (\DataFilter\Attribute)$obj,
        // ..
     ];
     </code>
     */
    public function getInvalidAttribs()
    {
        return array_combine(
            array_keys($this->invalidAttribs),
            array_map(function ($ref) {
                return $ref['attrib'];
            }, array_values($this->invalidAttribs))
        );
    }

    /**
     * Returns all invalid input data
     *
     * @return array
     <code>
     $res = [
        'attribName' => 'The input value',
        // ..
     ];
     </code>
     */
    public function getInvalidData()
    {
        return array_combine(
            array_keys($this->invalidAttribs),
            array_map(function ($ref) {
                return $ref['value'];
            }, array_values($this->invalidAttribs))
        );
    }

    /**
     * Returns all errors for invalid attribs
     *
     * @return array
     <code>
     $res = [
        'attribName' => 'The Error message',
        // ..
     ];
     </code>
     */
    public function getInvalidErrors()
    {
        return array_combine(
            array_keys($this->invalidAttribs),
            array_map(function ($ref) {
                return $ref['error'];
            }, array_values($this->invalidAttribs))
        );
    }

    /**
     * Returns all missing attributes
     *
     * @return array
     <code>
     $res = [
        'attribName' => (\DataFilter\Attribute)$obj,
        // ..
     ];
     </code>
     */
    public function getMissingAttribs()
    {
        return array_combine(
            array_keys($this->missingAttribs),
            array_map(function ($ref) {
                return $ref['attrib'];
            }, array_values($this->missingAttribs))
        );
    }

    /**
     * Returns all missing error message
     *
     * @return array
     <code>
     $res = [
        'attribName' => 'The error message'
        // ..
     ];
     </code>
     */
    public function getMissingErrors()
    {
        return array_combine(
            array_keys($this->missingAttribs),
            array_map(function ($ref) {
                return $ref['error'];
            }, array_values($this->missingAttribs))
        );
    }

    /**
     * Returns combined missing an invalid error messages
     *
     * @return array
     <code>
     $res = [
        'attribName' => 'The error message'
        // ..
     ];
     </code>
     */
    public function getInvalidOrMissingErrors()
    {
        $all = $this->invalidAttribs + $this->missingAttribs;
        return array_combine(
            array_keys($all),
            array_map(function ($ref) {
                return $ref['error'];
            }, array_values($all))
        );
    }

    /**
     * Returns data of unkown input
     *
     * @return array
     <code>
     $res = [
        'attribName' => 'The input data',
        // ..
     ];
     </code>
     */
    public function getUnknownData()
    {
        return $this->unknownAttribs;
    }

    /**
     * Returns data of all inputs (valid, invalid and unkown input)
     *
     * @return array
     <code>
     $res = [
        'attribName' => 'The input data',
        // ..
     ];
     </code>
     */
    public function getAllData()
    {
        return $this->getValidData() + $this->getInvalidData() + $this->unknownAttribs;
    }

    /**
     * Returns attribute of named attrib if existing (either valid or invalid)
     *
     * @param string  $attribName  Name of the attrib
     *
     * @return \DataFilter\Attribute
     */
    public function getAttrib($attribName)
    {
        if (isset($this->validAttribs[$attribName])) {
            return $this->validAttribs[$attribName]['attrib'];
        }
        elseif (isset($this->invalidAttribs[$attribName])) {
            return $this->invalidAttribs[$attribName]['attrib'];
        }
        return null;
    }

    /**
     * Returns value of named attrib if existing (either valid, invalid or unknown)
     *
     * @param string  $attribName  Name of the attrib
     *
     * @return string
     */
    public function getData($attribName)
    {
        if (isset($this->validAttribs[$attribName])) {
            return $this->validAttribs[$attribName]['value'];
        }
        elseif (isset($this->invalidAttribs[$attribName])) {
            return $this->invalidAttribs[$attribName]['value'];
        }
        elseif (isset($this->unknownAttribs[$attribName])) {
            return $this->unknownAttribs[$attribName];
        }
        return null;
    }


    /**
     * Returns whether has error
     *
     * @return bool
     */
    public function hasError($attribName = null)
    {
        if (is_null($attribName)) {
            return count($this->invalidAttribs) > 0 || count($this->missingAttribs) > 0;
        } else {
            return isset($this->invalidAttribs[$attribName]) || isset($this->missingAttribs[$attribName]);
        }
    }

    /**
     * Returns all errors (invalid + missing)
     *
     * @return array
     <code>
     $res = [
        'attribName' => 'The error text',
        // ..
     ];
     </code>
     */
    public function getAllErrors()
    {
        return $this->getInvalidErrors() + $this->getMissingErrors();
    }

    /**
     * Returns all error texts (no assoc)
     *
     * @return array
     <code>
     $res = [
        'The error text',
        // ..
     ];
     </code>
     */
    public function getErrorTexts($join = null)
    {
        $errors = array_filter(
            array_merge(array_values($this->getInvalidErrors()), array_values($this->getMissingErrors())),
            function($txt) {
                return !is_null($txt);
            }
        );
        return $join ? join($join, $errors) : $errors;
    }


    /**
     * Check this rule against input
     *
     * @param string  $input  Input data
     *
     * @return bool
     */
    public function check($data)
    {
        $this->validAttribs   = array();
        $this->invalidAttribs = array();
        $this->missingAttribs = array();
        $this->unknownAttribs = array();
        $requiredDependent    = array();
        $seenAttrib           = array();

        foreach (U::flatten($data) as $attribName => $value) {
            $attrib = $this->dataFilter->getAttrib($attribName);
            if (!$attrib) {
                $parts = explode(U::$FLATTEN_SEPARATOR, $attribName);
                $count = count($parts);
                if ($count > 1) {

                    for ($i = $count -1; $i >= 1; $i--) {
                        $testName = join(U::$FLATTEN_SEPARATOR, array_splice($parts, 0, $i));
                        $attrib   = $this->dataFilter->getAttrib($testName. U::$FLATTEN_SEPARATOR. '*');
                        if ($attrib) {
                            break 1;
                        }
                    }
                }
            }
            $seenAttrib[$attribName] = true;

            // unknown attrib
            if (!$attrib) {
                $this->unknownAttribs[$attribName] = $this->dataFilter->applyFilter('pre', $value);
                continue;
            }

            // run pre-filters
            if ($attrib->useFilters()) {
                $value = $this->dataFilter->applyFilter('pre', $attrib->applyFilter('pre', $value));
            }

            // successfull check
            if ($attrib->check($value)) {
                $this->validAttribs[$attribName] = array(
                    'value'  => $attrib->useFilters()
                        ? $this->dataFilter->applyFilter('post', $attrib->applyFilter('post', $value))
                        : $value,
                    'attrib' => &$attrib
                );

                // determine possible dependents
                $attrib->determineDependents($value, $requiredDependent);
            }

            // checks failed
            else {
                $this->invalidAttribs[$attribName] = array(
                    'value'  => $value,
                    'attrib' => &$attrib,
                    'error'  => $attrib->getError()
                );
            }
        }

        // check now all attribs for required
        foreach ($this->dataFilter->getAttribs() as $attribName => $attrib) {

            // already seen
            if (isset($seenAttrib[$attribName])) {
                continue;
            }

            // has default
            elseif (!is_null($default = $attrib->getDefault())) {
                $this->validAttribs[$attribName] = ['value' => $default, 'attrib' => &$attrib];
            }

            // required -> missing
            elseif ($attrib->isRequired() || isset($requiredDependent[$attribName])) {
                $parts = explode(U::$FLATTEN_SEPARATOR, $attribName);
                $count = count($parts);
                if ($count > 1 && $parts[$count-1] === '*') {
                    $before = join(U::$FLATTEN_SEPARATOR, array_splice($parts, 0, $count - 1)). U::$FLATTEN_SEPARATOR;
                    $seen   = array_filter(array_keys($seenAttrib), function ($check) use ($before) {
                        return strpos($check, $before) === 0;
                    });
                    if (count($seen) > 0) {
                        continue;
                    }
                }
                $this->missingAttribs[$attribName] = array(
                    'attrib' => &$attrib,
                    'error'  => $attrib->getMissingText()
                );
            }
        }

        /*error_log("INVALID: ". count($this->invalidAttribs));
        error_log("MISSING: ". count($this->missingAttribs));*/
        return !$this->hasError();
    }

}
