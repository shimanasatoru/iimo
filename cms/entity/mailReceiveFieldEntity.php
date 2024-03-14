<?php 
namespace host\cms\entity;

trait mailReceiveFieldEntity{
  
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

  public $receive_id;
  public function getReceiveId(){
    return $this->receive_id;
  }
  public function setReceiveId(int $receive_id) :void{
    $this->receive_id = $receive_id;
  }
  
  public $form_id;
  public function getFormId(){
    return $this->form_id;
  }
  public function setFormId(int $form_id) :void{
    $this->form_id = $form_id;
  }
  
  public $field_id;
  public function getFieldId(){
    return $this->field_id;
  }
  public function setFieldId(int $field_id) :void{
    $this->field_id = $field_id;
  }
  
}

// ?>