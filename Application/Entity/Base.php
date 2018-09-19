<?php

namespace Application\Entity;

class Base {

    protected $id = 0;
    protected $mapping = ['id' => 'id'];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    public static function arrayToEntity($data, Base $instance)
    {
        if ($data && is_array($data)) {
            foreach ($instance->mapping as $dbColumn => $propertyName) {
                $method = 'set' . ucfirst($propertyName);
                $instance->$method($data[$dbColumn]);
            }
            return $instance;
        }
        return FALSE;
    }

    public function entityToArray()
    {
        $data = array();
        foreach ($this->mapping as $dbColumn => $propertyName) {
            $method = 'get' . ucfirst($propertyName);
            $data[$dbColumn] = $this->$method() ?? NULL;
        }
        return $data;
    }

    protected static function isMultiArray($arr)
    {
        rsort($arr);
        return isset($arr[0]) && is_array($arr[0]);
    }

}
