<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class settlementRepository extends dbRepository {
  
  use \host\cms\entity\settlementEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("m_settlement_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("release_kbn = :release_kbn AND (release_start_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') >= DATE_FORMAT(release_start_date, '%Y-%m-%d')) AND (release_end_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') <= DATE_FORMAT(release_end_date, '%Y-%m-%d'))");
      self::setValue(":release_kbn", $release_kbn);
    }
    self::setOrder("rank ASC");
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
        $unit_stmt = self::prepare('SELECT * FROM m_settlement_product_unit_tbl 
                                    WHERE settlement_id = :id AND delete_kbn IS NULL');
        $unit_stmt->execute([':id' => $d->id]);
        while($u = $unit_stmt->fetch(\PDO::FETCH_OBJ)){
          $d->target_class = $u->target_class;
          $d->target_price[$u->target_class] = $u->target_price;
          $d->price[$u->target_class] = $u->price;
          $d->tax_price[$u->target_class] = $u->tax_price;
          $d->notax_price[$u->target_class] = $u->notax_price;
          $d->tax[$u->target_class] = $u->tax;
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
      $query = $this->queryCreate($push, 'm_settlement_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE m_settlement_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $push['site_id']
        ])){
          $this->rollBack();
        }
      }

      //商品合計金額を対象に設定
      $ary = filter_input_array(INPUT_POST, [
        'target_price' => [
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
        ]
      ]);
      for ($class = 0; $class <= 6; $class++) { //区分ごとループ
        $query = 'INSERT INTO m_settlement_product_unit_tbl ( 
          settlement_id, target_class, target_price, price, tax_price, notax_price, tax
        ) VALUES (
          :settlement_id, :target_class, :target_price, :price, :tax_price, :notax_price, :tax
        ) ON DUPLICATE KEY UPDATE 
          target_price = :target_price, price = :price, tax_price = :tax_price, notax_price = :notax_price, tax = :tax, delete_kbn = :delete_kbn';
        $params = [
          ':settlement_id' => $this->_lastId,
          ':target_class' => $class,
          ':target_price' => @$ary['target_price'][$class],
          ':price'        => @$ary['price'][$class],
          ':tax_price'    => @$ary['tax_price'][$class],
          ':notax_price'  => @$ary['notax_price'][$class],
          ':tax'          => @$ary['tax_price'][$class],
          ':delete_kbn'   => null
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
      $query = $this->queryCreate($push, 'm_settlement_tbl');
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