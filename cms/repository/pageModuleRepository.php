<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class pageModuleRepository extends dbRepository {
  
  use \host\cms\entity\pageModuleEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("m.*, c.name as category_name");
    self::setFrom("m_page_module_tbl m LEFT JOIN m_page_module_category_tbl c ON m.module_category_id = c.id");
    self::setWhere("m.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("m.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("m.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }else{
      self::setWhere("m.site_id IS NULL");
    }
    if($module_theme = self::getModuleTheme()){
      self::setWhere("m.module_theme = :module_theme");
      self::setValue(":module_theme", $module_theme);
    }
    if($module_category_id = self::getModuleCategoryId()){
      self::setWhere("m.module_category_id = :module_category_id");
      self::setValue(":module_category_id", $module_category_id);
    }
    self::setOrder("m.rank ASC");
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
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->template_directory = null;
        if($d->module_type == "template"){
          $d->template_directory = DIR_CMS.'module/'.$d->module_theme.'/files/templates/'.$d->template;
        }
        $d->links = explode("\n", str_replace(array("\r\n", "\r", "\n"), "\n", $d->links));
        $d->explanation = html_entity_decode($d->explanation);
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
    if(!$ut->mbStrLenCheck($push['name'], 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
    }
    if(!$push['module_theme']){
      $this->set_message('テーマを選択してください。');
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
      $query = $this->queryCreate($push, 'm_page_module_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $where = " AND module_theme = :module_theme";
        $bind[':module_theme'] = $push['module_theme'];
        if($push['site_id']){
          $where.= " AND site_id = :site_id";
          $bind[':site_id'] = $push['site_id'];
        }
        $stmt = $this->prepare("UPDATE m_page_module_tbl SET rank = rank + 1 WHERE delete_kbn IS NULL {$where}");
        if(!$stmt->execute($bind)){
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
      $query = $this->queryCreate($push, 'm_page_module_tbl');
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