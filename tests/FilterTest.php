<?php

namespace DataFilter;

class FilterTest extends \PHPUnit_Framework_TestCase
{

    public function testGlobalFilters()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true,
                'attrib2' => false
            ],
            'preFilters' => [
                function($in) {
                    return 'X'. $in;
                }
            ],
            'postFilters' => [
                function($in) {
                    return $in. 'Y';
                }
            ]
        ]);
        $res = $df->run(['attrib1' => 'foo', 'attrib2' => 'bar', 'attrib3' => 'unknown']);
        $this->assertFalse($res->hasError());
        $data = $res->getAllData();
        $this->assertFalse(empty($data));
        $this->assertTrue(isset($data['attrib1']) && isset($data['attrib2']) && isset($data['attrib3']));
        $this->assertEquals(
            $data['attrib1']. ':'. $data['attrib2']. ':'. $data['attrib3'],
            'XfooY:XbarY:Xunknown'
        );
    }


    public function testAttribFilters()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'preFilters' => [
                        function($in) {
                            return 'X'. $in;
                        }
                    ],
                    'postFilters' => [
                        function($in) {
                            return $in. 'Y';
                        }
                    ]
                ],
                'attrib2' => [
                    'postFilters' => [
                        function($in) {
                            return $in. 'Y';
                        }
                    ]
                ]
            ],

        ]);
        $res = $df->run(['attrib1' => 'foo', 'attrib2' => 'bar', 'attrib3' => 'unknown']);
        $this->assertFalse($res->hasError());
        $data = $res->getAllData();
        $this->assertFalse(empty($data));
        $this->assertTrue(isset($data['attrib1']) && isset($data['attrib2']) && isset($data['attrib3']));
        $this->assertEquals(
            $data['attrib1']. ':'. $data['attrib2']. ':'. $data['attrib3'],
            'XfooY:barY:unknown'
        );
    }


    public function testPFBasicTrim()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true
            ],
            'preFilters' => 'Trim',
        ]);
        $res = $df->run(['attrib1' => '  ok  ']);
        $data = $res->getValidData();
        $this->assertTrue(isset($data['attrib1']));
        $this->assertEquals($data['attrib1'], 'ok');
    }


    public function testPFBasicUrlPart()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true
            ],
            'preFilters' => ['UrlPart']
        ]);
        $res = $df->run(['attrib1' => 'HelloThere']);
        $data = $res->getValidData();
        $this->assertTrue(isset($data['attrib1']));
        $this->assertEquals($data['attrib1'], 'hellothere');

        $res = $df->run(['attrib1' => 'What are you doing?']);
        $data = $res->getValidData();
        $this->assertTrue(isset($data['attrib1']));
        $this->assertEquals($data['attrib1'], 'what-are-you-doing');
    }


    public function testPFBasicUrlPartUnicode()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true
            ],
            'preFilters' => ['UrlPartUnicode'],
        ]);
        $res = $df->run(['attrib1' => '&&xجx']);
        $data = $res->getValidData();
        $this->assertTrue(isset($data['attrib1']));
        $this->assertEquals($data['attrib1'], 'xجx');
    }

    public function testCustomFilter1()
    {
        include_once __DIR__. '/MyPredefinedFilter.php';
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true
            ],
            'preFilters' => ['MyFilter'],
            'filterClasses' => ['\\DataFilter\\MyPredefinedFilter']
        ]);
        $res = $df->run(['attrib1' => 'howdy']);
        $data = $res->getValidData();
        $this->assertTrue(isset($data['attrib1']));
        $this->assertEquals($data['attrib1'], '[howdy]');
    }

    public function testCustomFilter2()
    {
        include_once __DIR__. '/MyPredefinedFilter.php';
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true
            ],
            'preFilters' => [
                ['\\DataFilter\\MyPredefinedFilter', 'myFilter'],
                function($in) {
                    return ">$in<";
                }
            ],
        ]);
        $res = $df->run(['attrib1' => 'howdy']);
        $data = $res->getValidData();
        $this->assertTrue(isset($data['attrib1']));
        $this->assertEquals($data['attrib1'], '>[howdy]<');
    }


}


