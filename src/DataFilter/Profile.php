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

use DataFilter\Filterable;

/**
 * Filtering data

<code>

$df = new \DataFilter\Profile([
    'attribs' => [
        'attribName' => [

            // make required
            'required' => true,

            // enable match any mode (default: match all) -> as long as ONE rules matches, validation OK
            'matchAny' => true,

            // default value, if not given (implies optional)
            'default' => 'a1',

            // custom error when missing (only if no default and required)
            'missing' => 'Where is :attrib:?',

            // rules are applied as given
            'rules' => [

                // user defined callback (func ref)
                'someCallback' => [
                    'constraint' => function($input, $attrib, $rule, $dataFilter) {
                        error_log("I am in rule ". $rule->getName(). " for attrib ". $attrib->getName());
                        return strlen($input) < 5;
                    },
                    'error'      => 'Input for :attrib: is to long',
                ],

                // user defined callback (callable)
                'someCallback' => [
                    'constraint' => array('\\MyClass', 'myMethod'),
                    'error'      => 'Something is wrong with :attrib:',

                    // if this rule matches -> no other is required
                    'sufficient' => true,
                ],

                // using regex
                'someRegex' => [
                    'constraint' => 'Regex:/^a[0-9]+$/',
                    'error'      => 'String format should be "a" followed by numbers for :attrib:',
                ],

                // shortcut with no custom error
                'otherRule' => function($input) {
                    return time() % 2;
                },

            ],

            // make other attribs required via dependencies
            'dependent' => [

                // if "a123" matches
                'a123' => ['otherAttrib', 'yetAnother'],

                // if no other matches => this matches
                '*' => ['otherAttrib']
            ],

            // make other attribs required via dependencies (regex)
            'dependentRegex' => [
                '/^(a1)[234]/' => ['otherAttrib'],
            ],

            // pre-validation input filters
            'preFilters' => [
                function($input, $attrib) {
                    return $input . '0';
                }
            ],

            // post-validation input filters
            'postFilters' => [
                function($input, $attrib) {
                    return $input . '0';
                }
            ]
        ],

        // an optional attrib with a constraint
        'fooBar' => 'Regex:/^foo/',

        // a required attrib, no validation
        'otherAttrib' => true,

        // an optional attrib, no validation
        'yetAnother' => false
    ],

    // default error template
    'errorTemplate' => 'Attribute ":attrib:" is frong (rule :rule:)',

    // classes for predefined (string) rules
    'ruleClasses' => ['\\MyPredefinedRules'],

    /// classes for predefined (string) filters
    'filterClasses' => ['\\MyPredefinedFilters'],

    // global pre filters to be applied on all inputs -> run before
    'preFilters' => [
        array('\\MyClass', 'filterMethod'),
    ],

    // global post filters to be applied on all inputs (including unknown but not invalid)
    'postFilters' => [
        array('\\MyClass', 'filterMethod'),
    ]

]);

$inputData = [
];

if ($df->check($inputData)) {
    echo "OK, all good\n";
}
else {
    $res = $df->getLastResult();
    foreach ($res->getErrors() as $error) {
        echo "Err: $error\n";
    }

    if ($res->getErrorFor('attr'
}

</code>
 *
 * @author Ulrich Kautz <ulrich.kautz@gmail.com>
 */

class Profile extends Filterable
{

    /**
     * @const string
     */
    const DEFAULT_ERROR = 'Attribute ":attrib:" does not match ":rule:"';

    /**
     * @const string
     */
    const DEFAULT_MISSING = 'Attribute ":attrib:" is missing';


    /**
     * @var array
     */
    protected $attribs;

    /**
     * @var array
     */
    protected $predefinedRuleClasses = array(
        '\\DataFilter\\PredefinedRules\\Basic'
    );

    /**
     * @var array
     */
    protected $predefinedFilterClasses = array(
        '\\DataFilter\\PredefinedFilters\\Basic'
    );

    /**
     * @var string
     */
    protected $errorTemplate = self::DEFAULT_ERROR;

    /**
     * @var string
     */
    protected $missingTemplate = self::DEFAULT_MISSING;

    /**
     * @var \DataFilter\Result
     */
    protected $lastResult;



    /**
     * Constructor for DataFilter\DataFilter
     *
     * @param array  $definition  Optional definition
     */
    public function __construct($definition = array())
    {
        if (isset($definition['errorTemplate'])) {
            $this->errorTemplate = $definition['errorTemplate'];
        }
        if (isset($definition['missingTemplate'])) {
            $this->missingTemplate = $definition['missingTemplate'];
        }
        foreach (array('ruleClasses', 'filterClasses') as $var) {
            if (isset($definition[$var])) {
                $accessor = 'predefined'. ucfirst($var);
                foreach ($definition[$var] as $addClass) {
                    array_push($this->$accessor, $addClass);
                }
                array_unique($this->$accessor);
            }
        }
        if (isset($definition['preFilters'])) {
            $this->addFilters('pre', $definition['preFilters']);
        }
        if (isset($definition['postFilters'])) {
            $this->addFilters('post', $definition['postFilters']);
        }
        if (isset($definition['attribs'])) {
            $this->setAttribs($definition['attribs']);
        } elseif (isset($definition['attributes'])) {
            $this->setAttribs($definition['attributes']);
        } else {
            $this->attribs = array();
        }
    }

    /**
     * Construct from JSON file
     *
     * @param string  $jsonFile  The JSON file
     *
     * @return \DataFilter\Profile
     *
     * @throws \RuntimeException
     */
    public static function fromJson($jsonFile)
    {
        if (!is_file($jsonFile) && !is_readable($jsonFile)) {
            throw new \RuntimeException("Either '$jsonFile' is not a file or cannot access it");
        }
        $content = file_get_contents($jsonFile);
        if (!$content) {
            throw new \RuntimeException("Cannot load empty JSON file '$jsonFile'");
        }
        $json = json_decode($content, true);
        if (!$json) {
            throw new \RuntimeException("Could not parse JSON from '$jsonFile'");
        }
        return new \DataFilter\Profile($json);
    }



    /**
     * Set (replace/add) multiple named attribs at once
     *
     * @param array  $attribsDefinition  Attrib/rule definition
     */
    public function setAttribs($attribsDefinition)
    {
        foreach ($attribsDefinition as $attribName => $definition) {
            $this->setAttrib($attribName, $definition);
        }
    }


    /**
     * Set (replace/add) a named attribute. Returns the new attrib
     *
     * @param string  $attribName        Name of the attrib
     * @param mixed   $attribDefinition  Attrib/rule definition or \DataFilter\Attribute object
     *
     * @return \DataFilter\Attribute
     */
    public function setAttrib($attribName, $attribDefinition = null)
    {
        $this->attribs[$attribName] = is_object($attribDefinition) && $attribDefinition instanceof \DataFilter\Attribute
            ? $attribDefinition
            : new \DataFilter\Attribute($attribName, $attribDefinition, $this);
        return $this->attribs[$attribName];
    }

    /**
     * Returns list of attributes (assoc array)
     *
     * @return array
     */
    public function getAttribs()
    {
        return $this->attribs;
    }

    /**
     * Returns single attribute by name (or null)
     *
     * @param string  $attribName  Name of attrib
     *
     * @return \DataFilter\Attribute
     */
    public function getAttrib($attribName)
    {
        return isset($this->attribs[$attribName]) ? $this->attribs[$attribName] : null;
    }

    /**
     * Removes a single attribute by name
     *
     * @param string  $attribName  Name of the attrib
     *
     * @return bool  Whether removed
     */
    public function removeAttrib($attribName)
    {
        if (isset($this->attribs[$attribName])) {
            unset($this->attribs[$attribName]);
            return true;
        }
        return false;
    }

    /**
     * Returns list of predefined rule classes
     *
     * @return array
     */
    public function getPredefinedRuleClasses()
    {
        return $this->predefinedRuleClasses;
    }

    /**
     * Returns list of predefined filter classes
     *
     * @return array
     */
    public function getPredefinedFilterClasses()
    {
        return $this->predefinedFilterClasses;
    }


    /**
     * Returns default error template
     *
     * @return string
     */
    public function getErrorTemplate()
    {
        return $this->errorTemplate;
    }

    /**
     * Returns default missing template
     *
     * @return string
     */
    public function getMissingTemplate()
    {
        return $this->missingTemplate;
    }

    /**
     * Returns the last check result
     *
     * @return \DataFilter\Result
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }

    /**
     * Check this rule against input
     *
     * @param array  $data  Input data
     *
     * @return bool
     */
    public function run($data)
    {
        $this->lastResult = new \DataFilter\Result($this);
        $this->lastResult->check($data);
        return $this->lastResult;
    }

    /**
     * Check this rule against input
     *
     * @param array  $data  Input data
     *
     * @return bool
     */
    public function check($data)
    {
        return !$this->run($data)->hasError();
    }


}
