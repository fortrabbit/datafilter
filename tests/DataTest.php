<?php

namespace DataFilter;

use \DataFilter\Util as U;

class DataTest extends \PHPUnit_Framework_TestCase
{

    public function testMultiLevel()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'level1' => false,
                'Object1.level2' => false,
                'Object2.Sub1.level3' => false,
                'Object3.Sub2.SubSub2.level4' => false
            ],
        ]);
        $res = $df->run([
            'level1'  => 'l1',
            'Object1' => [
                'level2' => 'l2'
            ],
            'Object2' => [
                'Sub1' => [
                    'level3' => 'l3'
                ]
            ],
            'Object3' => [
                'Sub2' => [
                    'SubSub2' => [
                        'level4' => 'l4'
                    ]
                ]
            ]
        ]);
        $this->assertEquals(
            json_encode($res->getAllData()),
            '{"level1":"l1","Object1.level2":"l2","Object2.Sub1.level3":"l3","Object3.Sub2.SubSub2.level4":"l4"}'
        );
    }

    public function testMultiLevel2()
    {
        U::$FLATTEN_SEPARATOR = '::';
        $df = new \DataFilter\Profile([
            'attribs' => [
                'level1' => false,
                'Object1::level2' => false,
                'Object2::Sub1::level3' => false,
                'Object3::Sub2::SubSub2::level4' => false
            ],
        ]);
        $res = $df->run([
            'level1'  => 'l1',
            'Object1' => [
                'level2' => 'l2'
            ],
            'Object2' => [
                'Sub1' => [
                    'level3' => 'l3'
                ]
            ],
            'Object3' => [
                'Sub2' => [
                    'SubSub2' => [
                        'level4' => 'l4'
                    ]
                ]
            ]
        ]);
        $this->assertEquals(
            json_encode($res->getAllData()),
            '{"level1":"l1","Object1::level2":"l2","Object2::Sub1::level3":"l3","Object3::Sub2::SubSub2::level4":"l4"}'
        );
    }

}