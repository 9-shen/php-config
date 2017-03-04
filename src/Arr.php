<?php

//
// Oussama Elgoumri
// contact@sec4ar.com
//
// Sat Mar  4 17:57:46 WET 2017
//

namespace OussamaElgoumri\Config;

class Arr
{
    /**
     * Make array accessible via dot notation.
     *
     * @param  array     $arr
     * @return array
     */
    public function flatify($arr)
    {
        if (empty($arr) || !is_array($arr)) {
            return $arr;
        }

        $path = [];
        foreach ($arr as $key => $value) {
            if (!is_string($key) && is_string($value)) {
                $key = $value;
                $value = '';
            } elseif(!is_string($key)) {
                continue;
            }

            $path[] = $this->_flatify($key, $value);
        }

        $ret = [];
        array_walk_recursive($path, function($item, $key) use(&$ret) {
            $ret[$key] = $item;
        });

        return $ret;
    }

    /**
     * Go on a single row.
     *
     * @param string    $key
     * @param string    $value
     * @param string    $depth
     *
     * @return array
     */
    private function _flatify($key, $value, $depth = '')
    {
        $path = [];
        $depth = empty($depth) ? $key : "{$depth}.{$key}";

        if (!is_array($value)) {
            return [$depth => $value];
        }

        foreach ($value as $k => $v) {
            if (!is_string($k) && is_string($v)) {
                $k = $v;
                $v = '';
            } elseif(!is_string($k)) {
                continue;
            }

            $path[] = $this->_flatify($k, $v, $depth);
        }

        return $path;
    }

    /**
     * Get value by dot notation.
     *
     * @param string    $path
     * @param array     $arr
     *
     * @return mixed
     */
    public function get($path, &$at)
    {
        $found = false;
        $keys = array_filter(explode('.', $path));

        while ($key = array_shift($keys)) {
            if (isset($at[$key])) {
                $found = true;
                $at = &$at[$key];
            } else {
                break;
            }
        }

        if ($found) {
            return $at;
        }

        return null;
    }

    /**
     * Get writable array to file.
     *
     * @param  array     $arr
     * @return string
     */
    public function getWritableToFile($arr, $tabs = 1)
    {
        $content = $this->_getWritableToFile($arr, $tabs);

        return trim($content, PHP_EOL . ',') . ';';
    }

    /**
     * Recursive.
     *
     * @param  array     $arr
     * @return string
     */
    private function _getWritableToFile($arr, $tabs)
    {
        $content = '[' . PHP_EOL;

        foreach ($arr as $key => $value) {
            if (!is_string($key) && is_string($value)) {
                $key = $value;
                $value = '';
            } elseif(!is_string($key)) {
                continue;
            }

            $content .= $this->getTabs($tabs) . '"' . $key . '" => ';

            if (is_array($value)) {
                $content .= $this->_getWritableToFile($value, $tabs + 1);
            } else {
                if (is_bool($value)) {
                    $content .= (bool) $value ? 'true,' : 'false,';
                } elseif (is_null($value)) {
                    $content .= 'null,';
                } elseif (is_string($value)) {
                    $content .= '"' . $value . '",';
                } else {
                    $content .= $value . ',';
                }

                $content .= PHP_EOL;
            }
        }

        return $content .= $this->getTabs($tabs - 1) . '],' . PHP_EOL;
    }

    /**
     * Generate tabs for the generated config file.
     *
     * @param  integer   $tabs
     * @return string
     */
    private function getTabs($tabs)
    {
        $tab_size = '4';
        $ret = '';

        if ($tabs === 0) {
            return $ret; 
        }

        foreach (range(1, $tabs) as $i) {
            $ret .= join('', array_fill(0, 4, ' '));
        }

        return $ret;
    }
}
