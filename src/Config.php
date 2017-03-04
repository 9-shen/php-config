<?php

//
// Oussama Elgoumri
// contact@sec4ar.com
//
// Thu Mar  2 19:12:27 WET 2017
//

namespace OussamaElgoumri\Config;

class Config
{
    static $instance;

    protected $arr;
    protected $defaults;
    protected $attributes;
    protected $config_file;
    protected $config_file_type;

    private function __wakeup() {  }
    private function __clone() {  }

    /**
     * Initialize.
     */
    private function __construct() 
    {
        $this->arr = new Arr;
    }

    /**
     * Load the configuration.
     *
     * @param string    $filename
     * @param array     $defaults
     */
    public function load($filename = '', $defaults = [])
    {
        if (empty($defaults)) {
            return $this->attributes = [];
        }

        if (!empty($filename)) {
            $this->createConfigFile($filename, $defaults);
        }

        $this->setAttributes($defaults);
    }

    /**
     * Set configuration attributes.
     *
     * @param array     $defaults
     */
    public function setAttributes($defaults)
    {
        $defaults = $this->arr->flatify($defaults);
        $from_dotenv_file = $this->parseDotenvFile();

        if ($this->config_file) {
            if ($this->config_file_type === 'php') {
                $from_config_file = require $this->config_file;
            } elseif ($this->config_file_type === 'json') {
                $from_config_file = json_decode($this->config_file, true);
            } else {
                throw new \Exception("Unsupported config file type: {$this->config_file_type}");
            }
        } else {
            $from_config_file = [];
        }

        foreach ($defaults as $key => $value) {
            if (!empty($from_dotenv_file[$key])) {
                $this->attributes[$key] = $from_dotenv_file[$key];
            } else {
                $val = $this->get($key, $from_config_file);
                $this->attributes[$key] = empty($val) ? $value : $val;
            }
        }
    }

    /**
     * Get key from array as path.
     *
     * @param string    $path
     * @param array     $arr
     *
     * @return mixed
     */
    public function get($path, $arr = [])
    {
        if (empty($path)) {
            return null;
        }

        if (count($arr)) {
            $at = &$arr;

            return $this->arr->get($path, $at);
        }

        if (isset($this->attributes[$path])) {
            return $this->attributes[$path];
        }

        return null;
    }

    /**
     * Set value.
     *
     * @param string    $path
     * @param string    $value
     */
    public function set($path, $value)
    {
        $this->attributes[$path] = $value;
    }

    /**
     * Get the current configuration values.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Parse dotenv file.
     *
     * @return array
     */
    public function parseDotenvFile()
    {
        // Setup .env file:
        $file = base_path('.env');
        $dotenv = [];

        if (!file_exists($file)) {
            return [];
        }

        // Get the content:
        exec("sed -e 's/#.*$//' -e '/^$/d' " . base_path('.env'), $lines);

        if (!count($lines)) {
            return [];
        }

        // Parse the content:
        foreach ($lines as $line) {
            $line = trim($line);

            if (strpos($line, '=') < 1) {
                continue;
            }

            list($key, $value) = explode('=', $line);
            $value = trim(trim($value, '"\''));
            $key = trim($key);

            if (empty($key) || empty($value)) {
                continue;
            }

            if (preg_match('/true/i', $value)) {
                $value = true;
            } elseif (preg_match('/false/i', $value)) {
                $value = false;
            }

            $dotenv[$key] = $value;
        }

        return $dotenv;
    }

    /**
     * Create config filename.
     *
     * @param string    $filename
     */
    public function createConfigFile($filename, $defaults)
    {
        $this->setConfigFilename($filename);

        if (!file_exists($this->config_file)) {
            if ($this->config_file_type === 'php') {
                $content = file_get_contents(__DIR__ . '/../stubs/new-config-file.txt');
                $content = str_replace('%date%', date('D M  j H:i:s T Y'), $content);
                $content = str_replace('%defaults%', $this->arr->getWritableToFile($defaults), $content);
                file_put_contents($this->config_file, $content);
            } elseif ($this->config_file_type === 'json') {
                file_put_contents($this->config_file, json_encode($defaults, JSON_PRETTY_PRINT));
            }
        }
    }

    /**
     * Get filename.
     *
     * @param  string    $filename
     * @return string
     */
    private function setConfigFilename($filename)
    {
        $file = base_path("config/{$filename}");

        if (!is_dir(base_path('config'))) {
            mkdir(base_path('config'), 0777);
        }

        if (preg_match('/.*\.(json|php)$/', $file, $m)) {
            $this->config_file_type = $m[1];
            $this->config_file = $file;
        } else {
            $this->config_file_type = 'php';
            $this->config_file = "{$file}.php";
        }
    }

    /**
     * Get config instance.
     *
     * @return self::$instance
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Kill config
     */
    public function __destruct()
    {
        self::$instance = null;
    }
}
