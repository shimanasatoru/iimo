<?php 
namespace host\cms\entity;

trait logEntity{
  
  public $row;
  public function __construct () {
    $this->row = array();
  }

  public $id;
  public function getId(){
    return $this->id;
  }
  public function setId(int $id) :void{
    $this->id = $id;
  }
  
  public $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  
  public $site_ids;
  public function getSiteIds(){
    return $this->site_ids;
  }
  public function setSiteIds(array $site_ids) :void{
    $this->site_ids = $site_ids;
  }
  
  public $navigation_id;
  public function getNavigationId(){
    return $this->navigation_id;
  }
  public function setNavigationId(int $navigation_id) :void{
    $this->navigation_id = $navigation_id;
  }

  public $release_kbn;
  public function getReleaseKbn(){
    return $this->release_kbn;
  }
  public function setReleaseKbn(int $release_kbn) :void{
    $this->release_kbn = $release_kbn;
  }
  
  public $start_period;
  public function getStartPeriod(){
    return $this->start_period;
  }
  public function setStartPeriod(string $start_period) :void{
    $this->start_period = $start_period;
  }
  
  public $end_period;
  public function getEndPeriod(){
    return $this->end_period;
  }
  public function setEndPeriod(string $end_period) :void{
    $this->end_period = $end_period;
  }
  
  public $module_id;
  public function getModuleId(){
    return $this->module_id;
  }
  public function setModuleId(int $module_id) :void{
    $this->module_id = $module_id;
  }

  public $keyword;
  public function getKeyword(){
    return $this->keyword;
  }
  public function setKeyword(string $keyword) :void{
    $this->keyword = $keyword;
  }
}

// ?>