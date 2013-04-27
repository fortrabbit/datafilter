<?php

namespace DataFilter;

class AttribPathTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleMultiAttribPattern()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1.*' => true
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => ['foo' => 'bar']]));
    }

    public function testSimpleMultiAttribPatternFail()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1.*' => true
            ]
        ]);
        $this->assertFalse($df->check(['attrib2' => ['foo' => 'bar']]));
    }

    public function testComplexMultiAttribPattern()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1.*' => [
                    'required' => true,
                    'rules' => [
                        'test' => [
                            'constraint' => function ($val) {
                                return in_array($val, array('foo', 'bar'));
                            }
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => ['val1' => 'bar', 'val2' => 'foo']]));
    }

    public function testComplexMultiAttribPatternFail()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1.*' => [
                    'required' => true,
                    'rules' => [
                        'test' => [
                            'constraint' => function ($val) {
                                return in_array($val, array('foo', 'bar'));
                            }
                        ]
                    ]
                ]
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => ['val1' => 'bar', 'val2' => 'arg']]));
    }



}


