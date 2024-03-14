<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class orderItemRepository extends dbRepository {
  
  use \host\cms\entity\orderItemEntity;
  
  public function get() {
    //各種マスタは削除されていても必ず読み込む（過去の状況察知のため）
    self::setSelect("*");
    self::setFrom("order_item_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($order_delivery_id = self::getOrderDeliveryId()){
      self::setWhere("order_delivery_id = :order_delivery_id");
      self::setValue(":order_delivery_id", $order_delivery_id);
    }
    self::setOrder("rank ASC");
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
      while($p = $stmt->fetch(\PDO::FETCH_OBJ)){
        $p->caution_include_value = json_decode($p->caution_include_value);
        $p->by_tax_rate = json_decode($p->by_tax_rate);
        $p->field = json_decode($p->field);
        $p->option_include = json_decode($p->option_include);
        $p->campaign = json_decode($p->campaign);
        $this->row[] = $p;
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
    $data->data->account_id = $this->getAccountId();
    if($id = $this->getId()){
      $data->data->id = $id;
    }
    if($site_id = $this->getSiteId()){
      $data->data->site_id = $site_id;
    }
    if($member_id = $this->getMemberId()){
      $data->data->member_id = $member_id;
    }
    $judge = $this->judgeFilter($data);
    if($judge->_message || $judge->_invalid){
      return $judge;
    }

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($data->data, 'order_item_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $data->data->id ? $data->data->id : $this->lastInsertId();
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
    $product_id = $post->input->id;
    $repeat_product_id = $post->input->repeat_product_id;
    $repeat_product_item_id = $post->input->repeat_product_item_id;
    $charge_authority = $this->getChargeAuthority();//運営、出品者
    $charge_seller_id = $this->getChargeSellerId();//出品者ID
    $charge_type = $this->getChargeType();//一般商品、リピート商品

    if(!$site_id || !$post->input->quantity){
      $this->set_message('数量がありません。');
      return $this;
    }
    if(!$product_id && (!$repeat_product_id || !$repeat_product_item_id)){
      $this->set_message('商品コードがありません。');
      return $this;
    }
    if($post->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
      return $this;
    }

    $key = (object) array(
      'charge_authority' => null,
      'charge_type' => null,
      'seller_id' => null,
      'product_id' => null,
      'repeat_product_id' => null,
      'repeat_product_item_id' => null
    );
    if($product_id){
      $product = new productRepository;
      $product->setSiteId($site_id);
      $product->setId($product_id);
      $product->setReleaseKbn(1);
      $product->setLimit(1);
      $p = $product->get()->row[0];
      if(!$p){
        $this->set_message('商品がありません。');
        return $this;
      }
      $key->charge_type = 'default';
      $key->product_id = $p->id;
    }
    if($repeat_product_id && $repeat_product_item_id){
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
      $key->charge_type = 'repeat';
      $key->repeat_product_id = $p->id;
      $key->repeat_product_item_id = $p->item->id;
    }
    if($p->seller_id){
      $key->charge_authority = 'seller';
      $key->seller_id = $p->seller_id;
    }else{
      $key->charge_authority = 'default';
    }

    //運営商品と出品者商品の同時購入は不可
    if($charge_authority && $charge_authority != $key->charge_authority){
      $this->set_message('一般商品と出品商品との同時購入はできません。別々にご注文して下さい。');
      return $this;
    }
    
    //出品者が違う場合も不可
    if($charge_seller_id && $charge_seller_id != $key->seller_id){
      $this->set_message('違う出品者との同時購入はできません。別々にご注文して下さい。');
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
    
    if($key->charge_type == 'default'){
      //フィールド
      $field_key = array_search($post->input->field_code, array_column( $p->fields_stock, 'code'));
      $field = $p->fields_stock[$field_key];
      if(!$field){
        $this->set_message('商品項目がありません。');
        return $this;
      }

      //在庫チェック
      if($p->stock_status && $field->quantity < $post->input->quantity){
        $this->set_message("在庫がありません。");
        return $this;
      }
    }

    //商品情報追加(商品価格＋フィールド価格)
    $p->index_product = $post->input->index_product;
    $p->index_delivery = $post->input->index_delivery;
    $p->quantity = $post->input->quantity;
    $p->price = ($p->unit_price + $field->unit_price) * $p->quantity;
    $p->tax_price = ($p->unit_tax_price + $field->unit_tax_price) * $p->quantity;
    $p->notax_price = ($p->unit_notax_price + $field->unit_notax_price) * $p->quantity;
    $p->tax = ($p->unit_tax + $field->unit_tax) * $p->quantity;
    $p->delivery_size = ($p->unit_delivery_size + $field->unit_delivery_size) * $p->quantity;
    
    //商品計
    $price = new sumPriceRepository;
    $price->sumPrice($p->quantity,$p->unit_price,$p->unit_tax_price,$p->unit_notax_price,$p->unit_tax,$p->tax_rate,$p->unit_delivery_size);
    $price->sumPrice($p->quantity,$field->unit_price,$field->unit_tax_price,$field->unit_notax_price,$field->unit_tax,$p->tax_rate,$field->unit_delivery_size);
    
    //付属品
    $option_include = array();
    $option_select = json_decode($post->input->option_select, true);
    if($p->option_include && is_array($option_select)){
      foreach($option_select as $option_id => $class){
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
        $selected->product_included_id = $option->id;
        $selected->tax_class = $option->select_field_tax_class;
        $selected->tax_rate = $option->select_field_tax_rate;
        $selected->price = $selected->unit_price * $p->quantity;
        $selected->tax_price = $selected->unit_tax_price * $p->quantity;
        $selected->notax_price = $selected->unit_notax_price * $p->quantity;
        $selected->tax = $selected->unit_tax * $p->quantity;
        $price->sumPrice($p->quantity,$selected->unit_price,$selected->unit_tax_price,$selected->unit_notax_price,$selected->unit_tax,$option->select_field_tax_rate);

        $input_value = $post->input->option_input[$option_id] ? $post->input->option_input[$option_id] : null;
        $input = new \StdClass;
        $input->product_included_id = $option->id;
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
    $campaign_code = json_decode($post->input->campaign_code, true);
    if($p->campaign && is_array($campaign_code)){
      $combined_use = true;//割引併用の判定
      foreach($campaign_code as $campaign_id => $code){
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
      'seller_id' => $key->seller_id,
      'product_id' => $key->product_id,
      'repeat_product_id' => $key->repeat_product_id,
      'repeat_product_item_id' => $key->repeat_product_item_id,
      'delivery_id' => $p->delivery_id,
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
    $this->charge_authority = $key->charge_authority;
    $this->charge_seller_id = $key->seller_id;
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