<?php

namespace DataFilter;

class BasicTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $exception = false;
        try {
            $df = new \DataFilter\Profile([]);
        } catch(\Exception $e) {
            error_log("Error in create: $e");
            $exception = true;
        }
        $this->assertFalse($exception);
    }

    public function testCreateAttribs()
    {
        $exception = false;
        try {
            $df = new \DataFilter\Profile([
                'attribs' => [
                    'attrib1' => true,
                    'attrib2' => false
                ]
            ]);
        } catch(\Exception $e) {
            error_log("Error in create: $e");
            $exception = true;
        }
        $this->assertFalse($exception);
    }

    public function testFilterSimple1()
    {
        $exception = false;
        $checkRes = false;
        try {
            $df = new \DataFilter\Profile([
                'attribs' => [
                    'attrib1' => true,
                    'attrib2' => false
                ]
            ]);
            $input = [
                'attrib1' => 'bla'
            ];
            if ($df->check($input)) {
                $checkRes = true;
            }
            else {
                print_r($df);
            }
        } catch(\Exception $e) {
            error_log("Error in create: $e");
            $exception = true;
        }
        $this->assertTrue(!$exception && $checkRes);
    }

    public function testFilterSimple2()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true,
                'attrib2' => false
            ]
        ]);
        $input = [
            'attrib2' => 'bla'
        ];
        $this->assertFalse($df->check($input));
    }

    public function testCanMissing()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true,
                'attrib2' => false
            ]
        ]);
        $input = [
            'attrib2' => 'bla'
        ];
        $res = $df->run($input);
        $this->assertTrue($res->hasError());
        $this->assertEquals($res->getErrorTexts(' - '), 'Attribute "attrib1" is missing');
    }

    public function testCanSimpleConstraint()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => function($input) {
                    return $input === 'bar';
                }
            ]
        ]);
        $this->assertFalse($df->check(['attrib1' => 'foo']));
        $this->assertTrue($df->check(['attrib1' => 'bar']));
    }

}