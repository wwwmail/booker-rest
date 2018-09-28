<?php

namespace Application\Entity;

class Events extends Base {

    const TABLE_NAME = 'app_events';

    public $id = '';
    public $recursion='';
    public $recursion_id = '';
    public $user_id = '';
    public $room_id = '';
    public $description = '';
    public $date = '';
    public $starttime = '';
    public $endtime = '';
    public $created = '';
    protected $mapping = [
        'id' => 'id',
        'recursion' => 'recursion',
        'recursion_id' => 'recursionId',
        'user_id' => 'userId',
        'room_id' => 'roomId',
        'description' => 'description',
        'date' => 'date',
        'starttime' => 'starttime',
        'endtime' => 'endtime',
        'created' => 'created',
    ];
    
    public function getRecursion(): int
    {
        return $this->recursion;
    }

    public function setRecursion($id)
    {
        $this->recursion = (int)$id;
    }
    
    public function getRecursionId(): int
    {
        return $this->recursion_id;
    }

    public function setRecursionId($id)
    {
        $this->recursion_id = (int)$id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId($id)
    {
        $this->user_id = (int)$id;
    }

    public function getRoomId(): int
    {
        return $this->room_id;
    }

    public function setRoomId($id)
    {
        $this->room_id = (int)$id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription($description)
    {

        $this->description = (string) $description;
    }
    
    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate($date)
    {

        $this->date = (string)$date;
    }

    public function getStarttime(): string
    {
        return $this->starttime;
    }

    public function setStarttime($time)
    {
        $this->starttime = (string)$time;
    }

    public function getEndtime(): string
    {
        return $this->endtime;
    }

    public function setEndtime($time)
    {
        $this->endtime = (string)$time;
    }

    public function getCreated(): string
    {
        return $this->created;
    }

    public function setCreated($time)
    {
        $this->created = (string)$time;
    }

}
