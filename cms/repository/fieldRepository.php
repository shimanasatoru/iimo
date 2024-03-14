<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class fieldRepository extends dbRepository {
  
  use \host\cms\entity\fieldEntity;
  
  /*
   * 全件取得
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("page_content_field_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($navigation_id = self::getNavigationId()){
      self::setWhere("navigation_id = :navigation_id");
      self::setValue(":navigation_id", $navigation_id);
    }
    $q = self::getSelect().self::getFrom().self::getWhere().self::getGroupBy().self::getOrder().self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        if(in_array($d->field_type, ['table'])){
          $d->detail = json_decode($d->detail);
        }else{
          $d->detail = json_decode($d->detail, true);
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
    if($push['field_type'] == 'table'){
      //並び替え後、データ維持のためID付与。（問題：最大ID番号が削除時だけは引き継がれる）
      $cnt = max($push['column_id']);
      if($cnt == null || !is_int($cnt)){
        $cnt = 0;
      }
      foreach($push['column_id'] as $key => $id){
        if(!is_int($id)){
          $cnt++;
          $push['column_id'][$key] = $cnt;
        }
        if(!isset($push['column_name'][$key]) || !$push['column_name'][$key]){
          $this->set_message(($key+1)."行目：カラム名は必須となります。");
        }
        if(!isset($push['column_type'][$key]) || !$push['column_type'][$key]){
          $this->set_message(($key+1)."行目：カラム型は必須となります。");
        }
        $push['detail'][$key] = array(
          'column_id' => $push['column_id'][$key],
          'column_name' => $push['column_name'][$key],
          'column_type' => $push['column_type'][$key],
          'column_detail' => $push['column_detail'][$key]
        );
      }
    }
    if($push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    unset($push['token'], $push['column_id'], $push['column_name'], $push['column_type'], $push['column_detail']);
    $push['detail'] = json_encode($push['detail'], JSON_UNESCAPED_UNICODE);

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'page_content_field_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE page_content_field_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL");
        if(!$stmt->execute([':id'=> $this->_lastId, ':site_id'=> $push['site_id']])){
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
      $query = $this->queryCreate($push, 'page_content_field_tbl');
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