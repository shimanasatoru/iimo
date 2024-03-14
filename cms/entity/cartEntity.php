<?php 
namespace host\cms\entity;

use host\cms\repository\settlementRepository;
use host\cms\repository\deliveryDateTimeRepository;

use host\cms\repository\orderRepository;
use host\cms\repository\orderItemRepository;
use host\cms\repository\orderDeliveryRepository;
use host\cms\repository\orderSettlementRepository;

trait cartEntity{

  public $master;
  public $process_type;
  public $orderer;
  public $item;
  public $delivery;
  public $settlement;
  public $total;
  public function __construct () {
    
    $this->process_type = null;
    $this->master = new \StdClass;
    $this->orderer = (object) array(
      'data' => new \StdClass,
    );
    if(!@$_SESSION['orderer']){
      $_SESSION['orderer'] = $this->orderer;
    }else{
      $this->orderer = $_SESSION['orderer'];
    }
    
    $this->item = (object) array(
      'row' => array(),
      'charge_authority' => null, //権限(default=サイト運営,seller=出品者)
      'charge_seller_id' => null,
      'charge_type' => null, //default=一般商品、repeat=定期商品
    );
    if(!@$_SESSION['item']){
      $_SESSION['item'] = $this->item;
    }else{
      $this->item = $_SESSION['item'];
    }

    $this->delivery = (object) array(
      'row' => array()
    );
    if(!@$_SESSION['delivery']){
      $_SESSION['delivery'] = $this->delivery;
    }else{
      $this->delivery = $_SESSION['delivery'];
    }

    if(!@$_SESSION['settlement']){
      $_SESSION['settlement'] = $this->settlement;
    }else{
      $this->settlement = $_SESSION['settlement'];
    }
  }
  
  public function loadMaster(){
    $site_id = $this->getSiteId();
    if(!$site_id){
      return false;
    }
    $settlement = new settlementRepository;
    $settlement->setSiteId($site_id);
    $settlement->setReleaseKbn(1);
    $this->master->settlement = $settlement->get()->row;

    $dateTime = new deliveryDateTimeRepository;
    $dateTime->setSiteId($site_id);
    $dateTime->setLimit(1);
    $dt = $dateTime->get();
    $this->master->deliveryDateTime = $dt->row[0];
    return true;
  }
  
  public function getSession($session_name){
    switch($session_name){
      case 'user':
        return $_SESSION['user'];
        break;
      case 'member':
        return $_SESSION['member'];
        break;
      case 'orderer':
        return $_SESSION['orderer'];
        break;
      case 'item':
        return $_SESSION['item'];
        break;
      case 'delivery':
        return $_SESSION['delivery'];
        break;
      case 'settlement':
        return $_SESSION['settlement'];
        break;
    }
    return false;
  }
  
  public function clearSession(){
    unset(
      $_SESSION['orderer'],
      $_SESSION['item'],
      $_SESSION['delivery'],
      $_SESSION['settlement']
    );
  }
  
  public function setSession($session_name, array $post) :object{
    
    $this->set_status(false);
    $account = $this->getSession('user');
    $member = $this->getSession('member');
    $session_orderer = $this->getSession('orderer');
    $session_item = $this->getSession('item');
    $session_delivery = $this->getSession('delivery');

    switch($session_name){
      case 'orderer':
        $orderer = new orderRepository;
        $orderer->setSiteId($this->getSiteId());
        if($account->id){
          $orderer->setAccountId($account->id);
        }
        if($member->id){
          foreach($member as $column => $value){
            $post[$column] = $value;
          }
        }
        if(!$member->id && $session_item->charge_type == "repeat"){
          $orderer->setMemberRegistration(true);//リピート商品は会員必須
        }
        $orderer->setPost($post, 'diff');
        $get = $orderer->getPost();
        $judge = $orderer->judgeFilter($get);
        if(!$judge->_status){
          $this->_message = $judge->_message;
          $this->_invalid = $judge->_invalid;
          return $this;
        }
        foreach($get as $data => $class){
          if(!$session_orderer->{$data}){
            $session_orderer->{$data} = new \StdClass;
          }
          if(is_object($class) || is_array($class)){
            foreach($class as $column => $value){
              $session_orderer->{$data}->{$column} = $value;
            }
            continue;
          }
          $session_orderer->{$data} = $class;
        }
        $this->orderer = $session_orderer;
        $this->set_status(true);
        break;
        
      case 'item':
        $item = new orderItemRepository;
        $item->setSiteId($this->getSiteId());
        $item->setChargeAuthority($session_item->charge_authority);
        $item->setChargeSellerId($session_item->charge_seller_id);
        $item->setChargeType($session_item->charge_type);
        $item->setSiteId($this->getSiteId());
        if($account->id){
          $item->setAccountId($account->id);
        }
        $item->setPost($post);
        $get = $item->getPost();
        $judge = $item->judgeFilter($get);
        if(!$judge->_status){
          $this->_message = $judge->_message;
          $this->_invalid = $judge->_invalid;
          return $this;
        }
        $session_item->charge_authority = $judge->charge_authority;
        $session_item->charge_seller_id = $judge->charge_seller_id;
        $session_item->charge_type = $judge->charge_type;
        if($judge->post->input->index !== null){
          $session_item->row[$judge->post->input->index] = (object) $judge->item;
        }else{
          $session_item->row[] = (object) $judge->item;
        }
        $total_item = $item->setTotalPrice($session_item->row);
        foreach($total_item as $column => $value){
          $session_orderer->data->{"item_total_".$column} = $value;
        }
        $this->item = $session_item;
        $this->set_status(true);
        break;
        
      case 'delivery':
        $delivery = new orderDeliveryRepository;
        $delivery->setSiteId($this->getSiteId());
        if($account->id){
          $delivery->setAccountId($account->id);
        }
        $delivery->setPost($post);
        $get = $delivery->getPost();
        $judge = $delivery->judgeFilter($get);
        if(!$judge->_status){
          $this->_message = $judge->_message;
          $this->_invalid = $judge->_invalid;
          return $this;
        }
        $session_delivery->row[$get->input->index] = (object) $get;
        $this->set_status(true);
        break;
        
      case 'settlement':
        $settlement = new orderSettlementRepository;
        $settlement->setSiteId($this->getSiteId());
        $settlement->setPost($post);
        $get = $settlement->setSettlement($settlement->getPost(), $session_orderer->data);
        if(!$get->_status){
          $this->_message = $get->_message;
          $this->_invalid = $get->_invalid;
          return $this;
        };
        foreach($get->row as $column => $value){
          $session_orderer->data->{$column} = $value;
        }
        $this->set_status(true);
        break;
    }

    $recalucation = new orderDeliveryRepository;
    $recalucation->setSiteId($this->getSiteId());
    $packing = $recalucation->setPacking($session_item->row, $session_delivery->row);
    if($packing->_status && $packing->row){
      foreach($session_delivery->row as $data){
        $data->packing = array();
      }
      foreach($packing->row as $i => $data){
        $session_delivery->row[$i]->packing = $data;
      }
      $this->delivery = $session_delivery;
    }
    return $this;
  }
  
  public function deleteSession($session_name, array $post) :object{
    
    $this->set_status(false);
    $account = $this->getSession('user');
    $member = $this->getSession('member');
    $session_orderer = $this->getSession('orderer');
    $session_item = $this->getSession('item');
    $session_delivery = $this->getSession('delivery');

    switch($session_name){
      case 'item':
        $item = new orderItemRepository;
        $item->setSiteId($this->getSiteId());
        $item->setPost($post, 'diff');
        $get = $item->getPost();
        if($get->input->index !== null){
          unset($session_item->row[$get->input->index]);
        }else{
          $session_item->row = array();
        }
        if(!$session_item->row){
          $session_item->charge_authority = null;
          $session_item->charge_seller_id = null;
          $session_item->charge_type = null;
        }
        $total_item = $item->setTotalPrice($session_item->row);
        foreach($total_item as $column => $value){
          $session_orderer->data->{"item_total_".$column} = $value;
        }
        $this->item = $session_item;
        $this->set_status(true);
        break;
        
      case 'delivery':
        $delivery = new orderDeliveryRepository;
        $delivery->setSiteId($this->getSiteId());
        if($account->id){
          $delivery->setAccountId($account->id);
        }
        $delivery->setPost($post);
        $get = $delivery->getPost();
        
        $index_delivery = 0;
        if($get->input->index !== null){
          $index_delivery = $get->input->index;
        }
        unset($session_delivery->row[$index_delivery]);
        $this->set_status(true);
        break;
    }
    
    $recalucation = new orderDeliveryRepository;
    $recalucation->setSiteId($this->getSiteId());
    $packing = $recalucation->setPacking($session_item->row, $session_delivery->row);
    if($packing->_status && $packing->row){
      foreach($session_delivery->row as $data){
        $data->packing = array();
      }
      foreach($packing->row as $i => $data){
        $session_delivery->row[$i]->packing = $data;
      }
      $this->delivery = $session_delivery;
    }
    return $this;
  }

  public function getProcessType(){
    return $this->process_type;
  }
  public function setProcessType(string $process_type) :void{
    $this->process_type = $process_type;
  }

  private $id;
  public function getId(){
    return $this->id;
  }
  public function setId(int $id) :void{
    $this->id = $id;
  }
  
  private $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
}

// ?>