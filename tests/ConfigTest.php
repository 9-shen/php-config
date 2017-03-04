<?php

//
// Oussama Elgoumri
// contact@sec4ar.com
//
// Thu Mar  2 19:14:57 WET 2017
//

namespace OussamaElgoumri\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function test_setAttributes()
    {
        $inst = Config::getInstance();
        $file = base_path('config/test.php');

        if (file_exists($file)) {
            unlink($file);
        }

        if (file_exists(base_path('.env'))) {
            unlink(base_path('.env'));
        }
        
        $content = <<<CONTENT
<?php

return [
    'key1' => [
        'key1' => 'value',
        'key2' => [
            'key3' => '',
            'key4' => 12,
        ],
    ],
    'key2' => true,
    'key3' => '',
    'key4' => 'value2',
    'fake' => 'should not read me',
];
CONTENT;

        file_put_contents($file, $content);
        file_put_contents(base_path('.env'),
            'key1.key1="modified"' . PHP_EOL .
            'key4="modified2"'
        );
        $inst->setAttributes('test', [
            'key1' => [
                'key1' => 'default',
                'key2' => 'default',
            ],
            'key2' => false,
            'key3' => 'default',
            'key4',
            'key5' => [
                'key1' => 'value',
            ],
        ]);

        $results = $inst->getAttributes();
        $this->assertEquals($results, [
            'key1.key1' => 'modified',
            'key1.key2' => [
                'key3' => '',
                'key4' => 12,
            ],
            'key2' => true,
            'key3' => 'default',
            'key4' => 'modified2',
            'key5.key1' => 'value',
        ]);
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

        $flatified = Config::getInstance()->flatify($arr);
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
        $inst = Config::getInstance();
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

        $value = $inst->get('key5.key1', $arr);
        $this->assertNull($value);

        $value = $inst->get('key3', $arr);
        $this->assertNull($value);

        $value = $inst->get('key1.key1', $arr);
        $this->assertEquals($value, ['key1' => 'value', 'key2' => false]);

        $value = $inst->get('key1.key1.key1', $arr);
        $this->assertEquals($value, 'value');

        $value = $inst->get('key2', $arr);
        $this->assertEquals($value, 'value2');

        $value = $inst->get('key4', $arr);
        $this->assertNull($value);

        $value = $inst->get(null);
        $this->assertNull($value);
    }

    public function test_parseDotenvFile()
    {
        $file = base_path('.env');

        if (file_exists($file)) {
            unlink($file);
        }

        file_put_contents(
            $file,
            'path.path1.path2=value1' . PHP_EOL .
            'path3=value2' . PHP_EOL . 
            'path4.path5="value3"' . PHP_EOL . 
            '=value' . PHP_EOL . 
            'path6=' . PHP_EOL .
            'justwrong' . PHP_EOL . 
            'path.path1.path3=true' 
        );
        $results = Config::getInstance()->parseDotenvFile();
        $this->assertEquals($results, [
            'path.path1.path2' => 'value1',
            'path3' => 'value2',
            'path4.path5' => 'value3',
            'path.path1.path3' => true,
        ]);
    }

    public function test_createConfigFile()
    {
        $file = base_path('config/testconfig.php');

        if (file_exists($file)) {
            unlink($file);
        } 

        Config::getInstance()
            ->createConfigFile('testconfig');

        $this->assertFileExists($file);
        $this->assertGreaterThan(0, filesize($file));
        unlink($file);
    }

    public function test_getInstance()
    {
        $c1 = Config::getInstance();
        $c2 = Config::getInstance();

        $this->assertEquals(spl_object_hash($c1), spl_object_hash($c2));
    }

    public function tearDown()
    {
        Config::getInstance()->__destruct();
    }
}
