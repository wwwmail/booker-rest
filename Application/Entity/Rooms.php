<?php

namespace Application\Entity;

class Rooms extends Base {

    const TABLE_NAME = 'app_rooms';

    public $id;
    public $name;
    public $is_active;

    /**
     * @var array mapping from key  to column (values).
     */
    protected $mapping = [
        'id' => 'id',
        'name' => 'name',
        'is_active' => 'isActive',
    ];

    /**
     * Get name for Rooms obj.
     * @return string name of obj Rooms.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name for Rooms obj.
     * @params string $name
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }
    
    /**
     * Set is_active
     * @param int $param
     */
    public function setIsActive($param)
    {
        $this->is_active = (int)$param;
    }
    
    /**
     * Get is active
     * @return int
     */
    public function getIsActive(): int
    {
        return $this->is_active;
    }

}
