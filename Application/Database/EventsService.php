<?php

namespace Application\Database;

use Application\Helper\Filter;
use Application\Entity\Events;
use PDO;
use Application\Helper\Text;

class EventsService {

    /**
     * @var instanse of Connection $connection
     */
    protected $connection;

    /**
     * @var int first insert in recursive loop
     */
    private $firstInsertId;

    /**
     * @var int last insert id
     */
    public $lastInsertId;

    /**
     * @access public
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get event by id
     * @param int $id
     * return object
     */
    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_events')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return Events::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Events());
    }

    /**
     * Get all events use generator
     */
    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql('SELECT * FROM app_events '
                                        . 'ORDER BY starttime'));
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Events::arrayToEntity($row, new Events());
        }
    }

    /**
     * Get events by room id
     * @param int $id
     * return array
     */
    public function fetchByRoom($id)
    {
        $sql = "SELECT * FROM app_events WHERE room_id = :id ORDER BY starttime";
        $stmt = $this->connection->pdo
                ->prepare(Finder::getSql($sql));
        $stmt->execute(['id' => (int) $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Create event
     * @param array $data
     * @return bool
     */
    public function createEventExec(array $data)
    {
         $sql = 'INSERT INTO app_events SET recursion = :recursion, '
                    . 'recursion_id = :recursion_id, user_id = :user_id,'
                    . 'room_id = :room_id, description = :description,'
                    . 'date = :date, starttime = :starttime,'
                    . 'endtime = :endtime';

            $stmt = $this->connection->pdo->prepare($sql);

            return $stmt->execute(['recursion' => $data['recursion'],
                        'recursion_id' => $data['recursion_id'],
                        'user_id' => $data['user_id'],
                        'room_id' => $data['room_id'],
                        'description' => $data['description'],
                        'date' => $data['date'],
                        'starttime' => date('Y-m-d H:i:s', strtotime($data['starttime'])),
                        'endtime' => date('Y-m-d H:i:s', strtotime($data['endtime'])),
            ]);
    }

    /**
     * Create event 
     * @param array $data
     * return true|string
     */
    public function creatSimpleEvent(array $data)
    {
        $check = $this->checkAvaliableDate($data['starttime'], $data['endtime'], $data['room_id']);
        if (!$check) {
            return $this->createEventExec($data);
        } else {
            $events = '';
            foreach ($check as $item) {
                $events .= " from {$item['starttime']} - to {$item['endtime']}";
            }
            return "event exixt  {$events}";
        }
    }

    /**
     * Chekc type of recursion for add event 
     * @param string $recursion_type
     * @param array $data
     * return bool
     */
    public function creatRecursionEvent($recursion_type, array $data)
    {
        switch ($recursion_type) {
            case 'weekly':
                return $this->creatWeeklyEvent($data);
                break;
            case 'bi-weekly':
                return $this->creatBiWeeklyEvent($data);
                break;
            case 'monthly':
                return $this->creatMonthlyEvent($data);
                break;
            default:
                return $this->creatSimpleEvent($data);
        }
    }

    /**
     * Create weekly events by recursion_value (2,3,4 in month)
     * @param array $data
     * return true|string
     */
    public function creatWeeklyEvent(array $data)
    {
        $success = false;
        $flag = true;
        try {
            $sql = 'INSERT INTO app_events SET recursion = :recursion, '
                    . 'recursion_id = :recursion_id, user_id = :user_id,'
                    . 'room_id = :room_id, description = :description,'
                    . 'date = :date, starttime = :starttime,'
                    . 'endtime = :endtime';
            $stmt = $this->connection->pdo->prepare($sql);

            $events = 'events exist ';
            $this->connection->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $this->connection->pdo->beginTransaction();

            for ($i = 0; $i < $data['recursion_value']; $i++) {

                //       $this->connection->pdo->beginTransaction();
                $date = 7 * $i;
                if ($i == 1) {
                    $this->firstInsertId = $this->connection->pdo->lastInsertId();
                }

                $check = $this->checkAvaliableDate(
                        date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} days")), date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} days")), $data['room_id']);

                $stmt->execute(['recursion' => $data['recursion'],
                    'recursion_id' => $this->firstInsertId,
                    'user_id' => $data['user_id'],
                    'room_id' => $data['room_id'],
                    'description' => $data['description'],
                    'date' => date('Y-m-d H:i:s', strtotime($data['date'] . " +{$date} days")),
                    'starttime' => date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} days")),
                    'endtime' => date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} days")),
                ]);
                if (!$check) {
                    $success = true;
                } else {
                    foreach ($check as $item) {
                        $events .= " from {$item['starttime']} - to {$item['endtime']} ";
                    }
                    $flag = false;
                }
            }

            if (!$flag) {
                $this->connection->pdo->rollBack();
                return $events;
            }

            $this->connection->pdo->commit();
        } catch (PDOException $e) {
            $this->connection->pdo->rollBack();
            echo $e->getMessage();
        }

        return $success;
    }

    /**
     * Create bi-weekly events by recursion_value (1,2 in month)
     * @param array $data
     * return true|string
     */
    public function creatBiWeeklyEvent(array $data)
    {
        $success = false;
        $flag = true;
        try {
            $sql = 'INSERT INTO app_events SET recursion = :recursion, '
                    . 'recursion_id = :recursion_id, user_id = :user_id,'
                    . 'room_id = :room_id, description = :description,'
                    . 'date = :date, starttime = :starttime,'
                    . 'endtime = :endtime';
            $stmt = $this->connection->pdo->prepare($sql);

            $events = 'events exist ';
            $this->connection->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $this->connection->pdo->beginTransaction();

            for ($i = 0; $i <= $data['recursion_value']; $i++) {

                //       $this->connection->pdo->beginTransaction();
                $date = 14 * $i;
                if ($i == 1) {
                    $this->firstInsertId = $this->connection->pdo->lastInsertId();
                }

                $check = $this->checkAvaliableDate(
                        date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} days")), date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} days")), $data['room_id']);

                $stmt->execute(['recursion' => $data['recursion'],
                    'recursion_id' => $this->firstInsertId,
                    'user_id' => $data['user_id'],
                    'room_id' => $data['room_id'],
                    'description' => $data['description'],
                    'date' => date('Y-m-d H:i:s', strtotime($data['date'] . " +{$date} days")),
                    'starttime' => date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} days")),
                    'endtime' => date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} days")),
                ]);
                if (!$check) {
                    $success = true;
                } else {
                    foreach ($check as $item) {
                        $events .= " from {$item['starttime']} - to {$item['endtime']} ";
                    }
                    $flag = false;
                }
            }

            if (!$flag) {
                $this->connection->pdo->rollBack();
                return $events;
            }

            $this->connection->pdo->commit();
        } catch (PDOException $e) {
            $this->connection->pdo->rollBack();
            echo $e->getMessage();
        }

        return $success;
    }

    /**
     * Create monthly events by recursion_value (1 in month)
     * @param array $data
     * return true|string
     */
    public function creatMonthlyEvent(array $data)
    {
        $success = false;
        $flag = true;
        try {
            $sql = 'INSERT INTO app_events SET recursion = :recursion, '
                    . 'recursion_id = :recursion_id, user_id = :user_id,'
                    . 'room_id = :room_id, description = :description,'
                    . 'date = :date, starttime = :starttime,'
                    . 'endtime = :endtime';
            $stmt = $this->connection->pdo->prepare($sql);

            $events = 'events exist ';
            $this->connection->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $this->connection->pdo->beginTransaction();

            for ($i = 0; $i <= $data['recursion_value']; $i++) {

                //       $this->connection->pdo->beginTransaction();
                $date = $i;
                if ($i == 1) {
                    $this->firstInsertId = $this->connection->pdo->lastInsertId();
                }

                $check = $this->checkAvaliableDate(
                        date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} month")), date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} month")), $data['room_id']);

                $stmt->execute(['recursion' => $data['recursion'],
                    'recursion_id' => $this->firstInsertId,
                    'user_id' => $data['user_id'],
                    'room_id' => $data['room_id'],
                    'description' => $data['description'],
                    'date' => date('Y-m-d H:i:s', strtotime($data['date'] . " +{$date} month")),
                    'starttime' => date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} month")),
                    'endtime' => date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} month")),
                ]);
                if (!$check) {
                    $success = true;
                } else {
                    foreach ($check as $item) {
                        $events .= " from {$item['starttime']} - to {$item['endtime']} ";
                    }
                    $flag = false;
                }
            }

            if (!$flag) {
                $this->connection->pdo->rollBack();
                return $events;
            }

            $this->connection->pdo->commit();
        } catch (PDOException $e) {
            $this->connection->pdo->rollBack();
            echo $e->getMessage();
        }

        return $success;
    }

    /**
     * Filter data and create event simple or recursive
     * @param array $data
     * @param int $user_id
     * return true|string
     */
    public function createEvent(array $data, int $user_id)
    {
        if (!$data['user_id']) {
            $data['user_id'] = $user_id;
        }
        $filter = Filter::check($data, array(
                    'user_id' => ['required' => true, 'numeric' => true],
                    'room_id' => ['required' => true, 'numeric' => true],
                    // 'recursion' => ['zirrow_one' => true],
                    'description' => ['required' => true],
                    'date' => ['required' => true, 'date' => date('Y-m-d')],
                    'starttime' => ['required' => true, 'date' => date("Y-m-d G:i"), 'hour' => true],
                    'endtime' => ['required' => true, 'date' => $data['starttime'], 'hour' => true, 'minutes' => $data['starttime']],
                    'recursion_type' => [$data['recursion'] ? 'required' : '' => true],
                    'recursion_value' => [$data['recursion_type'] ? 'required' : '' => true, 'recursion_type' => $data['recursion_type']]
        ));

        if ($filter->passed()) {
            return $this->creatRecursionEvent($data['recursion_type'], $data);
        } else {
            return $filter->errors();
        }
    }

    /**
     * Check if can add event to room
     * @param string $starttime
     * @param string $endtime
     * @param int $id 
     * return array
     */
    private function checkAvaliableDate($starttime, $endtime, $id)
    {
        $starttime = date('Y-m-d H:i:s', strtotime($starttime . ' +1 minutes'));
        $endtime = date('Y-m-d H:i:s', strtotime($endtime . ' +1 minutes'));

        $sql = "SELECT * FROM app_events WHERE ('$starttime' BETWEEN `starttime` AND `endtime` "
                . "OR '$endtime' BETWEEN `starttime` AND `endtime` "
                . "OR `starttime` BETWEEN '$starttime' AND '$endtime'"
                . "OR `endtime` BETWEEN '$starttime' AND '$endtime') AND room_id = '$id'";
        $stmt = $this->connection->pdo->prepare($sql);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Check if can add event to room
     * @param string $starttime
     * @param string $endtime
     * @param int $id 
     * return array
     */
    private function checkAvaliableDateForUpdate($starttime, $endtime, $room_id, $id)
    {
        $starttime = date('Y-m-d H:i:s', strtotime($starttime . ' +1 minutes'));
        $endtime = date('Y-m-d H:i:s', strtotime($endtime . ' +1 minutes'));
        
        $sql = "SELECT * FROM app_events WHERE ('$starttime' BETWEEN `starttime` AND `endtime` "
                . "OR '$endtime' BETWEEN `starttime` AND `endtime` "
                . "OR `starttime` BETWEEN '$starttime' AND '$endtime'"
                . "OR `endtime` BETWEEN '$starttime' AND '$endtime') "
                . " AND room_id = '$room_id' AND  id <> '$id' AND recursion_id <> '$id'";
        $stmt = $this->connection->pdo->prepare($sql);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Filter data and update event simple or recursive
     * @param array $data
     * @param int $string
     * @param int $recursion 
     * return true|string
     */
    public function updateEvent(array $data, $recursion = 0)
    {

        $data['starttime'] = date('Y-m-d H:i:s', strtotime($data['date'] . ' ' . $data['newStartTime']));
        $data['endtime'] = date('Y-m-d H:i:s', strtotime($data['date'] . ' ' . $data['newEndTime']));


        $filter = Filter::check($data, array(
                    'id' => ['required' => true, 'numeric' => true],
                    'description' => ['required' => true],
                    'starttime' => ['required' => true, 'date' => date("Y-m-d G:i"), 'hour' => true],
                    'endtime' => ['required' => true, 'date' => $data['starttime'], 'hour' => true,'minutes' => $data['starttime']],
                ));

        if ($filter->passed()) {

            if ($recursion != '1') {
                return $this->updateSimpleEvent($data);
            } else {
                return $this->updateRecursionEvent($data, $data['newStartTime'], $data['newEndTime']);
            }
        } else {
            return $filter->errors();
        }
    }

    /**
     * Update event
     * @param array $data
     * return bool|string
     */
    public function updateSimpleEvent(array $data)
    {
        $check = $this->checkAvaliableDateForUpdate(
                date('Y-m-d H:i:s', strtotime($data['starttime'])), 
                date('Y-m-d H:i:s', strtotime($data['endtime'])), 
                $data['room_id'], 
                $data['id']);
        
        $events = 'events exist ';
        foreach ($check as $item) {
            $events .= "from {$item['starttime']} - to {$item['endtime']} ";
        }

        if (!$check) {
            $sql = "UPDATE app_events SET description=?, starttime=?, endtime=? WHERE id=?";
            $stmt = $this->connection->pdo->prepare($sql);
            return $stmt->execute([$data['description'], $data['starttime'], $data['endtime'], $data['id']]);
        } else {
            return $events;
        }
    }

    /**
     * check recursion
     * @param type $recursion_type
     * @param type $data
     * @return type
     */
    private function checkRecursion($recursion_type, $data)
    {

        $array = [];
        switch ($recursion_type) {
            case 'weekly':
                for ($i = 0; $i <= $data['recursion_value']; $i++) {

                    $date = 7 * $i;
                    if ($i == 1) {
                        $this->firstInsertId = $this->connection->pdo->lastInsertId();
                    }

                    $array[] = $check = $this->checkAvaliableDate(
                            date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} days")), date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} days")), $data['room_id']);
                }

                return $array;

                break;
            case 'bi-weekly':
                for ($i = 0; $i <= $data['recursion_value']; $i++) {

                    $date = 14 * $i;
                    if ($i == 1) {
                        $this->firstInsertId = $this->connection->pdo->lastInsertId();
                    }

                    $array[] = $check = $this->checkAvaliableDate(
                            date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} days")), date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} days")), $data['room_id']);
                }

                return $array;
                break;
            case 'monthly':
                for ($i = 0; $i <= $data['recursion_value']; $i++) {

                    $date = $i;
                    if ($i == 1) {
                        $this->firstInsertId = $this->connection->pdo->lastInsertId();
                    }

                    $array[] = $check = $this->checkAvaliableDate(
                            date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} month")), date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} month")), $data['room_id']);
                }

                return $array;
                break;
        }
    }

    public function getRecursionEvents($id)
    {
        $sql = "SELECT * FROM app_events WHERE id = '$id' OR recursion_id = '$id'";
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update recursion event only time
     * @param array $data
     * return bool
     */
    public function updateRecursionEvent(array $data, $newStartTime, $newEndTime)
    {
        $success = true;
        $eventsMessage = 'event exist ';
        $eventsExist = array();
        $events = $this->getRecursionEvents($data['id']);

        foreach ($events as $recEvent) {
            $starttime = date('Y-m-d H:i:s', strtotime($recEvent['date'] . ' ' . $newStartTime));
            $endtime = date('Y-m-d H:i:s', strtotime($data['date'] . ' ' . $newEndTime));


            $check = $this->checkAvaliableDateForUpdate(
                    $starttime, $endtime, $data['room_id'], $data['id']);

            if ($check) {


                $success = FALSE;
                foreach ($check as $item) {

                    $eventsMessage .= " from {$item['starttime']} - to {$item['endtime']} ";
                }
            }
        }

        $newStartTime = date('H:i', strtotime($newStartTime));
        $newEndTime = date('H:i', strtotime($newEndTime));

        if ($success) {

            $sql = "UPDATE app_events SET description = ?, "
                    . "starttime = DATE_FORMAT(starttime, '%Y-%m-%d {$newStartTime}:%s'),"
                    . "endtime = DATE_FORMAT(endtime, '%Y-%m-%d {$newEndTime}:%s')"
                    . " WHERE id = ?   "
                    . "OR  (recursion_id = ?  AND starttime >= ?) "
                    . "OR (recursion_id = ? AND starttime >= ?) ";

            $stmt = $this->connection->pdo->prepare($sql);
            return $stmt->execute([$data['description'], $data['id'], $data['recursion_id'], $data['starttime'], $data['id'], $data['starttime']]);
        } else {
            return $eventsMessage;
        }
    }

    /**
     * Check how remove event simple or recursion
     * @param int $id
     * @param int $recursion
     * return bool
     */
    public function removeEvent($id, $recursion = 0)
    {
        if ($recursion != '1') {
            return $this->removeEventSimple($id);
        } else {
            return $this->removeEventRecursion($id);
        }
    }

    /**
     * Remove event recursion
     * @param int $id
     * return bool
     */
    public function removeEventRecursion($id)
    {
        $sql = "DELETE FROM app_events WHERE id = ? OR recursion_id=? AND id >= ?  ";

        $stmt = $this->connection->pdo->prepare($sql);
        return $stmt->execute([$id, $id, $id]);
    }

    /**
     * Remove event simple
     * @param int $id
     * return bool
     */
    public function removeEventSimple($id)
    {
        $sql = "DELETE FROM app_events WHERE id = ?";

        $stmt = $this->connection->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Save event object
     * @param object instance of Events $obj
     * return bool
     */
    public function save(Events $obj)
    {
        if ($obj->getId() && $this->fetchById($obj->getId())) {
            return $this->doUpdate($obj);
        } else {
            return $this->doInsert($obj);
        }
    }

    /**
     * Update object event
     * @param object $obj
     * return bool
     */
    protected function doUpdate($obj)
    {
        $values = $obj->entityToArray();
        $update = 'UPDATE ' . $obj::TABLE_NAME;
        $where = ' WHERE id = ' . $obj->getId();
        unset($values['id']);
        return $this->flush($update, $values, $where);
    }

    /**
     * Create object event
     * @param object $obj
     * return bool
     */
    protected function doInsert($obj)
    {
        $values = $obj->entityToArray();
        unset($values['id']);
        $insert = 'INSERT INTO ' . $obj::TABLE_NAME . ' ';
        if ($this->flush($insert, $values)) {
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * Create sql string with values and params and execute 
     * @param string $sql
     * @param string $values
     * @param string $where
     * return bool
     */
    protected function flush($sql, $values, $where = '')
    {
        $sql .= ' SET ';
        foreach ($values as $column => $value) {
            $sql .= $column . ' = :' . $column . ',';
        }

        $sql = substr($sql, 0, -1) . $where;
        $success = FALSE;
        try {
            $stmt = $this->connection->pdo->prepare($sql);


            $stmt->execute($values);
            $this->lastInsertId = $this->connection->pdo->lastInsertId();
            $success = TRUE;
        } catch (PDOException $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':'
                    . $e->getMessage());
            $success = FALSE;
        } catch (Throwable $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':'
                    . $e->getMessage());
            $success = FALSE;
        }
        return $success;
    }

    /**
     * Remove object event
     * @param object $obj
     * return bool
     */
    public function remove(Events $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }

}
