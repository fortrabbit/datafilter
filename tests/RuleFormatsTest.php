<?php

namespace DataFilter;

class RuleFormatsTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleBoolFormat()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true
            ]
        ]);
        $this->assertFalse($df->check(['attrib2' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'bar']));
    }

    public function testSimpleFuncRefFormat()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => function($in) {
                    return $in === 'bar';
                }
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'bar']));
    }

    public function testSimpleStaticCallbackFormat()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => array('\\DataFilter\\RuleFormatsTest', 'staticCallbackTest')
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'bar']));
    }
    public static function staticCallbackTest($in) {
        return $in === 'bar';
    }

    public function testSimpleObjCallbackFormat()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => array($this, 'objectCallbackTest')
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'bar']));
    }
    public function objectCallbackTest($in) {
        return $in === 'bar';
    }

    public function testComplexRules1()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => array('\\DataFilter\\RuleFormatsTest', 'staticCallbackTest')
                    ]
                ]
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'bar']));
    }

    public function testComplexRules2()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'rules' => [
                        'rule1' => [
                            'constraint' => array('\\DataFilter\\RuleFormatsTest', 'staticCallbackTest')
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'bar']));
    }


}


