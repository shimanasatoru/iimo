<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\mailRepository;
use host\cms\repository\mailTemplatesRepository;
use host\cms\repository\deliveryDateTimeRepository;
use host\cms\repository\settlementRepository;
use host\cms\repository\orderRepository;
use host\cms\repository\orderDeliveryRepository;
use host\cms\repository\orderItemRepository;
use Smarty;

class orderController{

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
    $p = new orderRepository;
    $p->setSiteId($site_id);
    if($_SESSION['user']->authcode == "seller"){
      $p->setSellerId($_SESSION['user']->id);
    }
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
    $tpl->display("order/index.tpl");
  }
  
  public function viewAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);

    $order = new orderRepository;
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
    $tpl->display("order/view.tpl");
  }
  
  public function editOrdererAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $site_id = $_SESSION['site']->id;
    $id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    
    $order = new orderRepository;
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
    $tpl->display("order/orderer.tpl");
  }

  public function pushOrdererAction(){
    
    $site_id = $_POST['site_id'] = $_SESSION['site']->id;
    $account_id = $_POST['account_id'] = $_SESSION['user']->id;
    
    $o = new orderRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    if(@$params[2] == "read"){
      $o->setPost([
        'token'=> $_POST['token'], 
        'id'=> $_POST['id'], 
        'site_id'=> $site_id, 
        'status_read'=> 1
      ], 'diff');
      $result = $o->update();
    }else{
      $o->setSiteId($site_id);
      $o->setAccountId($account_id);
      $o->setPost($_POST, 'diff');
      $result = $o->push();
    }
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
    
    $time = new deliveryDateTimeRepository;
    $time->setSiteId($site_id);
    $delivery_time_zone_value = $time->get()->row[0]->time_zone_value;

    $delivery = new orderDeliveryRepository;
    $data = array();
    if($order_delivery_id){
      $delivery->setSiteId($site_id);
      $delivery->setOrderId($order_id);
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
      'delivery_time_zone_value' => $delivery_time_zone_value,
      'data' => $data
    ]);
    $tpl->display("order/delivery.tpl");
  }
  
  public function pushDeliveryAction(){
    
    $_POST['site_id'] = $site_id = $_SESSION['site']->id;
    $_POST['account_id'] = $account_id = $_SESSION['user']->id;
    
    $o = new orderDeliveryRepository;
    $o->setSiteId($site_id);
    $o->setPost($_POST, 'diff');
    $result = $o->push();
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
    
    $item = new orderItemRepository;
    $data = array();
    if($item_id){
      $item->setSiteId($site_id);
      $item->setOrderDeliveryId($order_delivery_id);
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
    $tpl->display("order/item.tpl");
  }
  
  public function pushItemAction(){
    
    $_POST['site_id'] = $site_id = $_SESSION['site']->id;
    $_POST['account_id'] = $account_id = $_SESSION['user']->id;
    
    $o = new orderItemRepository;
    $o->setSiteId($site_id);
    $o->setPost($_POST, 'diff');
    $result = $o->push();
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }

  public function editMailAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $site_id = $_SESSION['site']->id;
    $order_id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $order_delivery_id = filter_var(@$params[3], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$order_id || !$order_delivery_id){
      return false;
    }
    
    $od = new orderRepository;
    $od->setSiteId($site_id);
    $od->setId($order_id);
    $od->setLimit(1);
    $order = $od->get()->row[0];
    if(!$order->id){
      return false;
    }
    
    $hi = new mailRepository;
    $hi->setSiteId($site_id);
    $hi->setOrderId($order_id);
    $hi->setOrderDeliveryId($order_delivery_id);
    $history = $hi->get();

    $mt = new mailTemplatesRepository;
    $mt->setSiteId($site_id);
    $mt->setTypeIs(false);
    $mail_order_details = $mt->orderDetails($order);
    $mail_templates = $mt->get()->row;
    if($mail_templates){
      $table = array(
        '{$first_name}'=>$order->first_name,
        '{$last_name}'=>$order->last_name,
        '{$first_name_kana}'=>$order->first_name_kana,
        '{$last_name_kana}'=>$order->last_name_kana,
        '{$orderDetails}'=> $mail_order_details,
        '{$serviceName}'=> SERVICE_NAME,
        '{$serviceMail}'=> SERVICE_MAIL,
        '{$hostName}'=> HOST_NAME,
        '{$hostMail}'=> HOST_MAIL,
        '{$toMail}'=> $order->email_address,
        '&#13;&#10;'=> "\n",
        '&#10;'=> "\n"
      );
      foreach($mail_templates as $template){
        $search = array_keys($table);
        $replace = array_values($table);
        $template->template = str_replace($search,$replace,$template->template);
      }
    }

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'order_id' => $order_id,
      'order_delivery_id' => $order_delivery_id,
      'member_id' => $order->member_id,
      'order' => $order,
      'history' => $history,
      'mail_templates' => $mail_templates
    ]);
    $tpl->display("order/mail.tpl");
  }

  public function pushMailAction(){
    
    $_POST['site_id'] = $site_id = $_SESSION['site']->id;    
    $_POST['from_mail'] = array($_POST['from_mail'] => $_POST['from_name']);
    
    $m = new mailRepository;
    $m->setSiteId($site_id);
    $m->setPost($_POST);
    $result = $m->push();
    if($result->get_status() && $result->_lastId){
      $send = new mailRepository;
      $send->setSiteId($site_id);
      $send->setId($result->_lastId);
      $mail = $send->get()->row[0];
      if($mail->id){
        $send->setToMail([$mail->to_mail]);
        $send->setFromMail($mail->from_mail);
        $send->setSubject($mail->subject);
        $send->setBody($mail->body);
        $result = $send->sendSwift();
      }
    }
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }

}

// ?>