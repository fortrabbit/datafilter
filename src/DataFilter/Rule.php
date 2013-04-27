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

class Rule
{
    /**
     * @var array
     */
    protected static $DEFAULT_ATTRIBS = array(
        'sufficient' => false,
        'skipEmpty'  => false,
        'constraint' => null,
        'error'      => null,
    );

    /**
     * @var \DataFilter\Profile
     */
    protected $dataFilter;

    /**
     * @var \DataFilter\Attribute
     */
    protected $attrib;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable (func ref)
     */
    protected $constraint;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var bool
     */
    protected $sufficient = false;

    /**
     * @var bool
     */
    protected $skipEmpty = false;

    /**
     * @var bool
     */
    protected $lazy = false;

    /**
     * @var bool
     */
    protected $definition = null;

    /**
     * @var string
     */
    protected $lastValue;

    /**
     * Constructor for DataFilter\Attribute
     *
     * @param string                $name        Name of the attrib (unique per data filter)
     * @param mixed                 $definition  The rule definition
     * @param \DataFilter\Profile   $dataFilter  Parental data filter
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $definition, \DataFilter\Attribute &$attribute, \DataFilter\Profile &$dataFilter)
    {
        $this->name = $name;
        $this->attrib = $attribute;
        $this->dataFilter = $dataFilter;

        if (is_array($definition) && isset($definition['lazy']) && $definition['lazy'] === true) {
            $this->lazy = $definition['lazy'];
            $this->definition = $definition;
        }
        else {
            $this->parseDefinition($definition);
        }
    }

    /**
     * The long description
     *
     * @param mixed  $definition  The rule definition
     *
     * @throws \InvalidArgumentException
     */
    protected function parseDefinition($definition = null)
    {
        if (is_null($definition)) {
            if (!is_null($this->definition)) {
                throw new \InvalidArgumentException(
                    'Cannot parse rule definitions for rule "'. $this->name. '", attrib "'
                    . $this->attrib->getName(). '" without definition!'
                );
            }
            $definition = $this->definition;
            $this->definition = null;
        }

        // required, simple
        if (is_string($definition) || is_callable($definition)) {
            $definition = array('constraint' => $definition);
        }

        // init empty to reduce isset checks..
        $definition = array_merge(self::$DEFAULT_ATTRIBS, $definition);

        // set attribs
        $this->sufficient = $definition['sufficient'];
        $this->skipEmpty  = $definition['skipEmpty'];
        $this->error      = $definition['error'];

        // having old style callable constraint
        if (is_callable($definition['constraint']) && is_array($definition['constraint'])) { // !($definition['constraint'] instanceof \Closure)) {
            $cb = $definition['constraint'];
            $definition['constraint'] = function () use ($cb) {
                $args = func_get_args();
                return call_user_func_array($cb, $args);
            };
        }

        // from string -> check predefined
        elseif (is_string($definition['constraint'])) {
            $args = preg_split('/:/', $definition['constraint']);
            $method = 'rule'. array_shift($args);
            $found = false;
            foreach ($this->dataFilter->getPredefinedRuleClasses() as $className) {
                if (is_callable(array($className, $method)) && method_exists($className, $method)) {
                    $definition['constraint'] = call_user_func_array(array($className, $method), $args);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new \InvalidArgumentException(
                    'Could not use constraint "'. $definition['constraint']. '" for rule "'
                    . $this->name. '", attrib "'. $this->attrib->getName(). '" because no '
                    . 'predefined rule class found implementing "'. $method. '()"'
                );
            }
        }

        // determine class
        $constraintClass = is_object($definition['constraint'])
            ? get_class($definition['constraint'])
            : '(Scalar)';

        // at this point: it has to be a closure!
        if ($constraintClass !== 'Closure') {
            throw new \InvalidArgumentException(
                'Definition for rule "'. $this->name. '", attrib "'. $this->attrib->getName(). '"'
                . ' has an invalid constraint of class '. $constraintClass
            );
        }
        $this->constraint = $definition['constraint'];
    }

    /**
     * Check this rule against input
     *
     * @param string  $input  Input data
     *
     * @return bool
     */
    public function check($input)
    {
        if ($this->lazy) {
            $this->lazy = false;
            $this->parseDefinition();
        }
        $this->lastValue = $input;
        if (strlen($input) === 0 && $this->skipEmpty) {
            return true;
        }
        $constraint = $this->constraint;
        return $constraint($input, $this, $this->attrib, $this->dataFilter);
    }

    /**
     * Returns last input value used for check (or determine Dependent)
     *
     * @return string
     */
    public function getLastValue()
    {
        return $this->lastValue;
    }

    /**
     * Returns bool whether this is sufficient
     *
     * @return bool
     */
    public function isSufficient()
    {
        return $this->sufficient;
    }

    /**
     * Returns error string or null
     *
     * @return string
     */
    public function getError(\DataFilter\Attribute $attrib = null)
    {
        if ($this->error === false) {
            return null;
        }
        if (!$attrib) {
            $attrib = $this->attrib;
        }
        $formatData = array('rule' => $this->name);
        if ($attrib) {
            $formatData['attrib'] = $attrib->getName();
        }
        $error = $this->error;
        if (!$error && $attrib) {
            $error = $attrib->getDefaultErrorStr();
        }
        if (!$error) {
            $error = $this->dataFilter->getErrorTemplate();
        }
        return U::formatString($error, $formatData);
    }

}
