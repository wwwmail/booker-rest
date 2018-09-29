<?php

namespace Application\Entity;

class Rooms extends Base {

    const TABLE_NAME = 'app_rooms';

    public $id;
    public $name;

    /**
     * @var array mapping from key  to column (values).
     */
    protected $mapping = [
        'id' => 'id',
        'name' => 'name'
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

}
