<?php

//
// Oussama Elgoumri
// contact@sec4ar.com
//
// Sat Mar  4 18:03:56 WET 2017
//

namespace OussamaElgoumri\Config;

class ArrTest extends \PHPUnit_Framework_TestCase
{
    public function test_getWritableToFile()
    {
        $arr = [
            'key1' => 'value1',
            'key2' => [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => [
                    'key1' => 'value1',
                    'key2' => 'value2',
                ],
            ],
            'key3' => true,
            'key4' => 14,
            'key5' => false,
            'key6' => null,
            'key7' => 0,
            'key8',
            'key9' => 13.231,
        ];

        $data = (new Arr)->getWritableToFile($arr);
        $this->assertEquals($data, '[
    "key1" => "value1",
    "key2" => [
        "key1" => "value1",
        "key2" => "value2",
        "key3" => [
            "key1" => "value1",
            "key2" => "value2",
        ],
    ],
    "key3" => true,
    "key4" => 14,
    "key5" => false,
    "key6" => null,
    "key7" => 0,
    "key8" => "",
    "key9" => 13.231,
];');
    }

    public function test_flatify()
    {
        $arr = [
            'path' => [
                'path1' => [
                    'path2' => 12,
                    'path3' => true,
                ],
                'path2' => [
                    'path3' => [
                        'path4' => 'value1',
                        'path5' => false,
                        'path6' => 'value3',
                    ],
                    'path4' => [
                        'path5' => 'value1',
                        'path6' => 91,
                        'path7' => 'value3',
                        'path8' => [
                            'path1' => 'value',
                            'path2' => 22,
                            'path3' => true,
                        ]
                    ]
                ],
                'path3' => 'value',
                'path4',
                'path5',
            ],
            'path2',
            'path3' => 'value2',
            'path4' => [
                'path5' => 'value3',
                'path1' => [
                    'path1' => 'value',
                    'path2' => true,
                ],
            ],
        ];

        $flatified = (new Arr)->flatify($arr);
        $this->assertEquals($flatified, [
            'path.path1.path2' => 12,
            'path.path1.path3' => true,
            'path.path2.path3.path4' => 'value1',
            'path.path2.path3.path5' => false,
            'path.path2.path3.path6' => 'value3',
            'path.path2.path4.path5' => 'value1',
            'path.path2.path4.path6' => 91,
            'path.path2.path4.path7' => 'value3',
            'path.path2.path4.path8.path1' => 'value',
            'path.path2.path4.path8.path2' => 22,
            'path.path2.path4.path8.path3' => true,
            'path.path3' => 'value',
            'path.path4' => '',
            'path.path5' => '',
            'path2' => '',
            'path3' => 'value2',
            'path4.path5' => 'value3',
            'path4.path1.path1' => 'value',
            'path4.path1.path2' => true,
        ]);
    } 

    public function test_get()
    {
        $inst = new Arr;
        $arr = [
            'key1' => [
                'key1' => [
                    'key1' => 'value',
                    'key2' => false,
                ],
            ],
            'key2' => 'value2',
            'key3',
        ];

        $value = $inst->get('key1.key1', $arr);
        $this->assertEquals($value, ['key1' => 'value', 'key2' => false]);

        $value = $inst->get('key1.key1.key1', $arr);
        $this->assertEquals($value, 'value');

        $value = $inst->get('key2', $arr);
        $this->assertEquals($value, 'value2');

        $value = $inst->get('key4', $arr);
        $this->assertNull($value);

        $value = $inst->get(null, $arr);
        $this->assertNull($value);

        $value = $inst->get('key5.key1', $arr);
        $this->assertNull($value);

        $value = $inst->get('key3', $arr);
        $this->assertNull($value);
    }
}
