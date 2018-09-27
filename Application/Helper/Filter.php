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
        return implode(', ', $this->_errors);//$this->_errors;
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

    public function filter_hour($value, $rule_value, $item)
    {
        $hour = date("H", strtotime($value));
        if (    $hour < 8 || $hour > 20 ) {
            $this->add_error("{$item} can't be less then 8 AM and more then 8 PM");
        }
    }
    
    public function filter_minutes($value, $rule_value, $item)
    {
        $last_minutes = strtotime($value);
        
        $first_minutes = strtotime($rule_value);
        
        $difference = (int) $last_minutes - (int) $first_minutes;
        
        $difference = $difference / 60 ;
        
        
        if ( $difference    < 30 ) {
            $this->add_error("{$item} can't be less then 30 from start time");
        }
    }

        public function filter_recursion_type($value, $rule_value, $item)
    {
        //var_dump($value); die;
        switch ($rule_value) {
            case 'weekly':
                if(!in_array($value,[1,2,3,4])){
                     $this->add_error("{$rule_value} can be 1, 2, 3, or 4");
                }
                break;
            case 'bi-weekly':
                if(!in_array($value, [1,2])){
                     $this->add_error("{$rule_value} can be 1 or 2");
                }
                break;
            case 'monthly':
                if(!in_array($value, [1,2])){
                     $this->add_error("{$rule_value} can be 1");
                }
                break;
        }
        
    }

}
