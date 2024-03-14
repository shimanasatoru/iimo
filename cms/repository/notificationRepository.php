<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class notificationRepository extends dbRepository {
  
  use \host\cms\entity\notificationEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setWhere("n.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("n.id = :id");
      self::setValue(":id", $id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("n.release_kbn = :release_kbn AND (n.release_start_date IS NULL || now() >= n.release_start_date) AND (n.release_end_date IS NULL || now() <= n.release_end_date)");
      self::setValue(":release_kbn", $release_kbn);
    }
    if($keyword = self::getKeyword()){
      $where = "";
      foreach($keyword as $i => $word){
        $where.= ($where ? " OR " : "") . "(n.name LIKE :key{$i} OR n.description LIKE :key{$i})";
        self::setValue(":key{$i}", "%{$word}%");
      }
      self::setWhere("({$where})");
    }
    if(!self::getOrder()){
      self::setOrder("n.rank ASC");
    }
    self::setSelect("n.*, a.name as account_name");
    self::setFrom("notification_tbl n LEFT JOIN account_tbl a ON n.account_id = a.id");
    $q = self::getSelect()
        .self::getFrom()
        .self::getWhere()
        .self::getGroupBy()
        .self::getOrder()
        .self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      if(is_array(self::getValue())){
        foreach(self::getValue() as $name => $value){
          if(is_numeric($value)){
            $stmt->bindValue($name, $value, \PDO::PARAM_INT);
          }else{
            $stmt->bindValue($name, $value, \PDO::PARAM_STR);
          }
        }
      }
      $stmt->execute();
      $allNumber_stmt = $this->prepare("SELECT FOUND_ROWS() as `allNumber`");
      $allNumber_stmt->execute();
      while($d = $allNumber_stmt->fetch(\PDO::FETCH_OBJ)){
        $this->totalNumber = $d->allNumber;
      }
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $this->row[] = $d;
      }
      $this->rowNumber = $stmt->rowCount();
      if($this->rowNumber > 0){
        $this->set_status(true);
      }
      $this->pageRange = $this->getPageRange();
    } catch (\Exception $e) {
      $this->set_message($e->getMessage());
    }
    return $this;
  }
  
  public function push(){
    $ut = new utilityRepository;
    $push = $this->getPost();
    
    if(!$ut->mbStrLenCheck($push->data->name, 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
    }
    if(!$push->data->description){
      $this->set_message('内容は必須となります。');
    }
    if($push->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push->data, 'notification_tbl');
      $stmt = $this->prepare($query['query']);
      if(is_array($query['params'])){
        foreach($query['params'] as $name => $value){
          if(is_numeric($value)){
            $stmt->bindValue($name, $value, \PDO::PARAM_INT);
          }else{
            $stmt->bindValue($name, $value, \PDO::PARAM_STR);
          }
        }
      }
      if(!$stmt->execute()){
        $this->rollBack();
      }
      $this->_lastId = $push->data->id ? $push->data->id : $this->lastInsertId();
      if(!$push->data->id && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE notification_tbl SET rank = rank + 1 WHERE id != :id AND delete_kbn IS NULL");
        $stmt->bindValue(':id', $this->_lastId, \PDO::PARAM_INT);
        if(!$stmt->execute()){
          $this->rollBack();
        }
      }
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }
  
  public function update(){
    $push = $this->getPost();
    $ut   = new utilityRepository;
    if(!$push->data->id){
      $this->set_message('IDが取得できません。');
    }
    if($push->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push->data, 'notification_tbl');
      $stmt = $this->prepare($query['query']);
      if(is_array($query['params'])){
        foreach($query['params'] as $name => $value){
          if(is_numeric($value)){
            $stmt->bindValue($name, $value, \PDO::PARAM_INT);
          }else{
            $stmt->bindValue($name, $value, \PDO::PARAM_STR);
          }
        }
      }
      if(!$stmt->execute()){
        $this->rollBack();
      }
      $this->_lastId = $push->data->id ? $push->data->id : $this->lastInsertId();
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }  
}
// ?>