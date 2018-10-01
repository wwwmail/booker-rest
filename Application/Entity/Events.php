<?php

namespace Application\Entity;

class Events extends Base {

    const TABLE_NAME = 'app_events';

    public $id;
    public $recursion;
    public $recursion_id;
    public $user_id;
    public $room_id;
    public $description;
    public $date;
    public $starttime;
    public $endtime;
    public $created;

    /**
     * @var array mapping from key  to column (values).
     */
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

    /**
     * Get recursion
     * @return int
     */
    public function getRecursion()
    {
        return $this->recursion;
    }

    /**
     * Set recursion
     * @param int $id
     */
    public function setRecursion($id)
    {
        $this->recursion = (int) $id;
    }

    /**
     * Get recursionId
     * @return int
     */
    public function getRecursionId()
    {
        return $this->recursion_id;
    }

    /**
     * Set recursionId
     * @param int $id
     */
    public function setRecursionId($id)
    {
        $this->recursion_id = (int) $id;
    }

    /**
     * Get userId
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Set userId
     * @param int $id
     */
    public function setUserId($id)
    {
        $this->user_id = (int) $id;
    }

    /**
     * Get roomId
     * @return int
     */
    public function getRoomId(): int
    {
        return $this->room_id;
    }

    /**
     * Set roomId
     * @param int $id
     */
    public function setRoomId($id)
    {
        $this->room_id = (int) $id;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set description
     * @param string $description
     */
    public function setDescription($description)
    {

        $this->description = (string) $description;
    }

    /**
     * Get date
     * @return string 
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Set date
     * @param string $date
     */
    public function setDate($date)
    {

        $this->date = (string) $date;
    }

    /**
     * Get starttime
     * @return string 
     */
    public function getStarttime(): string
    {
        return $this->starttime;
    }

    /**
     * Set starttime datetime
     * @param string $time
     */
    public function setStarttime($time)
    {
        $this->starttime = (string) $time;
    }

    /**
     * Get endtime
     * @return string 
     */
    public function getEndtime(): string
    {
        return $this->endtime;
    }

    /**
     * Set endtime datetime
     * @param string $time
     */
    public function setEndtime($time)
    {
        $this->endtime = (string) $time;
    }

    /**
     * Get created datetime
     * @return string 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created datetime
     * @param string $time
     */
    public function setCreated($time)
    {
        if($time){
            $this->created = (string) $time;
        }
    }

}
