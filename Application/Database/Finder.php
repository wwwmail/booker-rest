<?php

namespace Application\Database;

class Finder {

    public static $sql = '';
    public static $instance = NULL;
    public static $prefix = '';
    public static $where = array();
    public static $control = ['', ''];

    /**
     * Create string prefix select values or select * 
     * @param string $a
     * @param string $cols
     * return self instance
     */
    public static function select($a, $cols = NULL)
    {
        self::$instance = new Finder();
        if ($cols) {
            self::$prefix = 'SELECT ' . $cols . ' FROM ' . $a;
        } else {
            self::$prefix = 'SELECT * FROM ' . $a;
        }
        return self::$instance;
    }

    /**
     * Add where to sql string 
     * @param string $a
     * return self instance
     */
    public static function where($a = NULL)
    {
        self::$where[0] = ' WHERE ' . $a;
        return self::$instance;
    }

    /**
     * Add where to sql string 
     * @param string $a
     * return self instance
     */
    public static function like($a, $b)
    {
        self::$where[] = trim($a . ' LIKE ' . $b);
        return self::$instance;
    }

    /**
     * Add and to sql string 
     * @param string $a
     * return self instance
     */
    public static function andS($a = NULL)
    {
        self::$where[] = trim('AND ' . $a);
        return self::$instance;
    }

    /**
     * Add or to sql string 
     * @param string $a
     * return self instance
     */
    public static function orS($a = NULL)
    {
        self::$where[] = trim('OR ' . $a);
        return self::$instance;
    }

    /**
     * Add in to sql string 
     * @param string $a
     * return self instance
     */
    public static function in(array $a)
    {
        self::$where[] = 'IN ( ' . implode(',', $a) . ' )';
        return self::$instance;
    }

    /**
     * Add not to sql string 
     * @param string $a
     * return self instance
     */
    public static function not($a = NULL)
    {
        self::$where[] = trim('NOT ' . $a);
        return self::$instance;
    }

    /**
     * Add limit to sql string 
     * @param string $limit
     * return self instance
     */
    public static function limit($limit)
    {
        self::$control[0] = 'LIMIT ' . $limit;
        return self::$instance;
    }

    /**
     * Add offset to sql string 
     * @param string $offset
     * return self instance
     */
    public static function offset($offset)
    {
        self::$control[1] = 'OFFSET ' . $offset;
        return self::$instance;
    }

    /**
     * Return sql if $sql or crete sql from components 
     * @param string $sql
     * return string 
     */
    public static function getSql($sql = false)
    {
        if ($sql) {
            return $sql;
        } else {
            self::$sql = self::$prefix
                    . implode(' ', self::$where)
                    . ' '
                    . self::$control[0]
                    . ' '
                    . self::$control[1];
            preg_replace('/ /', ' ', self::$sql);
            return trim(self::$sql);
        }
    }

}
