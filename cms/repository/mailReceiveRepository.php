<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\mailReceiveFieldRepository;

class mailReceiveRepository extends dbRepository {
  
  use \host\cms\entity\mailReceiveEntity;

  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("re.*, fo.name, fo.subject");
    self::setFrom("
      mail_form_receive_tbl re 
      LEFT JOIN mail_form_tbl fo ON re.form_id = fo.id AND fo.delete_kbn IS NULL
    ");
    self::setWhere("re.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("re.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("re.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($site_ids = self::getSiteIds()){
      $value = null;
      foreach($site_ids as $i => $val){
        $value.= ($value ? ' OR ' : '') . "re.site_id = :site_ids{$i}";
        self::setValue(":site_ids{$i}", $val);
      }
      self::setWhere("({$value})");
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("fo.release_kbn = :release_kbn");
      self::setValue(":release_kbn", $release_kbn);
    }
    if($form_id = self::getFormId()){
      self::setWhere("re.form_id = :form_id");
      self::setValue(":form_id", $form_id);
    }
    self::setOrder("re.created_date DESC");
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
      $stmt_allNumber = self::prepare("SELECT FOUND_ROWS() as `allNumber`");
      $stmt_allNumber->execute();
      while($d = $stmt_allNumber->fetch(\PDO::FETCH_OBJ)){
        $this->totalNumber = $d->allNumber;
      }
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $f = new mailReceiveFieldRepository;
        $f->setSiteId($d->site_id);
        $f->setReceiveId($d->id);
        $field_get = $f->get();
        $d->fields = $field_get->row;
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

}
// ?>