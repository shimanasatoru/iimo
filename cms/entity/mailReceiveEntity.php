<?php 
namespace host\cms\entity;

trait mailReceiveEntity{
  
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
  
  public $release_kbn;
  public function getReleaseKbn(){
    return $this->release_kbn;
  }
  public function setReleaseKbn(int $release_kbn) :void{
    $this->release_kbn = $release_kbn;
  }
  
  public $form_id;
  public function getFormId(){
    return $this->form_id;
  }
  public function setFormId(int $form_id) :void{
    $this->form_id = $form_id;
  }
  
}

// ?>