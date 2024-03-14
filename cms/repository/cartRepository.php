<?php 
namespace host\cms\repository;

use host\cms\repository\utilityRepository;
use host\cms\repository\memberRepository;
use host\cms\repository\settlementRepository;
use host\cms\repository\deliveryDateTimeRepository;
use host\cms\repository\mailTemplatesRepository;

class cartRepository extends dbRepository {
  
  use \host\cms\entity\cartEntity;

  //合計金額
  public function total(){
    $site_id = $this->getSiteId();
    if(!$site_id){
      return false;
    }
    
    $session_orderer = $this->getSession('orderer');
    $session_delivery = $this->getSession('delivery');

    $total = (object) array(
      'price' => 0,
      'tax_price' => 0,
      'notax_price' => 0,
      'tax' => 0,
      'by_tax_rate' => array()
    );
    
    foreach($session_delivery->row as $d){
      foreach($d->packing as $p){
        $total->price += $p->delivery_price;
        $total->tax_price += $p->delivery_tax_price;
        $total->notax_price += $p->delivery_notax_price;
        $total->tax += $p->delivery_tax;
        foreach($p->delivery_by_tax_rate as $rate => $value){
          if(!$total->by_tax_rate[$rate]){
            $total->by_tax_rate[$rate] = new \StdClass;
          }
          $total->by_tax_rate[$rate]->notax_price += $value->notax_price;
          $total->by_tax_rate[$rate]->tax += $value->tax;
        }
      }
    }

    if($session_orderer->data->item_total_price){
      $total->price += $session_orderer->data->item_total_price;
      $total->tax_price += $session_orderer->data->item_total_tax_price;
      $total->notax_price += $session_orderer->data->item_total_notax_price;
      $total->tax += $session_orderer->data->item_total_tax;
      foreach($session_orderer->data->item_total_by_tax_rate as $rate => $value){
        if(!$total->by_tax_rate[$rate]){
          $total->by_tax_rate[$rate] = new \StdClass;
        }
        $total->by_tax_rate[$rate]->notax_price += $value->notax_price;
        $total->by_tax_rate[$rate]->tax += $value->tax;
      }
    }
    
    if($session_orderer->data->settlement_price){
      $total->price += $session_orderer->data->settlement_price;
      $total->tax_price += $session_orderer->data->settlement_tax_price;
      $total->notax_price += $session_orderer->data->settlement_notax_price;
      $total->tax += $session_orderer->data->settlement_tax;
      foreach($session_orderer->data->settlement_by_tax_rate as $rate => $value){
        if(!$total->by_tax_rate[$rate]){
          $total->by_tax_rate[$rate] = new \StdClass;
        }
        $total->by_tax_rate[$rate]->notax_price += $value->notax_price;
        $total->by_tax_rate[$rate]->tax += $value->tax;
      }
    }
    foreach($total as $column => $value){
      $session_orderer->data->{"total_".$column} = $value;
    }
    return true;
  }
  
  //セッションを注文として保管する
  public function keep(array $post){
    
    $this->set_status(false);
    $token = @$post['token'];
    
    $ut = new utilityRepository;
    if(!$token || $token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
      return $this;
    }

    $site_id = $this->getSiteId();
    if(!$site_id){
      $this->set_message('サイトを選択してください。');
      return $this;
    }
    
    $process_type = $this->getProcessType();
    $charge_authority = $this->getSession('item')->charge_authority;
    $charge_seller_id = $this->getSession('item')->charge_seller_id;
    $charge_type = $this->getSession('item')->charge_type;
    $account = $this->getSession('user');
    $member = $this->getSession('member');
    $orderer = $this->getSession('orderer');
    $item = $this->getSession('item')->row;
    $delivery = $this->getSession('delivery')->row;

    if(!$orderer->data->first_name){
      $this->set_message('ご注文者をご確認下さい。');
      return $this;
    }
    if(!$item){
      $this->set_message('商品をご確認下さい。');
      return $this;
    }
    if(!$delivery){
      $this->set_message('配送先をご確認下さい。');
      return $this;
    }
    if(!$orderer->data->settlement_id){
      $this->set_message('決済方法をご確認下さい。');
      return $this;
    }

    $orderer->member->token = $token;
    
    //会員登録
    if($orderer->member_registration > 0){
      $param = array(
        'status_kbn' => 1,
        '_password' => $account->id,
        'member_id' => $orderer->member->password
      );
      foreach($orderer->member as $column => $value){
        $param[$column] = $value;
      }
      $m = new memberRepository;
      $m->setSiteId($site_id);
      $m->setPostMember($param);
      $m->filterMember();
      if(!$m->_status){
        $this->_message = $m->_message;
        return $this;
      }
      $m->pushMember();
      if(!$m->_status){
        $this->_message = $m->_message;
        return $this;
      }
      $this->memberId = $m->lastInsertId();
    }
    //会員ID判定
    $member_id = null;
    if($member->id || $this->memberId){
      $member_id = $member->id ? $member->id : $this->memberId;
    }

    $this->connect();
    $this->beginTransaction();
    try {

      //決済の後処理
      /*
      $send_settlement = new cartSettlementRepository;
      $send_settlement->setSiteId($site_id);
      $send_result = $send_settlement->sendResult($this);
      if(!$send_result->_status){
        if($send_result->_message){
          foreach($send_result->_message as $message){
            $this->set_message($message);
          }
        }
        return $this;
      }
      */
      
      $param = array(
        'id' => "",
        'site_id' => $site_id,
        'account_id' => $account->id,
        'member_id' => $member_id,
        'seller_id' => $charge_seller_id,
        'session_information' => $_SESSION
      );
      foreach($orderer->data as $column => $value){
        if(!isset($param[$column])){
          $param[$column] = $value;
        }
      }
      
      if($charge_type == 'repeat' && $process_type != 'batch'){
        $o = new orderRepeatRepository;
        $o->setPost($param);
        $query = $this->queryCreate($o->getPost()->data, 'order_repeat_tbl');        
        $stmt = $this->prepare($query['query']);
        if(!$stmt->execute($query['params'])){
          $this->rollBack();
        }
        $this->orderRepeatId = $this->lastInsertId();
      }
      
      $o = new orderRepository;
      $o->setPost($param);
      $query = $this->queryCreate($o->getPost()->data, 'order_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }

      $this->orderId = $this->lastInsertId();
      //print_r($this);

      //ご注文伝票明細の生成
      foreach($delivery as $row){
        foreach($row->packing as $packing){
          /*
          if(!$row->delivery_date && $p->charge_type == 'subscription'){
            //定期の場合は、配送希望日を次回配送日とする
            $d_row->delivery_date = $packing->next_delivery_date;
          }
          */
          $param = array(
            'id' => "",
            'site_id' => $site_id,
            'order_id' => $this->orderId,
            'account_id' => $account->id,
            'member_id' => $member_id,
            'seller_id' => $charge_seller_id
          );
          foreach($row->data as $column => $value){
            if(!isset($param[$column])){
              $param[$column] = $value;
            }
          }
          foreach($packing as $column => $value){
            if(!$param[$column]){
              $param[$column] = $value;
            }
          }
          $od = new orderDeliveryRepository;
          $od->setPost($param);
          $query = $this->queryCreate($od->getPost()->data, 'order_delivery_tbl');
          $stmt = $this->prepare($query['query']);
          if(!$stmt->execute($query['params'])){
            $this->rollBack();
          }
          $this->orderDeliveryId = $this->lastInsertId();

          if($charge_type == 'repeat' && $process_type != 'batch'){
            $param['order_repeat_id'] = $this->orderRepeatId;
            $od = new orderRepeatDeliveryRepository;
            $od->setPost($param);
            $query = $this->queryCreate($od->getPost()->data, 'order_repeat_delivery_tbl');        
            $stmt = $this->prepare($query['query']);
            if(!$stmt->execute($query['params'])){
              $this->rollBack();
            }
            $this->orderRepeatDeliveryId = $this->lastInsertId();
          }

          foreach($packing->index_items as $i){
            
            //商品（フィールド、ストック）、付属品、キャンペン
            $push = $item[$i];
            $rank = 0;
            $param = array(
              'site_id' => $site_id,
              'order_delivery_id' => $this->orderDeliveryId,
              'rank' => $rank,
              'account_id' => $account->id,
              'member_id' => $member_id,
              'seller_id' => $charge_seller_id,
              'product_id' => $push->product_id,
              'repeat_product_id' => $push->repeat_product_id,
              'repeat_product_item_id' => $push->repeat_product_item_id,
              'stock_code' => $push->field->code,
              'stock_name' => $push->field->name,
              'model' => $push->model,
              'name' => $push->name,
              'tax_class' => $push->tax_class,
              'tax_rate' => $push->tax_rate,
              'unit_name' => $push->unit_name,
              'unit_price' => $push->unit_price,
              'unit_tax_price' => $push->unit_tax_price,
              'unit_notax_price' => $push->unit_notax_price,
              'unit_tax' => $push->unit_tax,
              'quantity' => $push->quantity,
              'price' => $push->price,
              'tax_price' => $push->tax_price,
              'notax_price' => $push->notax_price,
              'tax' => $push->tax
            );
            $oi = new orderItemRepository;
            $oi->setPost($param, 'diff');
            $query = $this->queryCreate($oi->getPost()->data, 'order_item_tbl');
            $stmt = $this->prepare($query['query']);
            if(!$stmt->execute($query['params'])){
              $this->rollBack();
            }
            
            if($push->option_include){
              foreach($push->option_include as $option){
                if($option->selected->name){
                  $rank += 1;
                  $param = array(
                    'site_id' => $site_id,
                    'order_delivery_id' => $this->orderDeliveryId,
                    'rank' => $rank,
                    'account_id' => $account->id,
                    'member_id' => $member_id,
                    'seller_id' => $charge_seller_id,
                    'product_included_id' => $option->id,
                    'name' => $option->name,
                    'remarks' => $option->selected->name,
                    'tax_class' => $option->selected->tax_class,
                    'tax_rate' => $option->selected->tax_rate,
                    'unit_price' => $option->selected->unit_price,
                    'unit_tax_price' => $option->selected->unit_tax_price,
                    'unit_notax_price' => $option->selected->unit_notax_price,
                    'unit_tax' => $option->selected->unit_tax,
                    'quantity' => $option->quantity,
                    'price' => $option->selected->price,
                    'tax_price' => $option->selected->tax_price,
                    'notax_price' => $option->selected->notax_price,
                    'tax' => $option->selected->tax
                  );
                  $oi = new orderItemRepository;
                  $oi->setPost($param, 'diff');
                  $query = $this->queryCreate($oi->getPost()->data, 'order_item_tbl');
                  $stmt = $this->prepare($query['query']);
                  if(!$stmt->execute($query['params'])){
                    $this->rollBack();
                  }
                }
                if($option->input->value){
                  $rank += 1;
                  $param = array(
                    'site_id' => $site_id,
                    'order_delivery_id' => $this->orderDeliveryId,
                    'rank' => $rank,
                    'account_id' => $account->id,
                    'member_id' => $member_id,
                    'seller_id' => $charge_seller_id,
                    'product_included_id' => $option->id,
                    'name' => $option->input->name,
                    'remarks' => $option->input->value,
                    'tax_class' => $option->input->tax_class,
                    'tax_rate' => $option->input->tax_rate,
                    'unit_price' => $option->input->unit_price,
                    'unit_tax_price' => $option->input->unit_tax_price,
                    'unit_notax_price' => $option->input->unit_notax_price,
                    'unit_tax' => $option->input->unit_tax,
                    'quantity' => $option->quantity,
                    'price' => $option->input->price,
                    'tax_price' => $option->input->tax_price,
                    'notax_price' => $option->input->notax_price,
                    'tax' => $option->input->tax
                  );
                  $oi = new orderItemRepository;
                  $oi->setPost($param, 'diff');
                  $query = $this->queryCreate($oi->getPost()->data, 'order_item_tbl');
                  $stmt = $this->prepare($query['query']);
                  if(!$stmt->execute($query['params'])){
                    $this->rollBack();
                  }
                }
              }
            }
            if($push->campaign){
              foreach($push->campaign as $campaign){
                $rank += 1;
                $param = array(
                  'site_id' => $site_id,
                  'order_delivery_id' => $this->orderDeliveryId,
                  'rank' => $rank,
                  'account_id' => $account->id,
                  'member_id' => $member_id,
                  'seller_id' => $charge_seller_id,
                  'campaign_id' => $campaign->id,
                  'name' => $campaign->name,
                  'remarks' => $campaign->value,
                  'tax_class' => $push->tax_class,
                  'tax_rate' => $push->tax_rate,
                  'unit_price' => $campaign->discount_unit_price,
                  'unit_tax_price' => $campaign->discount_unit_tax_price,
                  'unit_notax_price' => $campaign->discount_unit_notax_price,
                  'unit_tax' => $campaign->discount_unit_tax,
                  'quantity' => $push->quantity,
                  'price' => $campaign->discount_price,
                  'tax_price' => $campaign->discount_tax_price,
                  'notax_price' => $campaign->discount_notax_price,
                  'tax' => $campaign->discount_tax
                );
                $oi = new orderItemRepository;
                $oi->setPost($param, 'diff');
                $query = $this->queryCreate($oi->getPost()->data, 'order_item_tbl');
                $stmt = $this->prepare($query['query']);
                if(!$stmt->execute($query['params'])){
                  $this->rollBack();
                }
              }
            }
            $this->orderItemId = $this->lastInsertId();

            if($charge_type == 'repeat' && $process_type != 'batch'){
              
              $param['order_repeat_delivery_id'] = $this->orderRepeatDeliveryId;
              $param['series_number_count'] = 1;
              $param['repeat_count'] = 1;
              $param['skip_count'] = 0;
              $param['last_check_date'] = $product->row[$i]->next_order_date;

              $oi = new orderRepeatItemRepository;
              $oi->setPost($param, 'diff');
              $query = $this->queryCreate($oi->getPost()->data, 'order_repeat_item_tbl');        
              $stmt = $this->prepare($query['query']);
              if(!$stmt->execute($query['params'])){
                $this->rollBack();
              }
              $this->orderRepeatItemId = $this->lastInsertId();
            }
          }
        }
      }
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    
    /*
    if($process_type != 'batch'){
      //メールテンプレートを読み込み、送信処理
      $ml = new mailTemplatesRepository;
      $ml->setType('autoOrder');
      $ml->setSiteId($site_id);
      $ml->setOrderId($this->orderId);
      //$ml->setReservation(1);
      $ml->getOrderTemplate();
      $result = $ml->push();
      if(!$result->get_status()){
        return $result;
      }
      $ml->sendSwift();
    }
    */

    /*
    //503回避 バックグラウンド処理
    $time = new \DateTime();
    exec('nohup php exec.php 2> ./error/error_'.$time->format('YmdHis').'.log');
    //exec('nohup php exec.php > /dev/null &');
    */
    
    //$this->clearSession();
    return $this;
  }  
}
// ?>