<?php
// autoload classes 
namespace Application\Autoload;

class Loader
{
    const UNABLE_TO_LOAD = 'Unable to load class';
    
    protected static $dirs = array();
    protected static $registered = 0;
    public function __construct(array $dirs = array())
    {
        self::init($dirs);
    }
    public static function addDirs($dirs)
    {
        if (is_array($dirs)) {
            self::$dirs = array_merge(self::$dirs, $dirs);
        } else {
            self::$dirs[] = $dirs;
        }
    }

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
    public static function autoLoad($class)
    {
        $success = FALSE;
        $fn = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        //echo $fn; die;
        foreach (self::$dirs as $start) {
            $file = $start . DIRECTORY_SEPARATOR . $fn;
            if (self::loadFile($file)) {
                $success = TRUE;
                break;
            }
            
           // echo $file; die;
        }
        if (!$success) {
            if (!self::loadFile(__DIR__ . DIRECTORY_SEPARATOR . $fn)) {
                return false;
            }
        }
        return $success;
    }
    protected static function loadFile($file)
    {
        if (file_exists($file)) {
            require_once $file;
            return TRUE;
        }
        return FALSE;
    }
}
    
