<?php

namespace DataFilter;

class LoadJsonTest extends \PHPUnit_Framework_TestCase
{

    public function testLoad()
    {
        include_once __DIR__. '/MyPredefinedFilter.php';
        $raw = file_get_contents(__DIR__. '/def.json');
        $json = json_decode($raw, true);
        $df = new \DataFilter\Profile($json);
        $this->assertFalse($df->check(['attrib2' => 'u-123']));
        $this->assertTrue($df->check(['attrib1' => 'u-123', 'attrib2' => 'xx']));
    }

}