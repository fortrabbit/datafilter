<?php

namespace DataFilter;

use \DataFilter\Util as U;

class ProgrammaticTest extends \PHPUnit_Framework_TestCase
{

    public function testManipulateRules()
    {
        $df = new \DataFilter\Profile();

        $df->setAttrib('attrib1', false);

        // all optional
        $this->assertTrue($df->check([]));

        // one required
        $df->getAttrib('attrib1')->setRequired(true);
        $this->assertFalse($df->check([]));

        // satisfy required
        $this->assertTrue($df->check(['attrib1' => 'foo']));

        // add rule
        $df->getAttrib('attrib1')->setRule('minLength', 'LenMin:5');
        $this->assertFalse($df->check(['attrib1' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'foobar']));

        // remove role again
        $df->getAttrib('attrib1')->removeRule('minLength');
        $this->assertTrue($df->check(['attrib1' => 'foo']));

        // remove required again
        $df->getAttrib('attrib1')->setRequired(false);
        $this->assertTrue($df->check([]));
    }

    public function testToggleFilters()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'required' => true,
                    'preFilters' => [
                        function($in) {
                            return '>'. $in;
                        }
                    ],
                    'postFilters' => [
                        function ($in) {
                            return $in. '<';
                        }
                    ]
                ]
            ],
            'preFilters' => [
                function ($in) {
                    return '['. $in;
                }
            ],
            'postFilters' => [
                function ($in) {
                    return $in. ']';
                }
            ]
        ]);

        $res = $df->run(['attrib1' => 'foo']);
        $this->assertFalse($res->hasError());

        $value = $res->getData('attrib1');
        $this->assertEquals($value, '[>foo<]');

        $df->getAttrib('attrib1')->setNoFilters(true);
        $res = $df->run(['attrib1' => 'foo']);
        $value = $res->getData('attrib1');
        $this->assertEquals($value, 'foo');

    }


}