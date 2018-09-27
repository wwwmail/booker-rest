<?php

namespace Application\Database;

use Application\Helper\Filter;
use Application\Entity\Events;
use PDO;

class EventsService {

    protected $connection;
    private $firstInsertId;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_events')
                ->where('id = :id')::getSql());
        $stmt->execute(['id' => (int) $id]);
        return Events::arrayToEntity(
                        $stmt->fetch(PDO::FETCH_ASSOC), new Events());
    }

    public function fetchAll()
    {
        $stmt = $this->connection->pdo
                ->prepare(Finder::select('app_events')::getSql());
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Events::arrayToEntity($row, new Events());
        }
    }


    public function fetchByRoom($id)
    {
       $sql = "SELECT * FROM app_events WHERE room_id = :id ORDER BY starttime"; 
         $stmt = $this->connection->pdo
                ->prepare(Finder::getSql($sql));
         $stmt->execute(['id' => (int) $id]);
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function creatSimpleEvent(array $data)
    {
        $check = $this->checkAvaliableDate($data['starttime'], $data['endtime'], $data['room_id']);
        if (!$check) {
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
        } else {
            $events = '';
            foreach ($check as $item) {
                $events .= "from {$item['starttime']} - to {$item['endtime']}";
            }
            return "event exixt  {$events}";
        }
    }

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
                        date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} days")), 
                        date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} days")), $data['room_id']);

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
                        $events .= "from {$item['starttime']} - to {$item['endtime']} ";
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
                        date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} days")), 
                        date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} days")),$data['room_id']);

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
                        $events .= "from {$item['starttime']} - to {$item['endtime']} ";
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
                        date('Y-m-d H:i:s', strtotime($data['starttime'] . " +{$date} month")), 
                        date('Y-m-d H:i:s', strtotime($data['endtime'] . " +{$date} month")), $data['room_id']);

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
                        $events .= "from {$item['starttime']} - to {$item['endtime']} ";
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
                    'endtime' => ['required' => true, 'date' => $data['starttime'], 'hour' => true, 'minutes'=>$data['starttime']],
                    'recursion_type' => [$data['recursion'] ? 'required' : '' => true],
                    'recursion_value' => [$data['recursion_type'] ? 'required' : '' => true, 'recursion_type' => $data['recursion_type']]
        ));




//        $a = $this->checkAvaliableDate($data['starttime'], $data['endtime']);
//        var_dump($a); die;
//        var_dump($data['recursion_type']); die;


        if ($filter->passed()) {

            return $this->creatRecursionEvent($data['recursion_type'], $data);
        } else {
            return $filter->errors();
        }
    }

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
    
    
    public function updateEvent(array $data, $recursion=0)
    {
        $data['starttime'] = date('Y-m-d H:i:s' , strtotime($data['date'].' '.$data['newStartTime']));
        $data['endtime'] = date('Y-m-d H:i:s' , strtotime($data['date'].' '.$data['newEndTime']));
        
       
        $filter = Filter::check($data, array(
                    'id' => ['required' => true, 'numeric' => true],
                    'description' => ['required' => true],
                    'starttime' => ['required' => true, 'date' => date("Y-m-d G:i"), 'hour' => true],
                    'endtime' => ['required' => true, 'date' => $data['starttime'], 'hour' => true],
                    
                   ));
        
                 //  var_dump($recursion); die;
         
         if ($filter->passed()) {
             
             if($recursion != '1'){
                 
                return $this->updateSimpleEvent($data);
             }else{
               
                return $this->updateRecursionEvent($data);
             }
        
        } else {
            return $filter->errors();
        }
        
    }
    
    public function updateSimpleEvent(array $data)
    {
        $check = $this->checkAvaliableDate(
                date('Y-m-d H:i:s', strtotime($data['starttime'])), 
                date('Y-m-d H:i:s', strtotime($data['endtime'])), $data['room_id']);
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

    public function updateRecursionEvent(array $data)
    {
      //  var_dump($data); die;
//      var_dump($data['starttime']); die;
        $sql ="UPDATE app_events SET description = ?, "
                . "starttime = DATE_FORMAT(starttime, '%Y-%m-%d {$data['newStartTime']}:%s'),"
                . "endtime = DATE_FORMAT(endtime, '%Y-%m-%d {$data['newEndTime']}:%s')"
                . " WHERE id = ?   "
                        . "OR  (recursion_id = ?  AND starttime >= ?) "
                        . "OR (recursion_id = ? AND starttime >= ?) ";

        $stmt = $this->connection->pdo->prepare($sql);
        return $stmt->execute([$data['description'], $data['id'], $data['recursion_id'], $data['starttime'], $data['id'], $data['starttime']]); 
    }
    
    public function removeEvent($id, $recursion=0)
    {
      
             if($recursion != '1'){
                return $this->removeEventSimple($id);
             }else{
                return $this->removeEventRecursion($id);
             }
        
        
    }
    
    public function removeEventRecursion ($id)
    {
        $sql ="DELETE FROM app_events WHERE id = ? OR recursion_id=? AND id >= ?  ";

        $stmt = $this->connection->pdo->prepare($sql);
        return $stmt->execute([$id, $id, $id]); 
    }
    
    public function removeEventSimple($id)
    {
        $sql ="DELETE FROM app_events WHERE id = ?";

        $stmt = $this->connection->pdo->prepare($sql);
        return $stmt->execute([$id]); 
        
    }

    public function save(Events $obj)
    {
        if ($obj->getId() && $this->fetchById($obj->getId())) {
            return $this->doUpdate($obj);
        } else {
            return $this->doInsert($obj);
        }
    }

    protected function doUpdate($obj)
    {
        $values = $obj->entityToArray();
        $update = 'UPDATE ' . $obj::TABLE_NAME;
        $where = ' WHERE id = ' . $obj->getId();
        unset($values['id']);
        return $this->flush($update, $values, $where);
    }

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

    public function remove(Events $obj)
    {
        $sql = 'DELETE FROM ' . $obj::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->connection->pdo->prepare($sql);
        $stmt->execute(['id' => $obj->getId()]);
        return ($this->fetchById($obj->getId())) ? FALSE : TRUE;
    }

}
