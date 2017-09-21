<?php
/**
 * Created by PhpStorm.
 * User: zharov
 * Date: 09.08.2016
 * Time: 9:07
 */

date_default_timezone_set('Europe/Moscow');

define("HEXAGONE_ROOT", dirname(__FILE__));
set_include_path(
    get_include_path() .
    PATH_SEPARATOR . HEXAGONE_ROOT . "/source" .
    PATH_SEPARATOR . HEXAGONE_ROOT . "/tests"
);

class Autoloader {
    public static function autoload ($class) {
        $config = file_get_contents(HEXAGONE_ROOT . '/composer.json');
        if ($config != false) {
            $config = json_decode($config);

            $replace  = (array)$config->autoload->{'psr-4'};
            $search = array_keys($replace);
            $class  = str_replace($search, $replace, $class);
        }

        $paths = explode(PATH_SEPARATOR, get_include_path());
        $file = str_replace("\\", DIRECTORY_SEPARATOR, trim($class, "\\")).".php";
        $file2 = str_replace("_", DIRECTORY_SEPARATOR, trim($class, "_")).".php";

        foreach ($paths as $path) {
            $combined = $path.DIRECTORY_SEPARATOR.$file;
            $combined2 = $path.DIRECTORY_SEPARATOR.$file2;

            if (file_exists($combined)) {
                require_once($combined);
                return;
            }elseif(file_exists($combined2)){
                require_once($combined2);
                return;
            }
        }

        throw new Exception("{$class} not found");
    }
}

spl_autoload_register(array('autoloader', 'autoload'));

