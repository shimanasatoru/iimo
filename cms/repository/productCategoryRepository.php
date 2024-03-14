<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class productCategoryRepository extends dbRepository {
  
  use \host\cms\entity\productCategoryEntity;
  /*
   * ツリー出力 再帰
   * @param array $list
   * @return array
   */
  public function tree(&$list, $parent, $level = 0, $dirPath = array()){
    $tree = array();
    foreach ($parent as $key=>$value){
      if($level > 0){
        $dirPath[$level] = $value->directory_name;
      }
      if(isset($list[$value->id])){
        $value->children = $this->tree($list, $list[$value->id], $level + 1, $dirPath);
      }
      if($level > 0){
        $value->url .= implode('/', $dirPath);
      }
      $tree[] = $value;
    }
    return $tree;
  }
  /*
   * 全件取得
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("product_category_tbl");
    self::setWhere("delete_kbn IS NULL");
    self::setOrder("rank ASC");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("release_kbn = :release_kbn");
      self::setValue(":release_kbn", $release_kbn);
    }
    $q = self::getSelect().
          self::getFrom().
          self::getWhere().
          self::getGroupBy().
          self::getOrder().
          self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      $array = array();
      $new = array();
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $array[] = $d;
        $new[$d->parent_id][] = $d;
      }
      $this->row = $this->tree($new, array($array[0]));
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
      $query = $this->queryCreate($push, 'product_category_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        //※ナビゲーションはツリー型のため「rank[0] == 'トップページ'」とする！
        $stmt = $this->prepare("UPDATE product_category_tbl SET rank = rank + 1 WHERE parent_id != 0 AND site_id = :site_id AND delete_kbn IS NULL");
        if(!$stmt->execute([':site_id'=> $push['site_id']])){
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
      $query = $this->queryCreate($push, 'product_category_tbl');
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