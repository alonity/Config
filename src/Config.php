<?php

/**
 * Config class
 *
 *
 * @author Qexy admin@qexy.org
 *
 * @copyright © 2021 Alonity
 *
 * @package alonity\eventhandler
 *
 * @license MIT
 *
 * @version 1.0.0
 *
 */

namespace alonity\config;

class Config {
    const VERSION = '1.0.0';

    public static $path = '../../../../app';

    private static $loaded = [];



    /**
     * Configs loader
     * Load configs by keys or keys/values
     * If path not set, used default @see self::$path
     *
     * @param array $list
     * @example ['Main', 'Example', 'Other']
     * @example ['Main' => '../../path/to/configs', 'Other' => '../../path/to/other/configs']
     *
     * @return array
     *
    */
    public static function loader(array $list) : array {
        $configs = [];

        foreach($list as $k => $v){
            $path = is_int($k) ? self::$path : $v;

            $name = is_int($k) ? $v : $k;

            $filename = "{$path}/{$name}.php";

            if(!is_file($filename)){ continue; }

            $configs[$name] = (include($filename));
        }

        self::$loaded = array_merge(self::$loaded, $configs);

        return self::$loaded;
    }



    /**
     * Get config array by config name and path
     * If path not set, used default ( see self::$path )
     *
     * @param string $name
     *
     * @param string|null $path
     *
     * @return array|null
    */
    public static function get(string $name, ?string $path = null) : ?array {

        if(isset(self::$loaded[$name])){ return self::$loaded[$name]; }

        if(is_null($path)){
            $path = self::$path;
        }

        $filename = "{$path}/{$name}.php";

        if(!is_file($filename)){ return null; }

        self::$loaded[$name] = (include($filename));

        return self::$loaded[$name];
    }



    /**
     * Get value from config
     * If you want used multidimensional array, you can use . as separator
     *
     * @param string $name
     *
     * @param string $key
     *
     * @return mixed|null
    */
    public static function getValue(string $name, string $key){
        $config = self::get($name);

        if(is_null($config)){
            return null;
        }

        $split = explode('.', $key);

        if(count($split) == 1){
            return $config[$key] ?? null;
        }

        $value = $config;

        foreach($split as $k){
            if(!isset($value[$k])){ return null; }

            $value = $value[$k];
        }

        return $value;
    }



    /**
     * Set value to config
     * If you want used multidimensional array, you can use . as separator
     *
     * @param string $name
     *
     * @param string $key
     *
     * @param mixed $value
     *
     * @return mixed|null
    */
    public static function setValue(string $name, string $key, $value){
        if(!isset(self::$loaded[$name])){
            self::$loaded[$name] = [];
        }

        $split = explode('.', $key);

        if(count($split) == 1){
            self::$loaded[$name][$key] = $value;

            return $value;
        }

        if(!self::isEnableFunction('eval')){
            return null;
        }

        $make = 'self::$loaded[$name][\''.implode('\'][\'',$split ).'\'] = $value;';

        @eval($make);

        return $value;
    }



    /**
     * Set config
     *
     * @param string $name
     *
     * @param array $values
     *
     * @return array
    */
    public static function set(string $name, array $values = []) : array {
        self::$loaded[$name] = $values;

        return $values;
    }



    /**
     * Save config into file
     * If parameter $path is null, used default (@see self::$path)
     *
     * @param string $name
     *
     * @param string|null $path
     *
     * @return bool
    */
    public static function save(string $name, ?string $path = null) : bool {

        if(!isset(self::$loaded[$name])){ return false; }

        if(is_null($path)){
            $path = self::$path;
        }

        $filename = "{$path}/{$name}.php";

        if(!is_dir($path)){
            @mkdir($path, 0777, true);
        }

        $data = "<?php // ".date("d.m.Y H:i:s").PHP_EOL.PHP_EOL;
        $data .= "return ".var_export(self::$loaded[$name], true)."?>".PHP_EOL.PHP_EOL;
        $data .= "?>";

        $put = @file_put_contents($filename, $data, LOCK_EX);

        return $put !== false;
    }



    /**
     * Unset value by key from config array
     * If key or config not found, returned false
     * If you want used multidimensional array, you can use . as separator
     *
     * @param string $name
     *
     * @param string $key
     *
     * @return bool
     */
    public static function unsetValue(string $name, string $key) : bool {
        if(!isset(self::$loaded[$name])){ return false; }

        $split = explode('.', $key);

        if(count($split) == 1){
            unset(self::$loaded[$name][$key]);

            return true;
        }

        if(!self::isEnableFunction('eval')){
            return false;
        }

        $make = 'self::$loaded[$name][\''.implode('\'][\'',$split ).'\']';

        $make = "if(isset({$make})){ unset({$make}); }";

        @eval($make);

        return true;
    }



    /**
     * Delete config with file
     *
     * @param string $name
     *
     * @param string | null $path
     */
    public static function delete(string $name, ?string $path = null) {
        if(isset(self::$loaded[$name])){
            unset(self::$loaded[$name]);
        }

        if(is_null($path)){
            $path = self::$path;
        }

        $filename = "{$path}/{$name}.php";

        if(is_file($filename)){
            @unlink($filename);
        }
    }



    /**
     * Unset config by name
     * Config file will not be deleted
     *
     * @param string $name
     *
     * @return bool
     */
    public static function unset(string $name) : bool {
        if(!isset(self::$loaded[$name])){ return false; }

        unset(self::$loaded[$name]);

        return true;
    }



    /**
     * Check function enable
     *
     * @param string $name
     *
     * @return bool
    */
    private static function isEnableFunction(string $name) : bool {
        $disabled = ini_get('disable_functions');

        return !in_array($name, explode(',', $disabled));
    }
}