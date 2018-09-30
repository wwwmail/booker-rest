<?php

namespace Application\Helper;

use Application\Helper\Text;
class Filter {

    protected static $instance = null;
    private $_passed = false,
            $_errors = array();

    /**
     * @access public
     * @return self instanse
     */
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Use filters for data($sourse) use rule ($items)
     * @param array $source
     * @param array $items
     * @return self instanse
     */
    public static function check($source, $items = array())
    {
        $filter = self::get_instance();
        foreach ($items as $item => $rules) {
            $value = htmlspecialchars(trim($source[$item]), ENT_QUOTES, 'UTF-8');

            foreach ($rules as $rule => $rule_value) {
                $method = "filter_{$rule}";
                if (method_exists($filter, $method)) {
                    $filter->$method($value, $rule_value, $item);
                }
            }
        }
        if (empty($filter->_errors)) {
            $filter->_passed = true;
        }
        return $filter;
    }

    /**
     * Add errors
     * @param string $error
     */
    private function add_error($error)
    {
        $this->_errors[] = $error;
    }

    /**
     * Get errors
     * @return string
     */
    public function errors()
    {
        return implode(', ', $this->_errors);
    }

    /**
     * Get flag
     * @return bool
     */
    public function passed()
    {
        return $this->_passed;
    }

    /**
     * Filter required value
     */
    public function filter_required($value, $rule_value, $item)
    {
        if (empty($value)) {
            $this->add_error("{$item} ". Text::t('is_required'));
        }
    }

    /**
     * Filter min length value
     */
    public function filter_length_min($value, $rule_value, $item)
    {
        if (strlen($value) < $rule_value) {
            $this->add_error("{$item} ".Text::t('must_minimum')." {$rule_value} ".Text::t('characters'));
        }
    }

    /**
     * Filter max length value
     */
    public function filter_length_max($value, $rule_value, $item)
    {
        if (strlen($value) > $rule_value) {
            $this->add_error("{$item} ".Text::t('cant_more')." {$rule_value} ".Text::t('characters'));
        }
    }

    /**
     * Filter email 
     */
    public function filter_email($value, $rule_value, $item)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->add_error("{$value} " .Text::t('is_not_valid_email')); 
        }
    }

    /**
     * Filter if is numeric value
     */
    public function filter_numeric($value, $rule_value, $item)
    {
        if (!ctype_digit($value)) {
            $this->add_error("{$item} " .Text::t('numeric_only'));
        }
    }

    /**
     * Filter value (can 0 or 1)
     */
    public function filter_zirrow_one($value, $rule_value, $item)
    {
        if (!ctype_digit($value) || ($value != '0' && $value != '1')) {
            $this->add_error("{$item} " .Text::t('0_1_only'));
        }
    }

    /**
     * Check if value < || > rule value (date)
     */
    public function filter_date($value, $rule_value, $item)
    {
        if (strtotime($value) < strtotime($rule_value)) {
            $this->add_error("{$item} " .Text::t('cant_less'). " {$rule_value}");
        }
    }

    /**
     * Check if isset value in array
     */
    public function filter_in_array($value, $rule_value, $item)
    {
        if (in_array($value, $rule_value)) {
            $this->add_error("{$item} " .Text::t('cant_use_type'). "  {$rule_value}");
        }
    }

    /**
     * Check if valu limit from 8 to 20
     */
    public function filter_hour($value, $rule_value, $item)
    {
        $hour = date("H", strtotime($value));
        if ($hour < 8 || $hour > 20) {
            $this->add_error("{$item} " .Text::t('hour_limit'));
        }
    }

    /**
     * Check if value and rule value have difference 30
     */
    public function filter_minutes($value, $rule_value, $item)
    {
        $last_minutes = strtotime($value);

        $first_minutes = strtotime($rule_value);

        $difference = (int) $last_minutes - (int) $first_minutes;

        $difference = $difference / 60;


        if ($difference < 30) {
            $this->add_error("{$item} " .Text::t('diference_time_limit'));
        }
    }

    /**
     * Filter recursion value 
     */
    public function filter_recursion_type($value, $rule_value, $item)
    {
        //var_dump($value); die;
        switch ($rule_value) {
            case 'weekly':
                if (!in_array($value, [1, 2, 3, 4])) {
                    $this->add_error("{$rule_value} ".Text::t('1_2_3_4'));
                }
                break;
            case 'bi-weekly':
                if (!in_array($value, [1, 2])) {
                    $this->add_error("{$rule_value} ".Text::t('1_2'));
                }
                break;
            case 'monthly':
                if (!in_array($value, [1, 2])) {
                    $this->add_error("{$rule_value} ".Text::t('_1_'));
                }
                break;
        }
    }

}
