<?php

namespace DataFilter;

class PredefinedRuleBasicTest extends \PHPUnit_Framework_TestCase
{


    public function testPRBasicLenMin()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'LenMin:3'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'f']));
        $this->assertTrue($df->check(['attrib1' => 'foo']));
    }

    public function testPRBasicLenMax()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'LenMax:3'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'fooo']));
        $this->assertTrue($df->check(['attrib1' => 'foo']));
    }

    public function testPRBasicLenRange()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'LenRange:3:4'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'foooo']));
        $this->assertFalse($df->check(['attrib1' => 'fo']));
        $this->assertTrue($df->check(['attrib1' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'fooo']));
    }

    public function testPRBasicRegex()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'Regex:/^f:o/'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'bar']));
        $this->assertFalse($df->check(['attrib1' => 'barf']));
        $this->assertTrue($df->check(['attrib1' => 'f:oo']));
        $this->assertTrue($df->check(['attrib1' => 'f:obar']));
    }

    public function testPRBasicNumber()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'Number'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => 123]));
        $this->assertTrue($df->check(['attrib1' => '123']));
        $this->assertTrue($df->check(['attrib1' => 123.1]));
        $this->assertTrue($df->check(['attrib1' => '123.1']));
        $this->assertFalse($df->check(['attrib1' => 'a1']));
    }

    public function testPRBasicInt()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'Int'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => 123]));
        $this->assertTrue($df->check(['attrib1' => '123']));
        $this->assertFalse($df->check(['attrib1' => 123.1]));
        $this->assertFalse($df->check(['attrib1' => '123.1']));
        $this->assertFalse($df->check(['attrib1' => 'a1']));
    }

    public function testPRBasicAlphanum()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'Alphanum'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => 123]));
        $this->assertTrue($df->check(['attrib1' => '123']));
        $this->assertTrue($df->check(['attrib1' => 'a1']));
        $this->assertTrue($df->check(['attrib1' => 'A1']));
        $this->assertFalse($df->check(['attrib1' => 'a-1']));
    }

    public function testPRBasicWebCompliant()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'WebCompliant'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => 123]));
        $this->assertTrue($df->check(['attrib1' => '1-2-A']));
        $this->assertTrue($df->check(['attrib1' => '1.a~3']));
        $this->assertFalse($df->check(['attrib1' => 'a--1']));
        $this->assertFalse($df->check(['attrib1' => '-a-1']));
        $this->assertFalse($df->check(['attrib1' => 'a-1-']));
    }


    public function testPRBasicCustomRuleClass()
    {
        include_once __DIR__. '/MyPredefinedRule.php';
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'MyRule'
                        ]
                    ]
                ]
            ],
            'ruleClasses' => ['\\MyPredefinedRule']
        ]);
        $this->assertTrue($df->check(['attrib1' => 'ok']));
        $this->assertFalse($df->check(['attrib1' => 'other']));
    }


}


