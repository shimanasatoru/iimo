<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\orderRepeatRepository;
use host\cms\repository\orderRepeatDeliveryRepository;
use host\cms\repository\orderRepeatItemRepository;
use host\cms\repository\settlementRepository;
use Smarty;

class orderRepeatController{

  /*
   * 検索用
   * @params request get
   */
  public function getAction(){
    $site_id = $_SESSION['site']->id;
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    $p = new orderRepeatRepository;
    $p->setSiteId($site_id);
    if($params['id']){
      $p->setId($params['id']);
    }
    $result = $p->get();
    if($params['dataType'] == 'json'){
      echo json_encode( $result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  /*
   * 受注一覧
   */
  public function indexAction(){
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'data' => $this->getAction()
    ]);
    $tpl->display("order/repeat/index.tpl");
  }
  
  public function viewAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);

    $order = new orderRepeatRepository;
    $data = array();
    if($id){
      $order->setSiteId($_SESSION['site']->id);
      $order->setId($id);
      $order->setLimit(1);
      $data = $order->get()->row[0];
    }

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $params,
      'tax_rate'=> $ut->masterLoader('tax_rate'),
      'temperature_zone' => $ut->masterLoader('temperature_zone'),
      'prefectures' => $ut->masterLoader('prefectures'),
      'corporation_kbn' => $order->corporation_kbn,
      'honorific_title' => $order->honorific_title,
      'data'  => $data
    ]);
    $tpl->display("order/repeat/view.tpl");
  }
  
  public function editOrdererAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $site_id = $_SESSION['site']->id;
    $id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    
    $order = new orderRepeatRepository;
    $data = array();
    if($id){
      $order->setSiteId($site_id);
      $order->setId($id);
      $order->setLimit(1);
      $data = $order->get()->row[0];
    }
    
    $settlement = new settlementRepository;
    $settlement->setSiteId($site_id);
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'prefectures' => $order->prefectures,
      'corporation_kbn' => $order->corporation_kbn,
      'honorific_title' => $order->honorific_title,
      'settlement' => $settlement->get()->row,
      'data' => $data
    ]);
    $tpl->display("order/repeat/orderer.tpl");
  }

  public function pushOrdererAction(){
    $site_id = $_POST['site_id'] = $_SESSION['site']->id;
    $account_id = $_POST['account_id'] = $_SESSION['user']->id;
    
    $o = new orderRepeatRepository;
    $o->setSiteId($site_id);
    $o->setAccountId($account_id);
    $o->setPost($_POST, 'diff');
    $result = $o->push();
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result);
      return true;
    }
    return $result;
  }
  
  public function editDeliveryAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $site_id = $_SESSION['site']->id;
    $order_id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $order_delivery_id = filter_var(@$params[3], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$order_id){
      return false;
    }
    
    $delivery = new orderRepeatDeliveryRepository;
    $data = array();
    if($order_delivery_id){
      $delivery->setSiteId($site_id);
      $delivery->setOrderRepeatId($order_id);
      $delivery->setId($order_delivery_id);
      $delivery->setLimit(1);
      $data = $delivery->get()->row[0];
    }

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'order_id' => $order_id,
      'order_delivery_id' => $order_delivery_id,
      'prefectures' => $delivery->prefectures,
      'corporation_kbn' => $delivery->corporation_kbn,
      'honorific_title' => $delivery->honorific_title,
      'data' => $data
    ]);
    $tpl->display("order/repeat/delivery.tpl");
  }
  
  public function pushDeliveryAction(){
    
    $_POST['site_id'] = $site_id = $_SESSION['site']->id;
    $_POST['account_id'] = $account_id = $_SESSION['user']->id;
    
    $o = new orderRepeatDeliveryRepository;
    $o->setSiteId($site_id);
    $o->setPost($_POST, 'diff');
    $result = $o->push();
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result);
      return true;
    }
    return $result;
  }
  
  public function editItemAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $site_id = $_SESSION['site']->id;
    $order_id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $order_delivery_id = filter_var(@$params[3], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $item_id = filter_var(@$params[4], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$order_id || !$order_delivery_id){
      return false;
    }
    
    $item = new orderRepeatItemRepository;
    $data = array();
    if($item_id){
      $item->setSiteId($site_id);
      $item->setOrderRepeatDeliveryId($order_delivery_id);
      $item->setId($item_id);
      $item->setLimit(1);
      $data = $item->get()->row[0];
    }

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'order_id' => $order_id,
      'order_delivery_id' => $order_delivery_id,
      'order_delivery_item_id' => $item_id,
      'data' => $data
    ]);
    $tpl->display("order/repeat/item.tpl");
  }
  
  public function pushItemAction(){
    
    $_POST['site_id'] = $site_id = $_SESSION['site']->id;
    $_POST['account_id'] = $account_id = $_SESSION['user']->id;
    
    $o = new orderRepeatItemRepository;
    $o->setSiteId($site_id);
    $o->setPost($_POST, 'diff');
    $result = $o->push();
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result);
      return true;
    }
    return $result;
  }


}

// ?>