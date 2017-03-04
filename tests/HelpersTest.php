<?php

//
// Oussama Elgoumri
// contact@sec4ar.com
//
// Sat Mar  4 21:10:05 WET 2017
//

namespace OussamaElgoumri\Config;

class HelpersTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Config__load('test', [
            'key1' => [
                'key1' => 'value',
            ],
        ]);
    }

    public function test_Config__load()
    {
        $this->assertEquals(Config::getInstance()->getAttributes(), ['key1.key1' => 'value']);
    }

    public function test_Config__get()
    {
        $value = Config__get('key1.key1');
        $this->assertEquals($value, 'value');
    }

    public function test_Config__set()
    {
        Config__set('key1.key1', 'modified');
        $this->assertEquals(Config__get('key1.key1'), 'modified');
    }

    public function test_config()
    {
        config('key1.key1', 'modified2');
        $this->assertEquals(config('key1.key1'), 'modified2');
    }
}
