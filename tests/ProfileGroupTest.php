<?php

namespace DataFilter;

class ProfileGroupTest extends \PHPUnit_Framework_TestCase
{

    public function testMultiProfile()
    {
        $sets = new \DataFilter\ProfileGroup([
            'test1' => [
                'attribs' => [
                    'attrib1' => true,
                    'attrib2' => false
                ],
            ],
            'test2' => [
                'attribs' => [
                    'bla1' => true,
                    'bla2' => true
                ]
            ]
        ]);
        $sets->setProfile('test1');
        $this->assertTrue($sets->check(['attrib1' => 'here']));

        $sets->setProfile('test2');
        $this->assertFalse($sets->check(['attrib1' => 'here']));
        $this->assertTrue($sets->check(['bla1' => 'here', 'bla2' => 'there']));
    }


}


