<?php

namespace DataFilter;

class ErrorTest extends \PHPUnit_Framework_TestCase
{


    public function testDefault()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true,
                'attrib2' => function($in) { return 'x' === $in; }
            ]
        ]);
        $res = $df->run(['attrib2' => 'foo']);
        $this->assertEquals($res->getErrorTexts(':'), 'Attribute "attrib2" does not match "default":Attribute "attrib1" is missing');
        //print_r($res);
    }

    public function testOtherDefault()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => true,
                'attrib2' => function($in) { return 'x' === $in; }
            ],
            'missingTemplate' => 'Missing :attrib:',
            'errorTemplate' => 'Failed :attrib:',
        ]);
        $res = $df->run(['attrib2' => 'foo']);
        $this->assertEquals($res->getErrorTexts(':'), 'Failed attrib2:Missing attrib1');
    }

    public function testAttribOverwrite()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' => [
                    'required' => true,
                    'missing'  => 'We are missing :attrib:'
                ],
                'attrib2' => [
                    'rules' => [
                        'isNotX' => [
                            'constraint' => function($in) { return 'x' === $in; },
                            'error'      => "Oops, :attrib: not X"
                        ]
                    ]
                ]
            ],
            'missingTemplate' => 'Missing :attrib:',
            'errorTemplate' => 'Failed :attrib:',
        ]);
        $res = $df->run(['attrib2' => 'foo']);
        $this->assertEquals($res->getErrorTexts(':'), 'Oops, attrib2 not X:We are missing attrib1');
    }

    public function testErrorsByAttrib()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib1' =>true,
                'attrib2' => [
                    'rules' => [
                        'isNotX' => function($in) { return 'x' === $in; }
                    ]
                ],
                'attrib3' => false
            ],
            'missingTemplate' => 'Missing :attrib:',
            'errorTemplate' => 'Failed :attrib:',
        ]);
        $res = $df->run(['attrib2' => 'foo']);

        // get all
        $errors = $res->getAllErrors();
        $this->assertTrue(isset($errors['attrib1']) && isset($errors['attrib2']) && count(array_keys($errors)) === 2);
        $this->assertEquals($errors['attrib1'], 'Missing attrib1');
        $this->assertEquals($errors['attrib2'], 'Failed attrib2');

        // get invalid
        $errors = $res->getInvalidErrors();
        $this->assertTrue(isset($errors['attrib2']) && count(array_keys($errors)) === 1);
        $this->assertEquals($errors['attrib2'], 'Failed attrib2');

        // get missing
        $errors = $res->getMissingErrors();
        $this->assertTrue(isset($errors['attrib1']) && count(array_keys($errors)) === 1);
        $this->assertEquals($errors['attrib1'], 'Missing attrib1');

        // check all
        $this->assertTrue($res->hasError());

        // check single
        $this->assertTrue($res->hasError('attrib1') && $res->hasError('attrib2') && !$res->hasError('attrib3'));
    }

    public function testErrorInheritance()
    {
        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib2' => [
                    'error' => 'From Attrib',
                    'rules' => [
                        'isNotX' => [
                            'constraint' => function($in) { return 'x' === $in; },
                            'error'      => 'From Rule'
                        ]
                    ]
                ],
                'attrib3' => false
            ],
            'errorTemplate' => 'From Profile',
        ]);
        $res = $df->run(['attrib2' => 'foo']);
        $errors = $res->getAllErrors();
        $this->assertEquals($errors['attrib2'], 'From Rule');


        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib2' => [
                    'error' => 'From Attrib',
                    'rules' => [
                        'isNotX' => [
                            'constraint' => function($in) { return 'x' === $in; },
                        ]
                    ]
                ],
                'attrib3' => false
            ],
            'errorTemplate' => 'From Profile',
        ]);
        $res = $df->run(['attrib2' => 'foo']);
        $errors = $res->getAllErrors();
        $this->assertEquals($errors['attrib2'], 'From Attrib');


        $df = new \DataFilter\Profile([
            'attribs' => [
                'attrib2' => [
                    'rules' => [
                        'isNotX' => [
                            'constraint' => function($in) { return 'x' === $in; },
                        ]
                    ]
                ],
                'attrib3' => false
            ],
            'errorTemplate' => 'From Profile',
        ]);
        $res = $df->run(['attrib2' => 'foo']);
        $errors = $res->getAllErrors();
        $this->assertEquals($errors['attrib2'], 'From Profile');
    }

}


