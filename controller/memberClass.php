<?php 
namespace controller;

use host\cms\repository\memberRepository;
use Smarty;

class memberClass{
  
  private $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
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
    if(!$site_id){
      return $this;
    }
    $m = new memberRepository;
    $m->setSiteId($site_id);
    $function = $this->getFunction();
    switch($function){
      case 'loginMember'://ログイン
        $m->setLogin($_POST);
        break;
      case 'logoutMember'://ログアウト
        $m->setLogout();
        break;
      case 'additionMember'://（仮）新規登録・変更
        $m->setSendMail(1);
        $m->setMember($_POST);
        break;
      case 'registrationMember'://（本）新規登録
        $m->setTemporary($_POST);
        break;
      case 'reTemporaryPasswordMember'://再パスワード（仮）申請
        $m->setReTemporaryPassword($_POST);
        break;
      case 'rePasswordMember'://再パスワード（本）申請
        $m->setRepassword($_POST);
        break;
      case 'deleteMember'://退会
        $m->setDelete($_POST);
        break;
    }
    if($this->getDataType() == 'json'){
      echo json_encode( $m->get(), JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $m->get();
  }
}

// ?>