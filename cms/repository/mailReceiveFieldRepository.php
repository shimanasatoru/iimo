<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class mailReceiveFieldRepository extends dbRepository {
  
  use \host\cms\entity\mailReceiveFieldEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("mail_form_receive_field_tbl");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($receive_id = self::getReceiveId()){
      self::setWhere("receive_id = :receive_id");
      self::setValue(":receive_id", $receive_id);
    }
    if($form_id = self::getFormId()){
      self::setWhere("form_id = :form_id");
      self::setValue(":form_id", $form_id);
    }
    if($field_id = self::getFieldId()){
      self::setWhere("field_id = :field_id");
      self::setValue(":field_id", $field_id);
    }
    self::setOrder("id ASC");
    $q = self::getSelect().self::getFrom().self::getWhere().self::getGroupBy().self::getOrder().self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->value = str_replace(["&#13;&#10;"], "\n", json_decode($d->value));
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
}
// ?>