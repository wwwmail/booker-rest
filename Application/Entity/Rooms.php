<?php

namespace Application\Entity;

class Rooms extends Base {

    const TABLE_NAME = 'app_rooms';

   
    public $name = '';

    protected $mapping = [
        'id' => 'id',
        'name' => 'name',
    ];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string)$name;
    }

}
