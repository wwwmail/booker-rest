<?php

namespace Application\Helper;

class Text{

    private static $array_messages;
    
    private static $flag = 0;
    
    /**
     * Get messages from file
     * @param type $string
     * @return string
     */
    public static function get($string)
    {

        if (self::$flag == 0) {
            require_once __DIR__.'/../../config/messages.php';
            self::$array_messages = $messages;
            self::$flag++;
        }
       if(self::$array_messages[$string]){
           return self::$array_messages[$string];
       }else{
           return 'NOT FOUND TRANSLATE';
       }
    }
}