<?php

namespace DataFilter;

class LoadJsonTest extends \PHPUnit_Framework_TestCase
{

    public function testLoad()
    {
        include_once __DIR__. '/MyPredefinedFilter.php';
        $df = \DataFilter\Profile::fromJson(__DIR__. '/def.json');
        $this->assertFalse($df->check(['attrib2' => 'u-123']));
        $this->assertTrue($df->check(['attrib1' => 'u-123', 'attrib2' => 'xx']));
    }

}