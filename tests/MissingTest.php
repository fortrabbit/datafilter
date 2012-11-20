<?php

namespace DataFilter;

class MissingTest extends \PHPUnit_Framework_TestCase
{


    public function testRequiredSimple1()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true,
                'attrib2' => false
            ]
        ]);
        $this->assertFalse($df->check(['attrib2' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'foo']));
    }

    public function testRequiredSimple2()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'required' => true
                ],
                'attrib2' => [
                    'required' => false
                ]
            ]
        ]);
        $this->assertFalse($df->check(['attrib2' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'foo']));
    }

    public function testRequiredDependent()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'required' => true,
                    'rules' => [
                        'rule1' => [
                            'constraint' => function($in) {
                                return true;
                            }
                        ]
                    ],
                    'dependent' => [
                        'foo2' => ['attrib2'],
                        'foo3' => ['attrib3'],
                        'foo4' => ['attrib2', 'attrib3'],
                        '*'    => ['attrib4']
                    ]
                ],
                'attrib2' => false,
                'attrib3' => false,
                'attrib4' => false,
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'foo2']));
        $this->assertTrue($df->check(['attrib1' => 'foo2', 'attrib2' => 'x']));

        $this->assertFalse($df->check(['attrib1' => 'foo3', 'attrib2' => 'x']));
        $this->assertTrue($df->check(['attrib1' => 'foo3', 'attrib3' => 'x']));

        $this->assertFalse($df->check(['attrib1' => 'foo4', 'attrib2' => 'x']));
        $this->assertFalse($df->check(['attrib1' => 'foo4', 'attrib3' => 'x']));
        $this->assertTrue($df->check(['attrib1' => 'foo4', 'attrib2' => 'x', 'attrib3' => 'x']));

        $this->assertFalse($df->check(['attrib1' => 'bar']));
        $this->assertTrue($df->check(['attrib1' => 'bar', 'attrib4' => 'x']));
    }

    public function testRequiredDependentRegex()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'required' => true,
                    'rules' => [
                        'rule1' => [
                            'constraint' => function($in) {
                                return true;
                            }
                        ]
                    ],
                    'dependentRegex' => [
                        '/^f/' => ['attrib2'],
                        '/^b/' => ['attrib3'],
                    ]
                ],
                'attrib2' => false,
                'attrib3' => false,
            ]
        ]);
        $this->assertTrue($df->check(['attrib1' => 'xxx']));

        $this->assertFalse($df->check(['attrib1' => 'fffff']));
        $this->assertTrue($df->check(['attrib1' => 'fffff', 'attrib2' => 'x']));

        $this->assertFalse($df->check(['attrib1' => 'bbb']));
        $this->assertTrue($df->check(['attrib1' => 'bbb', 'attrib3' => 'x']));
    }

}


