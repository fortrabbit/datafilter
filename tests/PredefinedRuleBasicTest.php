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

    public function testPRBasicInArray()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'InArray:foo:bar:123'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => 123]));
        $this->assertTrue($df->check(['attrib1' => '123']));
        $this->assertTrue($df->check(['attrib1' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'bar']));
        $this->assertFalse($df->check(['attrib1' => 'foobar']));
        $this->assertFalse($df->check(['attrib1' => '234']));
    }

    public function testPRBasicDate()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'Date'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => '2012-01-01']));
        $this->assertTrue($df->check(['attrib1' => '2012-02-01']));
        $this->assertFalse($df->check(['attrib1' => 'foo']));
        $this->assertFalse($df->check(['attrib1' => '2012-02-30']));
        $this->assertFalse($df->check(['attrib1' => '2012-02-40']));
        $this->assertFalse($df->check(['attrib1' => '2012-01-01 20:00:01']));
    }

    public function testPRBasicTime()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'Time'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => '23:10']));
        $this->assertTrue($df->check(['attrib1' => '23:10:20']));
        $this->assertFalse($df->check(['attrib1' => 'foo']));
        $this->assertFalse($df->check(['attrib1' => '2012-01-01']));
    }

    public function testPRBasicDateTime()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'DateTime'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => '2012-01-01']));
        $this->assertTrue($df->check(['attrib1' => '2012-01-01 23:10:20']));
        $this->assertFalse($df->check(['attrib1' => 'foo']));
    }

    public function testPRBasicUrlPart()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'UrlPart'
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

    public function testPRBasicEmail()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => 'Email'
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'user']));
        $this->assertFalse($df->check(['attrib1' => 'user@localhost']));
        $this->assertFalse($df->check(['attrib1' => 'user@...localhost']));
        $this->assertTrue($df->check(['attrib1' => 'user@example.com']));
        $this->assertTrue($df->check(['attrib1' => 'User@EXAMPLE.com']));
        $this->assertTrue($df->check(['attrib1' => 'user@exa-mple.com']));
        $this->assertTrue($df->check(['attrib1' => 'user@exa-m-ple.com']));
        $this->assertTrue($df->check(['attrib1' => 'user+foo@example.com']));
        $this->assertTrue($df->check(['attrib1' => 'user@example.sub.com']));
        $this->assertFalse($df->check(['attrib1' => 'user@example..com']));
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


