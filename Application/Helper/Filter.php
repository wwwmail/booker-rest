<?php

namespace Application\Helper;

class Filter {

    protected static $instance = null;
    private $_passed = false,
            $_errors = array();

    public static function get_instance()
    {
        if(self::$instance === null)
        {
            self::$instance = new static();
        }
        return self::$instance;
    }
    
            
    public static function check($source, $items = array())
    {
        $filter = self::get_instance();
        foreach ($items as $item => $rules) {
            $value = htmlspecialchars(trim($source[$item]), ENT_QUOTES, 'UTF-8');
            
            foreach ($rules as $rule => $rule_value) {
                $method = "filter_{$rule}";
                if(method_exists($filter, $method)){
                    $filter->$method($value,$rule_value, $item);
                }
            }
        }
        if (empty($filter->_errors)) {
            $filter->_passed = true;
        }
        return $filter;
    }
    private function add_error($error)
    {
        $this->_errors[] = $error;
    }

    public function errors()
    {
        return $this->_errors;
    }

    public function passed()
    {
        return $this->_passed;
    }

    public function filter_required($value, $rule_value, $item)
    {
        if (empty($value)) {
            $this->add_error("{$item} is required.");
        }
    }
    
    public function filter_length_min($value, $rule_value, $item ){
        if (strlen($value) < $rule_value) {
            $this->add_error("{$item} must contain minimum {$rule_value} characters");
        }
    }
    
    public function filter_length_max($value, $rule_value, $item ){
        if (strlen($value) > $rule_value) {
            $this->add_error("{$item} can't contain more than {$rule_value} characters");
        }
    }
    
    public function filter_email($value, $rule_value, $item ){
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->add_error("{$value} is not a valid email address");
        }                      
    }
    
    public function filter_numeric($value, $rule_value, $item)
    {
        if(!ctype_digit($value)) {
            $this->add_error("{$item} contains illegal characters. Only numbers 0-9");
        }
        
    }
    
    public function filter_zirrow_one($value, $rule_value, $item)
    {
        if(!ctype_digit($value)  || ($value != '0' &&  $value != '1')) {
            $this->add_error("{$item} Only numbers 0 or 1");
        }
    }
    
    public function filter_date($value, $rule_value, $item)
    {        
        if (strtotime($value) < strtotime($rule_value)) {
            $this->add_error("{$item} can't less then {$rule_value}");
        }   
    }
    
    public function filter_in_array($value, $rule_value, $item)
    {
        if (in_array($value, $rule_value)) {
            $this->add_error("{$item} can use this type {$rule_value}");
        }
    }

}
