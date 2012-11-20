<?php

namespace DataFilter;

class FilterResult extends \PHPUnit_Framework_TestCase
{


    public function testGetData()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true,
                'attrib2' => function($in){ return $in === 'bar'; },
                'attrib3' => false,
                'attrib4' => function($in){ return $in === 'argl'; },
            ],

        ]);
        $res = $df->run(['attrib1' => 'foo', 'attrib2' => 'bar', 'attrib3' => 'yadda', 'attrib4' => 'huh', 'attrib5' => 'wtf']);

        $dataValid = $res->getValidData();
        $this->assertEquals(join(':', array_values($dataValid)), 'foo:bar:yadda');

        $dataInvalid = $res->getInvalidData();
        $this->assertEquals(join(':', array_values($dataInvalid)), 'huh');

        $dataUnknown = $res->getUnknownData();
        $this->assertEquals(join(':', array_values($dataUnknown)), 'wtf');

        $dataAll = $res->getAllData();
        $this->assertEquals(join(':', array_values($dataAll)), 'foo:bar:yadda:huh:wtf');
    }


}


