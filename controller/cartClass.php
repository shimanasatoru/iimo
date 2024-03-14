<?php 
namespace controller;

use host\cms\repository\cartRepository;
use Smarty;

class cartClass{
  
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

    $function = $this->getFunction();
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    $cart = new cartRepository;
    $cart->setSiteId($site_id);
    $cart->loadMaster();
    
    //unset($_SESSION['orderer']);
    //unset($_SESSION['item']);
    //print_r($_SESSION);

    switch($function){
      case 'additionOrderer':
        $cart->setSession('orderer', $_POST);
        break;
      case 'additionDelivery':
        $cart->setSession('delivery', $_POST);
        break;
      case 'deleteDelivery':
        $cart->deleteSession('delivery', $_POST);
        break;
      case 'additionSettlement':
        $cart->setSession('settlement', $_POST);
        break;
      case 'additionDeliveryAndSettlement':
        //$cart->setDelivery($_POST);
        //$cart->setSettlement($_POST);
        break;
      case 'additionItem':
        $cart->setSession('item', $_POST);        
        break;
      case 'deleteItem':
        $cart->deleteSession('item', $_POST);
        break;
      case 'deleteAllItem':
        $_POST['index'] = null;
        $cart->deleteSession('item', $_POST);
        break;
      case 'changeItem':
        $cart->setSession('item', $_POST);
        break;
    }
    
    $cart->total();
    //$cart->keep();
    if($function == "keep"){
      $cart->keep($_POST);
    }

    //print_r($cart);
    
    if($this->getDataType() == 'json'){
      echo json_encode( $cart, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $cart;
  }
}

// ?>