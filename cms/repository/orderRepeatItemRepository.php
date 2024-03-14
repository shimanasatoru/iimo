<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class orderRepeatItemRepository extends dbRepository {
  
  use \host\cms\entity\orderRepeatItemEntity;
  
  public function get() {
    //各種マスタは削除されていても必ず読み込む（過去の状況察知のため）
    self::setSelect("ori.*, rp.repeat_type, rp.first_shipping_date_class, rp.first_shipping_date, rp.delivery_date_cycle_unit, rp.delivery_date_cycle, rp.delivery_week_cycle, rp.shipping_date_cycle, rp.settlement_date_cycle, rp.cancel_skip_date_cycle, rp.cycle_number_limit");
    self::setFrom("
      order_repeat_item_tbl ori 
      LEFT JOIN repeat_product_tbl rp ON ori.repeat_product_id = rp.id AND rp.delete_kbn IS NULL
    ");
    self::setWhere("ori.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("ori.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("ori.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($order_repeat_delivery_id = self::getOrderRepeatDeliveryId()){
      self::setWhere("ori.order_repeat_delivery_id = :order_repeat_delivery_id");
      self::setValue(":order_repeat_delivery_id", $order_repeat_delivery_id);
    }
    self::setOrder("ori.rank ASC");
    $q = self::getSelect()
        .self::getFrom()
        .self::getWhere()
        .self::getOrder()
        .self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      $stmt_allNumber = $this->prepare("SELECT FOUND_ROWS() as `allNumber`");
      $stmt_allNumber->execute();
      while($d = $stmt_allNumber->fetch(\PDO::FETCH_OBJ)){
        $this->totalNumber = $d->allNumber;
      }
      while($i = $stmt->fetch(\PDO::FETCH_OBJ)){
        //サイクル日を起算日にお届け日、出荷日、決済日（次回、販売商品）、解約日を算出
        $i->delivery_date = null;
        switch($i->delivery_date_cycle_unit){
          case 'month':
            $i->delivery_date = date("Y-m-{$i->delivery_date_cycle}", strtotime('+1 month '. $i->cycle_date));            
            break;
          case 'week':
            $i->delivery_date = date('Y-m-d', strtotime(sprintf('+%s day', $i->delivery_week_cycle - date('w', strtotime($i->cycle_date)) + 7). $i->cycle_date));
            break;
        }
        $i->shipping_date = date("Y-m-d", strtotime(sprintf('-%s day', $i->shipping_date_cycle). $i->delivery_date));
        $i->settlement_date = date("Y-m-d", strtotime(sprintf('-%s day', $i->settlement_date_cycle). $i->shipping_date));
        $i->cancel_skip_date = date("Y-m-d", strtotime(sprintf('-%s day', $i->cancel_skip_date_cycle). $i->settlement_date));
        $i->by_tax_rate = json_decode($i->by_tax_rate);
        $i->option_include = json_decode($i->option_include);
        $i->campaign = json_decode($i->campaign);
        
        $sale = new repeatItemRepository;
        $sale->setSiteId($i->site_id);
        $sale->setRepeatProductId($i->repeat_product_id);
        $sale->setSalesKbn(1);
        $sale->setSalesDate($i->settlement_date);
        $sale->setLimit(1);
        $i->sale = $sale->get()->row[0];
        $this->row[] = $i;
      }
      $this->rowNumber = $stmt->rowCount();
      if($this->rowNumber > 0){
        $this->set_status(true);
      }
      $this->pageRange = $this->getPageRange();
    } catch (\Exception $e) {
      $this->set_message($e->getMessage());
    }
    return $this;
  }
  
  public function push(){
    $data = $this->getPost();
    if($id = $this->getId()){
      $data->data->id = $id;
    }
    if($site_id = $this->getSiteId()){
      $data->data->site_id = $site_id;
    }
    if($order_repeat_delivery_id = $this->getOrderRepeatDeliveryId()){
      $data->data->order_repeat_delivery_id = $order_repeat_delivery_id;
    }
    $judge = $this->judgeFilter($data);
    if($judge->_message || $judge->_invalid){
      return $judge;
    }

    $this->set_status(false);
    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($data->data, 'order_repeat_item_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $data['id'] ? $data['id'] : $this->lastInsertId();
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }

  public function judgeFilter(object $post) :object{

    $this->set_status(false);
    
    $ut = new utilityRepository;
    $site_id = $this->getSiteId();
    $repeat_product_id = $post->input->repeat_product_id;
    $repeat_product_item_id = $post->input->repeat_product_item_id;
    $charge_type = $this->getChargeType();//一般商品、リピート商品

    if(!$site_id || !$post->input->quantity){
      $this->set_message('数量がありません。');
      return $this;
    }
    if(!$repeat_product_id || !$repeat_product_item_id){
      $this->set_message('商品コードがありません。');
      return $this;
    }
    if($post->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
      return $this;
    }

    $product = new repeatRepository;
    $product->setSiteId($site_id);
    $product->setId($repeat_product_id);
    $product->setItemId($repeat_product_item_id);
    $product->setReleaseKbn(1);
    $product->setLimit(1);
    $p = $product->get()->row[0];
    if(!$p || !$p->item->id){
      $this->set_message('商品がありません。');
      return $this;
    }

    //定期商品の場合は1品のみ、さらに一般商品と定期商品の同時購入は不可
    if($charge_type == 'repeat'){
      $this->set_message('リピート商品のご注文は1点のみとなります。別々にご注文して下さい。');
      return $this;
    }
    
    if($charge_type && $charge_type != $key->charge_type){
      $this->set_message('一般商品とリピート商品との同時購入はできません。別々にご注文して下さい。');
      return $this;
    }

    //商品情報追加
    $p->index_product = $post->input->index_product;
    $p->index_delivery = $post->input->index_delivery;
    $p->quantity = $post->input->quantity;
    $p->price = ($p->unit_price + $field->unit_price) * $p->quantity;
    $p->tax_price = ($p->unit_tax_price + $field->unit_tax_price) * $p->quantity;
    $p->notax_price = ($p->unit_notax_price + $field->unit_notax_price) * $p->quantity;
    $p->tax = ($p->unit_tax + $field->unit_tax) * $p->quantity;
    $p->delivery_size = ($p->unit_delivery_size + $field->unit_delivery_size) * $p->quantity;
    
    //商品小計
    $price = new sumPriceRepository;
    $price->sumPrice($p->quantity,$p->unit_price,$p->unit_tax_price,$p->unit_notax_price,$p->unit_tax,$p->tax_rate,$p->unit_delivery_size);
    $price->sumPrice($p->quantity,$field->unit_price,$field->unit_tax_price,$field->unit_notax_price,$field->unit_tax,$p->tax_rate,$field->unit_delivery_size);
    
    //付属品
    $option_include = array();
    if($p->option_include && is_array($post->input->option_select)){
      foreach($post->input->option_select as $option_id => $class){
        if(!is_numeric($class) || $class === false){
          continue;
        }
        $option_key = array_search($option_id, array_column( $p->option_include, 'id'));
        if($option_key === false){
          continue;
        }
        $option = $p->option_include[$option_key];
        $class_key = array_search($class, array_column( $option->select_field, 'class'));
        if($class_key === false){
          continue;
        }

        $selected = $option->select_field[$class_key];
        $selected->tax_class = $option->select_field_tax_class;
        $selected->tax_rate = $option->select_field_tax_rate;
        $selected->price = $selected->unit_price * $p->quantity;
        $selected->tax_price = $selected->unit_tax_price * $p->quantity;
        $selected->notax_price = $selected->unit_notax_price * $p->quantity;
        $selected->tax = $selected->unit_tax * $p->quantity;
        $price->sumPrice($p->quantity,$selected->unit_price,$selected->unit_tax_price,$selected->unit_notax_price,$selected->unit_tax,$option->select_field_tax_rate);

        $input_value = $post->input->option_input[$option_id] ? $post->input->option_input[$option_id] : null;
        $input = new \StdClass;
        $input->name = $option->input_field_name;
        $input->value = $input_value;
        $input->tax_class = $option->input_field_tax_class;
        $input->tax_rate = $option->input_field_tax_rate;
        $input->unit_price = $option->input_field_unit_price;
        $input->unit_tax_price = $option->input_field_unit_tax_price;
        $input->unit_notax_price = $option->input_field_unit_notax_price;
        $input->unit_tax = $option->input_field_unit_tax;
        $input->price = $input_value ? $option->input_field_unit_price * $p->quantity : 0;
        $input->tax_price = $input_value ? $option->input_field_unit_tax_price * $p->quantity : 0;
        $input->notax_price = $input_value ? $option->input_field_unit_notax_price * $p->quantity : 0;
        $input->tax = $input_value ? $option->input_field_unit_tax * $p->quantity : 0;
        if($inpu_value){
          $price->sumPrice($p->quantity,$option->input_field_unit_price,$option->input_field_unit_tax_price,$option->input_field_unit_notax_price,$option->input_field_unit_tax,$option->input_field_tax_rate);
        }
        $option_include[] = (object) array(
          'id' => $option->id,
          'name' => $option->name,
          'quantity' => $p->quantity,
          'selected' => $selected,
          'input' => $input
        );
      }
    }

    //キャンペーンチェック適用
    $campaign = array();
    if($p->campaign && is_array($post->input->campaign_code)){
      $combined_use = true;//割引併用の判定
      foreach($post->input->campaign_code as $campaign_id => $code){
        if(!$code || !$combined_use){
          continue;
        }
        $campaign_key = array_search($campaign_id, array_column( $p->campaign, 'id'));
        if(!$p->campaign[$campaign_key]){
          continue;
        }
        if(!$p->combined_use){
          $combined_use = false;
        }
        $cam = $p->campaign[$campaign_key];
        if($cam->method == "couponCode" && $cam->coupon_code == $code){
          $campaign[] = (object) array(
            'id' => $cam->id,
            'name' => $cam->name,
            'value' => $code,
            'discount_unit_price' => $cam->discount_unit_price,
            'discount_unit_tax_price' => $cam->discount_unit_tax_price,
            'discount_unit_notax_price' => $cam->discount_unit_notax_price,
            'discount_unit_tax' => $cam->discount_unit_tax,
            'discount_price' => $cam->discount_unit_price * $p->quantity,
            'discount_tax_price' => $cam->discount_unit_tax_price * $p->quantity,
            'discount_notax_price' => $cam->discount_unit_notax_price * $p->quantity,
            'discount_tax' => $cam->discount_unit_tax * $p->quantity
          );
          $price->sumPrice($p->quantity,$cam->discount_unit_price,$cam->discount_unit_tax_price,$cam->discount_unit_notax_price,$cam->discount_unit_tax,$p->tax_rate, 0, true);
        }
      }
    }

    $image = null;
    if($p->files[0]){
      $image = $p->files[0]->url;
    }
    $array = (object) array(
      'site_id' => $p->site_id,
      'repeat_product_id' => $p->id,
      'repeat_product_item_id' => $p->item->id,
      'order_repeat_delivery_id' => $p->order_repeat_delivery_id,
      'index_delivery' => $p->index_delivery,
      'model' => $p->model,
      'name' => $p->name,
      'image' => $image,
      'temperature_zone' => $p->temperature_zone,
      'caution_include_value' => $p->caution_include_value,
      'quantity' => $p->quantity,
      'unit_price' => $p->unit_price,
      'unit_tax_price' => $p->unit_tax_price,
      'unit_notax_price' => $p->unit_notax_price,
      'unit_tax' => $p->unit_tax,
      'unit_name' => $p->unit_name,
      'tax_class' => $p->tax_class,
      'tax_rate' => $p->tax_rate,
      'unit_delivery_size' => $p->unit_delivery_size,
      'price' => $p->price,
      'tax_price' => $p->tax_price,
      'notax_price' => $p->notax_price,
      'delivery_size' => $p->delivery_size,
      'tax' => $p->tax,
      'field' => (object) array(
        'code' => $field->code,
        'name' => $field->name,
        'unit_price' => $field->unit_price,
        'unit_tax_price' => $field->unit_tax_price,
        'unit_notax_price' => $field->unit_notax_price,
        'unit_tax' => $field->unit_tax,
        'unit_delivery_size' => $field->unit_delivery_size
      ),
      'option_include' => $option_include,
      'campaign' => $campaign
    );
    $total = $price->getPrice();
    foreach($total as $column => $value){
      $array->{"total_".$column} = $value;
    }

    $this->item = $array;
    $this->charge_type = $key->charge_type;
    $this->set_status(true);
    return $this;
  }

  public function setTotalPrice(array $data) :object{
    $array = array(
      'price' => 0,
      'tax_price' => 0,
      'notax_price' => 0,
      'tax' => 0,
      'by_tax_rate' => array()
    );
    if($data){
      foreach($data as $p){
        $array['price'] += $p->total_price;
        $array['tax_price'] += $p->total_tax_price;
        $array['notax_price'] += $p->total_notax_price;
        $array['tax'] += $p->total_tax;
        foreach($p->total_by_tax_rate as $rate => $columns){
          foreach($columns as $key => $value){
            if(!$array['by_tax_rate'][$rate]){
              $array['by_tax_rate'][$rate] = new \StdClass;
            }
            $array['by_tax_rate'][$rate]->{$key} += $value;
          }
        }
      }
    }
    return (object) $array;
  }
}
// ?>