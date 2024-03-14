<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class deliveryDateTimeRepository extends dbRepository {
  
  use \host\cms\entity\deliveryDateTimeEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("m_delivery_datetime_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    $q = self::getSelect()
        .self::getFrom()
        .self::getWhere()
        .self::getGroupBy()
        .self::getOrder()
        .self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        
        $d->delivery_start_period_date = date("Y-m-d");
        $d->delivery_end_period_date = null;
        $d->delivery_initial_period_date = null;
        if($d->use_delivery_date){
          $delivery_start_period = (int) $d->delivery_start_period;
          $delivery_end_period = (int) $d->delivery_end_period;
          $delivery_initial_period = (int) $d->delivery_initial_period;
          if($delivery_start_period){
            $d->delivery_start_period_date = date('Y-m-d', strtotime("+{$delivery_start_period} day"));
          }
          if($delivery_end_period){
            $period = $delivery_start_period + $delivery_end_period;
            $d->delivery_end_period_date = date('Y-m-d', strtotime("+{$period} day"));
          }
          if($delivery_initial_period >= 0){
            $period = $delivery_start_period + $delivery_initial_period;
            $d->delivery_initial_period_date = date('Y-m-d', strtotime("+{$period} day"));
          }
        }
        
        $d->time_zone_value = array();
        $unit_stmt = self::prepare('SELECT * FROM m_delivery_time_tbl 
                                    WHERE delivery_datetime_id = :id AND delete_kbn IS NULL');
        $unit_stmt->execute([':id' => $d->id]);
        while($u = $unit_stmt->fetch(\PDO::FETCH_OBJ)){
          $d->time_zone_value[$u->time_kbn] = (object) array(
            'time_kbn' => $u->time_kbn,
            'time_zone' => $u->time_zone,
            'code' => $u->code
          );
        }
        $this->row[] = $d;
      }
      $this->rowNumber = $stmt->rowCount();
      if($this->rowNumber > 0){
        $this->set_status(true);
      }
      $stmt_allNumber = $this->prepare("SELECT FOUND_ROWS() as `allNumber`");
      $stmt_allNumber->execute();
      while($d = $stmt_allNumber->fetch(\PDO::FETCH_OBJ)){
        $this->totalNumber = $d->allNumber;
      }
      $this->pageRange = $this->getPageRange();
    } catch (\Exception $e) {
      $this->set_message($e->getMessage());
    }
    return $this;
  }

  public function push(){
    $push = $this->getPost();
    $ut = new utilityRepository;
    if(!$push['site_id']){
      $this->set_message('サイトを選択してください。');
    }
    if($push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    unset($push['token']);

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'm_delivery_datetime_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      
      //区分
      $ary = filter_input_array(INPUT_POST, [
        'time_zone' => [
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'flags' => FILTER_FORCE_ARRAY, 
          'options'=> array('default'=>null)
        ],
        'code' => [
          'filter' => FILTER_VALIDATE_INT,
          'flags' => FILTER_FORCE_ARRAY, 
          'options'=> array('default'=>null)
        ],
      ]);
      foreach($ary['time_zone'] as  $kbn=>$zone){
        $query = 'INSERT INTO m_delivery_time_tbl ( 
          delivery_datetime_id, time_kbn, time_zone, code
        ) VALUES (
          :delivery_datetime_id, :time_kbn, :time_zone, :code
        ) ON DUPLICATE KEY UPDATE 
          time_zone = :time_zone, code = :code, delete_kbn = :delete_kbn';
        $params = [
          ':delivery_datetime_id' => $this->_lastId,
          ':time_kbn'    => $kbn,
          ':time_zone'   => $zone,
          ':code'        => $ary['code'][$kbn],
          ':delete_kbn'  => null
        ];
        $stmt = $this->prepare($query);
        if(!$stmt->execute($params)){
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
}
// ?>