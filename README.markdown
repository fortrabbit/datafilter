# DataFilter

DataFilter is a data validation (sanitation) module for PHP.

The incentive to write this module is mainly rooted in the need to have a general validation module, neither interwined with any kind of ORM nor controller (in terms of MVC) logic.

The second goal was to create a to which can be configured using a meta language (JSON, YAML, ..) having no need for "inline" rule definition - in short: keep the validation rules separate from the business logic.

PHP 5.4 is required.

# Install via Composer

Create a minimal `composer.json`:

    {
        "require": {
            "datafilter/datafilter": "dev-master"
        }
    }

Run composer update or install

    composer.phar install --dev

# Getting started

A basic example, which expects two POST parameters, both required

    <?php

    require 'vendor/autoload.php';
    
    // ..
    
    $profile = new \DataFilter\Profile([
        'attribs' => [
            'username' => true,
            'password' => true
        ]
    ]);
    if ($profile->check($_POST)) {
        $data = $profile->getLastResult()->getValidData();
    }
    else {
        error_log("Failed, required params not given");
    }

# In Depth

## Validation Profile

A validation profile is a set of attributes each having (possibly multiple) rules. You can think of it as a single formular or as the general attribute validation for a Model (ORM..).

### Structure

    $profile = [

        // attribute definitions
        'attribs' => [
            'attributeName' => [
                // rule definitions
            ],
            // list of attributes and rules
        ],

        // overwrite default invalid error message
        'errorTemplate' => "Attribute :attrib: violated rule :rule:",

        // overwrite default missing error message
        'missingTemplate' => "Attribute :attrib: is missing",

        // custom rule classes
        'ruleClasses' => [
            "\\MyRuleClass",
            // ..
        ],

        // custom filter classes
        'filterClasses' => [
            "\\MyFilterClass",
            // ..
        ],

        // custom, global pre-filters (before validating)
        'preFilters' => [
            function($in) {
                return $in;
            },
            "namedFilter",
            ["\\SomeClass", "someMethod"],
            // ..
        ],

        // custom, global pre-filters (after validating, only on valids)
        'postFilters' => [
            function($in) {
                return $in;
            },
            "namedFilter",
            ["\\SomeClass", "someMethod",
            // ..
        ]
    ];


### Rule formats

### Simplistic (required, optional)

The simplest rule format is `true` (reuquired) or `false` (optional).

    // ..
    'attribs' => [
        'attribName'  => true,
        'attribName2' => false
    ],
    // ..

### Named check functions

There are a couple of pre-defined named functions which can be used. For a full list, look in `\DataFilter\PredefinedRules\Basic`.

The follwing example shows one regular expression test and one min-lenght tests. Both attributes are implicit required.

    // ..
    'attribs' => [
        'attribName'  => 'Regex:/^a[0-9]+',
        'attribName2' => 'MinLen:5'
    ],
    // ..

Classes containing predefined rules can be added. They have to implement public, static methods returning a reference to a function:

    class MyClass {
        public static function ruleMyTest($arg1, $arg2) {
            return function ($input, \DataFilter\Rule $rule = null, \DataFilter\Attribute $attrib = null, \DataFilter\Profile $profile = null) {
                return true;
            }
        }
    }

The custom classes need to be registered and can be used by the name of the method:

    // ..
    'ruleClasses' => ['\\MyClass'],
    'attribs' => [
        'attribName'  => 'MyTest:foo:bar'
    ],
    // ..

### Custom check functions

Custom check functions (either Closure or callable array) can be added inline:

    // ..
    'attribs' => [
        'attribName'  => ['\\MyClass', 'myMethod'],
        'attribName2' => function($input, ..) {
            return true;
        }
    ],
    // ..


### Complex format

The complex format supports a close control over each attribute and rule. Also multiple rules per attribute can be used.

    'attribs' => [
        'attribName' => [

            // whether required
            'required' => true,

            // whether any (first) positive rule match validates argument
            'matchAny' => false,

            // default value is set if attribute NOT given (empty input is still given!). Implies optional (not required)
            'default' => null,

            // default missing error text
            'missing' => 'This attribute is missing',

            // list of ules
            'rules' => [
                'ruleName' => [

                    // either a Closure (or callable array or a named function)
                    'constraint' => $constraint,

                    // custom error message if rule fails
                    'error' => 'On error show this message',

                    // whether ignore this rule on empty input
                    'skipEmpty' => false,

                    // whether this rule sets the result valid and stops further rules
                    'sufficient' => false
                ]
            ],

            // dependencies: see explanation below
            'dependent' => [
                'onSomeInput' => ['otherField1', 'otherField2']
            ]
        ]
    ]

#### Attribute dependencies

Dependencies are best explained by the common password case. Assume you have a formular in which an input named `password` and an input named `password_new` exists. If `password` is given, `password_new` should be as well. In this case, you would create a dependency from `password` to `password_new` like so:

    'attribs' => [

        // the password input
        'password' => [

            // not required itself
            'required' => false,

            // ..
            'dependent' => [
                '*' => ['password_new']
            ]
            //..
        ],

        // default: password_new is optional
        'password_new' => false,
        // ..
    ]

The left-hand value of `dependent` respresents the input on which other attributes (right-hand array) will become dependent. `*` is a special case, meaning: "on any input". If more than one dependency is given, `*` is used if no other matches but input is given.

Additionally there are `dependentRegex`, which work the same way but having regular expressions on the left-hand side:

    'attribs' => [

        // the password input
        'password' => [
            // ..
            'dependentRegex' => [
                '/./' => ['password_new']
            ]
            //..
        ],

        // default: password_new is optional
        'password_new' => false,
        // ..
    ]

Dependencies can be useful in the context of conditional formular parts (eg if a radio input switches a part of the formular on or off).
