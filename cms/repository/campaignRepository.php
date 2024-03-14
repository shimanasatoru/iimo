<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class campaignRepository extends dbRepository {
  
  use \host\cms\entity\campaignEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*, CASE WHEN usage_kbn = 1 AND (usage_start_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') >= DATE_FORMAT(usage_start_date, '%Y-%m-%d')) AND (usage_end_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') <= DATE_FORMAT(usage_end_date, '%Y-%m-%d')) THEN 1 ELSE NULL END as available ");
    self::setFrom("m_campaign_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($ids = self::getIds()){
      $ids = implode(",", $ids);
      if(preg_match("/^[0-9,]+$/", $ids)){
        self::setWhere("id IN ( {$ids} )");
      }
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($usage_kbn = self::getUsageKbn()){
      self::setWhere("usage_kbn = :usage_kbn AND (usage_start_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') >= DATE_FORMAT(usage_start_date, '%Y-%m-%d')) AND (usage_end_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') <= DATE_FORMAT(usage_end_date, '%Y-%m-%d'))");
      self::setValue(":usage_kbn", $usage_kbn);
    }
    self::setOrder("rank ASC");
    $q = self::getSelect().self::getFrom().self::getWhere().self::getGroupBy().self::getOrder().self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
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
    if($push['method'] == "couponCode" && !$ut->mbStrLenCheck($push['coupon_code'], 1, 20)){
      $this->set_invalid('coupon_code', '必須または20文字以内となります。');
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
      $query = $this->queryCreate($push, 'm_campaign_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE m_campaign_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $push['site_id']
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
      $query = $this->queryCreate($push, 'm_campaign_tbl');
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