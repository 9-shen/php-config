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
    public function test_set()
    {
        $inst = Config::getInstance();
        $inst->load('test', [
            'key1' => [
                'key1' => 'value1',
            ],
        ]);

        $inst->set('key1.key1', 'modified');
        $this->assertEquals($inst->get('key1.key1'), 'modified');

        $inst->set('key1.key2', 'modified2');
        $this->assertEquals($inst->get('key1.key2'), 'modified2');

        $inst->set('key2.key1', 'modified3');
        $this->assertEquals($inst->get('key2.key1'), 'modified3');
    }

    public function test_setAttributes()
    {
        if (!is_dir(base_path('config'))) {
            mkdir(base_path('config'), 0777);
        }

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

        unlink($file);
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
            ->createConfigFile('testconfig', [
                'key1' => 'value1',
                'key2' => [
                    'key1' => 'value1',
                    'key2' => 'value2',
                ],
                'key3' => true,
            ]);

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
