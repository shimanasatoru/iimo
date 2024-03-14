<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\siteRepository;

class mailSmtpRepository extends dbRepository {
  
  use \host\cms\entity\mailSmtpEntity;

  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("mail_smtp_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    return self::getAll();
  }

  public function push(){
    $push = $this->getPost();
    $ut = new utilityRepository;
    if(!$push['site_id']){
      $this->set_message('サイトを選択してください。');
    }
    if(!$ut->mbStrLenCheck($push['smtp_server_name'], 1, 250)){
      $this->set_invalid('smtp_server_name', '必須または250文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($push['smtp_server_port'], 1, 5)){
      $this->set_invalid('smtp_server_port', '必須または5文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($push['smtp_server_secure'], 1, 50)){
      $this->set_invalid('smtp_server_secure', '必須または50文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($push['smtp_user_name'], 1, 250)){
      $this->set_invalid('smtp_user_name', '必須または250文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($push['smtp_user_password'], 1, 250)){
      $this->set_invalid('smtp_user_password', '必須または250文字以内となります。');
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
      $query = $this->queryCreate($push, 'mail_smtp_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    $this->set_status(true);
    return $this;
  }
}
// ?>