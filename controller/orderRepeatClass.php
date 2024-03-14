<?php 
namespace controller;

use host\cms\repository\orderRepeatRepository;
use Smarty;

class orderRepeatClass{
  
  private $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  
  public function getMember(){
    return @$_SESSION['member'];
  }

  public function getFunction(){
    $params = filter_input_array(INPUT_POST, [
      'function' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ]
    ]);
    if(@$params['function']){
      return $params['function'];
    }
    return null;
  }
  
  public function getDataType(){
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ]
    ]);
    if(@$params['dataType']){
      return $params['dataType'];
    }
    return null;
  }

  public function load(){
    $site_id = $this->getSiteId();
    $member = $this->getMember();
    if(!$site_id){
      return $this;
    }
    if(!$member->id){
      return $this;
    }
    
    $o = new orderRepeatRepository;
    $o->setSiteId($site_id);
    $o->setMemberId($member->id);
    $function = $this->getFunction();
    switch($function){
      case 'cancel'://キャンセル
        $o->setCancel($_POST);
        break;
    }
    if($this->getDataType() == 'json'){
      echo json_encode( $o->get(), JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $o->get();
  }
}

// ?>