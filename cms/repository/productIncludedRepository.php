<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class productIncludedRepository extends dbRepository {
  
  use \host\cms\entity\productIncludedEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("m_product_included_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($ids = self::getIds()){
      $ids = implode(",", array_filter($ids));
      if(preg_match("/^[0-9,]+$/", $ids)){
        self::setWhere("id IN ( {$ids} )");
      }
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
      $stmt_allNumber = $this->prepare("SELECT FOUND_ROWS() as `allNumber`");
      $stmt_allNumber->execute();
      while($d = $stmt_allNumber->fetch(\PDO::FETCH_OBJ)){
        $this->totalNumber = $d->allNumber;
      }
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $unit_stmt = self::prepare('SELECT * FROM m_product_included_item_tbl 
                                    WHERE product_included_id = :id AND delete_kbn IS NULL');
        $unit_stmt->execute([':id' => $d->id]);
        while($u = $unit_stmt->fetch(\PDO::FETCH_OBJ)){
          $d->select_field[$u->field_class] = (object) array(
            'class' => $u->field_class,
            'name' => $u->name,
            'unit_price' => $u->unit_price,
            'unit_tax_price' => $u->unit_tax_price,
            'unit_notax_price' => $u->unit_notax_price,
            'unit_tax' => $u->unit_tax
          );
        }
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
    $push = $this->getPost();
    $ut = new utilityRepository;
    if(!$push->data->site_id){
      $this->set_message('サイトを選択してください。');
    }
    if(!$ut->mbStrLenCheck($push->data->name, 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
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
      $query = $this->queryCreate($push->data, 'm_product_included_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push->data->id ? $push->data->id : $this->lastInsertId();
      if(!$push->data->id && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("
          UPDATE m_product_included_tbl SET rank = rank + 1 
          WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL
        ");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $push['site_id']
        ])){
          $this->rollBack();
        }
        //カテゴリ(更新で一旦削除して再度更新)
        $stmt = $this->prepare("
          UPDATE product_category_at_tbl SET delete_kbn = 1 
          WHERE site_id = :site_id AND product_id = :product_id
        ");
        if(!$stmt->execute([
          ':product_id'=> $this->_lastId, 
          ':site_id'=> $data['site_id']
        ])){
          $this->rollBack();
        }
      }
      foreach($push->field->select_field_name as $class => $name){
        $stmt = $this->prepare("
          INSERT INTO m_product_included_item_tbl ( 
            product_included_id, field_class, name, unit_price, unit_tax_price, unit_notax_price, unit_tax
          ) VALUES (
            :product_included_id, :field_class, :name, :unit_price, :unit_tax_price, :unit_notax_price, :unit_tax
          ) ON DUPLICATE KEY UPDATE 
            name = :name, unit_price = :unit_price, unit_tax_price = :unit_tax_price, unit_notax_price = :unit_notax_price, unit_tax = :unit_tax, delete_kbn = :delete_kbn
        ");
        if(!$stmt->execute([
          ':product_included_id' => $this->_lastId,
          ':field_class' => $class,
          ':name'        => $name,
          ':unit_price'       => @$push->field->select_field_unit_price[$class],
          ':unit_tax_price'   => @$push->field->select_field_unit_tax_price[$class],
          ':unit_notax_price' => @$push->field->select_field_unit_notax_price[$class],
          ':unit_tax'         => @$push->field->select_field_unit_tax[$class],
          ':delete_kbn'  => $delete_kbn
        ])){
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
      $query = $this->queryCreate($push, 'm_product_included_tbl');
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