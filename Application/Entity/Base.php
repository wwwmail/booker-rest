<?php

namespace Application\Entity;

class Base {

    protected $id = 0;

    /**
     * @var array mapping from key  to column (values).
     */
    protected $mapping = ['id' => 'id'];

    /**
     * Get id 
     * @return int 
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set id 
     * @param $id int 
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Set instanse object from data in array
     * @return object|false 
     */
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

    /**
     * Set array from object
     * @return array 
     */
    public function entityToArray()
    {
        $data = array();
        foreach ($this->mapping as $dbColumn => $propertyName) {
            $method = 'get' . ucfirst($propertyName);
            $data[$dbColumn] = $this->$method() ?? NULL;
        }
        return $data;
    }

}
