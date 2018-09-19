<?php

namespace Application\Entity;

class Events extends Base {

    const TABLE_NAME = 'app_events';

    public $id = '';
    public $recursion_id = '';
    public $user_id = '';
    public $room_id = '';
    public $description = '';
    public $starttime = '';
    public $endtime = '';
    public $created = '';
    protected $mapping = [
        'id' => 'id',
        'recursion_id' => 'recursionId',
        'user_id' => 'userId',
        'room_id' => 'roomId',
        'description' => 'description',
        'starttime' => 'starttime',
        'endtime' => 'endtime',
        'created' => 'created',
    ];

    public function getRecursionId()
    {
        return $this->recursion_id;
    }

    public function setRecursionId($id)
    {
        $this->recursion_id = $id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($id)
    {
        $this->user_id = $id;
    }

    public function getRoomId()
    {
        return $this->room_id;
    }

    public function setRoomId($id)
    {
        $this->room_id = $id;
    }

    public function getDescription()
    {
        return $this->password;
    }

    public function setDescription($description)
    {

        $this->description = $description;
    }

    public function getStarttime()
    {
        return $this->starttime;
    }

    public function setStarttime($time)
    {
        $this->starttime = $time;
    }

    public function getEndtime()
    {
        return $this->endtime;
    }

    public function setEndtime($time)
    {
        $this->endtime = $time;
    }

    public function getCreated()
    {
        return $this->cre;
    }

    public function setCreated($time)
    {
        $this->created = $time;
    }

}
