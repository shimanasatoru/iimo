<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\settlementRepository;
use host\cms\repository\paymentZeusSecureLinkRepository;

class orderSettlementRepository extends dbRepository {
  
  use \host\cms\entity\orderSettlementEntity;
  
  public function setSettlement(object $post, object $total_item) :object{
    
    $this->set_status(false);
    
    $site_id = $this->getSiteId();
    $settlement_id = $post->data->settlement_id;
    $zeus_token_get = $post->data->zeus_token_get;
    
    if(!$site_id){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    if(!$settlement_id){
      $this->set_message('決済方法が選択されていません。');
      return $this;
    }
    if(!$total_item->item_total_tax_price){
      $this->set_message('商品合計金額がありません。');
      return $this;
    }
    
    $settlement = new settlementRepository;
    $settlement->setSiteId($site_id);
    $settlement->setId($settlement_id);
    $settlement->setReleaseKbn(1);
    $settlement->setLimit(1);
    $s = $settlement->get()->row[0];
    if(!$s){
      $this->set_message('決済設定がありません。');
      return $this;
    }

    if($s->method == 'zeus_secure_link'){
      if($id['zeus_token_get']){
        $_POST['authkey1'] = $s->authkey1;
        $_POST['authkey2'] = $s->authkey2;
        
        $zeus = new paymentZeusSecureLinkRepository;
        $zeus->setAuthPost($_POST);
        $zeus_token = $zeus->getToken();
        if(!$zeus_token->_status){
          return $zeus_token;
        }
        $this->various = (object) $zeus_token;
      }
    }
    
    $array = (object) array(
      'settlement_id' => $s->id,
      'settlement_tax_class' => $s->tax_class,
      'settlement_tax_rate' => $s->tax_rate,
      'settlement_price' => 0,
      'settlement_tax_price' => 0,
      'settlement_notax_price' => 0,
      'settlement_tax' => 0,
      'settlement_by_tax_rate' => array(
        $s->tax_rate => array(
          'notax_price' => 0,
          'tax' => 0
        )
      )
    );
    foreach($s->target_price as $i => $target){
      if($target && $target >= $total_item->item_total_tax_price){
        $array->settlement_price = (int) $s->price[$i];
        $array->settlement_tax_price = (int) $s->tax_price[$i];
        $array->settlement_notax_price = (int) $s->notax_price[$i];
        $array->settlement_tax = (int) $s->tax[$i];
        $array->settlement_by_tax_rate[$s->tax_rate]->notax_price = $this->notax_price;
        $array->settlement_by_tax_rate[$s->tax_rate]->tax = $this->tax;
        break;
      }
    }
    $this->row = $array;
    $this->set_status(true);
    return $this;
  }
  
  

}
// ?>