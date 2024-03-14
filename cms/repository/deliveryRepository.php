<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class deliveryRepository extends dbRepository {
  
  use \host\cms\entity\deliveryEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("
      d.*, 
      t.code as temperature_zone_code,
      t.name as temperature_zone_name,
      t.badge as temperature_zone_badge
    ");
    self::setFrom("m_delivery_tbl d LEFT JOIN m_temperature_zone_tbl t ON d.temperature_zone = t.id");
    self::setWhere("d.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("d.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("d.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    self::setOrder("d.rank ASC");
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
        $d->billing_conditions_obj = $this->getBillingConditions($d->billing_conditions);
        $unit_stmt = self::prepare('SELECT d.*, p.name as prefectures_name FROM m_delivery_prefecture_unit_tbl d 
                                    LEFT JOIN m_prefectures_tbl p ON d.prefectures_id = p.id AND p.delete_kbn IS NULL
                                    WHERE d.delivery_id = :id AND d.delete_kbn IS NULL');
        $unit_stmt->execute([':id' => $d->id]);
        while($u = $unit_stmt->fetch(\PDO::FETCH_OBJ)){
          $d->day[$u->prefectures_id] = $u->day;
          $d->size[$u->size_class] = $u->size;
          $d->price[$u->prefectures_id][$u->size_class] = $u->price;
          $d->tax_price[$u->prefectures_id][$u->size_class] = $u->tax_price;
          $d->notax_price[$u->prefectures_id][$u->size_class] = $u->notax_price;
          $d->tax[$u->prefectures_id][$u->size_class] = $u->tax;
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
    if(!$ut->mbStrLenCheck($push['name'], 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
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
      $query = $this->queryCreate($push, 'm_delivery_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE m_delivery_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $push['site_id']
        ])){
          $this->rollBack();
        }
      }
      //都道府県・サイズ別料金
      $ary = filter_input_array(INPUT_POST, [
        'day' => [
          'filter' => FILTER_VALIDATE_INT,
          'flags' => FILTER_FORCE_ARRAY, 
          'options'=> array('default'=>null)
        ],
        'size' => [
          'filter' => FILTER_VALIDATE_INT,
          'flags' => FILTER_FORCE_ARRAY, 
          'options'=> array('default'=>null)
        ],
        'price' => [
          'filter' => FILTER_VALIDATE_INT,
          'flags' => FILTER_FORCE_ARRAY, 
          'options'=> array('default'=>null)
        ],
        'tax_price' => [
          'filter' => FILTER_VALIDATE_INT,
          'flags' => FILTER_FORCE_ARRAY, 
          'options'=> array('default'=>null)
        ],
        'notax_price' => [
          'filter' => FILTER_VALIDATE_INT,
          'flags' => FILTER_FORCE_ARRAY, 
          'options'=> array('default'=>null)
        ],
        'tax' => [
          'filter' => FILTER_VALIDATE_INT,
          'flags' => FILTER_FORCE_ARRAY, 
          'options'=> array('default'=>null)
        ],
      ]);
      for ($size_class = 0; $size_class <= 4; $size_class++) { //サイズ区分ごとループ
        for ($pref = 1; $pref <= 47; $pref++) { //都道府県ごとループ
          $query = 'INSERT INTO m_delivery_prefecture_unit_tbl ( 
            delivery_id, prefectures_id, day, size_class, size, 
            price, tax_price, notax_price, tax
          ) VALUES (
            :delivery_id, :prefectures_id, :day, :size_class, :size, 
            :price, :tax_price, :notax_price, :tax
          ) ON DUPLICATE KEY UPDATE 
            day = :day, size = :size, price = :price, tax_price = :tax_price, notax_price = :notax_price, tax = :tax, 
            delete_kbn = :delete_kbn';
          $params = [
            ':delivery_id'  => $this->_lastId,
            ':prefectures_id' => $pref,
            ':size_class'   => $size_class,
            ':size'         => @$ary['size'][$size_class],
            ':day'          => @$ary['day'][$pref],
            ':price'        => @$ary['price'][$pref][$size_class],
            ':tax_price'    => @$ary['tax_price'][$pref][$size_class],
            ':notax_price'  => @$ary['notax_price'][$pref][$size_class],
            ':tax'          => @$ary['tax'][$pref][$size_class],
            ':delete_kbn'   => null
          ];
          $stmt = $this->prepare($query);
          if(!$stmt->execute($params)){
            $this->rollBack();
          }
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
    if(!$push['id']){
      $this->set_message('IDが取得できません。');
    }
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
      $query = $this->queryCreate($push, 'm_delivery_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
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