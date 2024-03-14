<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class logRepository extends dbRepository {
  
  use \host\cms\entity\logEntity;

  /*
   * @return object array mixed $this
   */
  public function getPageActiveBk() {
    self::setSelect("ps.*, s.name as site_name, n.name as navigation_name, a.name as account_name");
    self::setFrom("
    ( SELECT id, site_id, navigation_id, account_id, name, update_date
      FROM page_bk 
      UNION ALL
      SELECT id, site_id, navigation_id, account_id, name, update_date
      FROM page_structure_bk 
    ) as ps  
    LEFT JOIN site_tbl s ON ps.site_id = s.id 
    LEFT JOIN navigation_tbl n ON ps.navigation_id = n.id
    LEFT JOIN account_tbl a ON ps.account_id = a.id
    ");
    if($id = self::getId()){
      self::setWhere("ps.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("ps.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($site_ids = self::getSiteIds()){
      $value = null;
      foreach($site_ids as $i => $val){
        $value.= ($value ? ' OR ' : '') . "ps.site_id = :site_ids{$i}";
        self::setValue(":site_ids{$i}", $val);
      }
      self::setWhere("({$value})");
    }
    if($navigation_id = self::getNavigationId()){
      self::setWhere("ps.navigation_id = :navigation_id");
      self::setValue(":navigation_id", $navigation_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("ps.release_kbn = :release_kbn AND (ps.release_start_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') >= DATE_FORMAT(ps.release_start_date, '%Y-%m-%d')) AND (ps.release_end_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') <= DATE_FORMAT(ps.release_end_date, '%Y-%m-%d'))");
      self::setValue(":release_kbn", $release_kbn);
    }
    self::setOrder("ps.update_date DESC");
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
  
  /*
   * @return object array mixed $this
   */
  public function getDailyActiveBk() {
    $start_period = self::getStartPeriod();
    $end_period = self::getEndPeriod();
    if(!$start_period || !$end_period){
      return $this;
    }
    if($start_period > $end_period){
      list($end_period, $start_period) = array($start_period, $end_period);
    }

    self::setWhere("ps.update_day >= :start_period");
    self::setValue(":start_period", $start_period);
    self::setWhere("ps.update_day <= :end_period");
    self::setValue(":end_period", $end_period);
    if($site_id = self::getSiteId()){
      self::setWhere("ps.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($site_ids = self::getSiteIds()){
      $value = null;
      foreach($site_ids as $i => $val){
        $value.= ($value ? ' OR ' : '') . "ps.site_id = :site_ids{$i}";
        self::setValue(":site_ids{$i}", $val);
      }
      self::setWhere("({$value})");
    }
    
    self::setSelect("count(ps.id) as counter, ps.update_day");
    self::setFrom("
    ( SELECT id, site_id, DATE_FORMAT(update_date, '%Y-%m-%d') as update_day
      FROM page_bk 
      UNION ALL
      SELECT id, site_id, DATE_FORMAT(update_date, '%Y-%m-%d') as update_day
      FROM page_structure_bk 
    ) as ps 
    ");
    self::setGroupBy("ps.update_day");
    self::setOrder("ps.update_day DESC");
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
      $data = array();
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $data[] = $d;
      }
      
      //日毎の配列を生成
      $datePeriod = (new \DatePeriod(
        new \DateTime($start_period),
        new \DateInterval('P1D'),
        new \DateTime(date('Y-m-d', strtotime($end_period." +1 day"))),
      ));
      
      foreach ($datePeriod as $dt) {
        $day = $dt->format('Y-m-d');
        $key = array_search( $day, array_column( $data, 'update_day'));
        $counter = 0;
        if($key !== false && $data[$key]->counter){
          $counter = $data[$key]->counter;
        }
        $this->row[] = (object) array(
          'day' => $day,
          'counter' => $counter
        );
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
  
  /*
   * @return object array mixed $this
   */
  public function getContentActiveBk() {

    if($site_id = self::getSiteId()){
      self::setWhere("co.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($site_ids = self::getSiteIds()){
      $value = null;
      foreach($site_ids as $i => $val){
        $value.= ($value ? ' OR ' : '') . "co.site_id = :site_ids{$i}";
        self::setValue(":site_ids{$i}", $val);
      }
      self::setWhere("({$value})");
    }
    
    self::setSelect("pa.name as page_title, fi.name as field_name, co.update_date");
    self::setFrom("
      page_content_bk co 
      LEFT JOIN page_tbl pa ON co.page_id = pa.id
      LEFT JOIN page_content_field_tbl fi ON co.field_id = fi.id
      ");
    self::setOrder("co.update_date DESC");
    $q = self::getSelect()
        .self::getFrom()
        .self::getWhere()
        .self::getGroupBy()
        .self::getOrder()
        .self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      if(self::getValue()){
        foreach(self::getValue() as $key => $value){
          $stmt->bindValue($key, $value);
        }
      }
      $stmt->execute();
      
      
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
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