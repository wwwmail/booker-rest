<?php

// autoload classes 

namespace Application\Autoload;

class Loader {

    const UNABLE_TO_LOAD = 'Unable to load class';

    /**
     * @var array dirs for load classes.
     */
    protected static $dirs = array();

    /**
     * @var integer check init start spl_autoload_register
     */
    protected static $registered = 0;

    /**
     * @access public
     */
    public function __construct(array $dirs = array())
    {
        self::init($dirs);
    }

    /**
     * Add dirs to init loader 
     * @param string|arrray $dirs
     */
    public static function addDirs($dirs)
    {
        if (is_array($dirs)) {
            self::$dirs = array_merge(self::$dirs, $dirs);
        } else {
            self::$dirs[] = $dirs;
        }
    }

    /**
     * Load classes use autoLoad
     * @param arrray $dirs
     */
    public static function init($dirs = array())
    {
        if ($dirs) {
            self::addDirs($dirs);
        }
        if (self::$registered == 0) {
            spl_autoload_register(__CLASS__ . '::autoload');
            self::$registered++;
        }
    }

    /**
     * Load file use loadFile
     * @param class $class
     * return true|false
     */
    public static function autoLoad($class)
    {
        $success = FALSE;
        $fn = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        foreach (self::$dirs as $start) {
            $file = $start . DIRECTORY_SEPARATOR . $fn;
            if (self::loadFile($file)) {
                $success = TRUE;
                break;
            }
        }
        if (!$success) {
            if (!self::loadFile(__DIR__ . DIRECTORY_SEPARATOR . $fn)) {
                return false;
            }
        }
        return $success;
    }

    /**
     * check if file exist and include file
     * @param string $file
     * return true|false
     */
    protected static function loadFile($file)
    {
        if (file_exists($file)) {
            require_once $file;
            return TRUE;
        }
        return FALSE;
    }

}
