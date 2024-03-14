<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\deliveryRepository;
use host\cms\repository\orderProductRepository;

class orderDeliveryRepository extends dbRepository {
  
  use \host\cms\entity\orderDeliveryEntity;
  
  public $time_zone_value;
  public function get() {
    //各種マスタは削除されていても必ず読み込む（過去の状況察知のため）
    self::setSelect("d.*, md.name as delivery_name, mt.time_zone as delivery_time_zone, pref.name as prefecture_name");
    self::setFrom("order_delivery_tbl d 
    LEFT JOIN m_prefectures_tbl pref ON d.prefecture_id = pref.id 
    LEFT JOIN m_delivery_tbl md ON d.delivery_id = md.id 
    LEFT JOIN m_delivery_datetime_tbl mdt ON d.site_id = mdt.site_id 
    LEFT JOIN m_delivery_time_tbl mt ON mdt.id = mt.delivery_datetime_id AND d.delivery_time = mt.time_kbn");
    self::setWhere("d.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("d.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("d.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($order_id = self::getOrderId()){
      self::setWhere("d.order_id = :order_id");
      self::setValue(":order_id", $order_id);
    }
    if($index_delivery = self::getIndexDelivery()){
      self::setWhere("d.index_delivery = :index_delivery");
      self::setValue(":index_delivery", $index_delivery);
    }
    self::setOrder("d.id ASC");
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
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->delivery_by_tax_rate = json_decode($d->delivery_by_tax_rate);
        $d->total_by_tax_rate = json_decode($d->total_by_tax_rate);
        
        $p = new orderItemRepository;
        $p->setSiteId($d->site_id);
        $p->setOrderDeliveryId($d->id);
        $d->item = $p->get()->row;
        $this->row[] = $d;
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
    if($order_id = $this->getOrderId()){
      $data->data->order_id = $order_id;
    }
    $judge = $this->judgeFilter($data);
    if($judge->_message || $judge->_invalid){
      return $judge;
    }

    $this->set_status(false);
    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($data->data, 'order_delivery_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $data->id ? $data->id : $this->lastInsertId();
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }
  
  //梱包＋配送料金の合計金額
  public function setPacking(array $data_product, array $data_delivery){
    $this->set_status(false);
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    if(!@count($data_product) || !@count($data_delivery)){
      $this->set_message('商品または、発送先を指定して下さい。');
      return $this;
    }
    $packing = array();
    foreach($data_product as $index_p => $p){
      $delivery_size = $p->delivery_size;
      $temperature_zone = $p->temperature_zone;
      $prefecture_id = $data_delivery[$p->index_delivery]->data->prefecture_id;
      $total = (object) array(
        'price' => $p->total->price,
        'tax_price' => $p->total->tax_price,
        'notax_price' => $p->total->notax_price,
        'tax' => $p->total->tax,
        'by_tax_rate' => array()
      );
      foreach($p->total_by_tax_rate as $rate => $value){
        $total->by_tax_rate[$rate] = (object) array(
          'notax_price' => $value->notax_price,
          'tax' => $value->tax
        );
      }

      if(!$p->delivery_id || !$prefecture_id){
        $this->set_message("「{$p->name}」は配送情報が設定されていません。(1)");
        return $this;
      }
      
      $m_delivery = new deliveryRepository;
      $m_delivery->setSiteId($site_id);
      $m_delivery->setId($p->delivery_id);
      $m_delivery->setLimit(1);
      $delivery = $m_delivery->get()->row[0];
      if(!$delivery){
        $this->set_message("「{$p->name}」は配送情報が設定されていません。(2)");
        return $this;
      }

      //現在のパッキングに同梱可能かを判定
      $bundle = true;
      if($p->caution_include_value && in_array(1, $p->caution_include_value, true)){
        //1=同梱不可,2=常温不可,3=冷蔵不可,4=冷凍不可
        $bundle = false;
      }

      $index_bundle = false;
      $index_items = array();
      if($packing[$p->index_delivery]){
        foreach($packing[$p->index_delivery] as $index_pack => $pack){
          
          //1=同梱不可,2=常温不可,3=冷蔵不可,4=冷凍不可
          if($p->caution_include_value){
            if($pack->temperature_zone == 2 && in_array(2, $p->caution_include_value, true)){
              continue;
            }
            if($pack->temperature_zone == 3 && in_array(3, $p->caution_include_value, true)){
              continue;
            }
            if($pack->temperature_zone == 4 && in_array(4, $p->caution_include_value, true)){
              continue;
            }
          }

          if($pack->delivery_id == $delivery->id && $pack->bundle && $bundle){
            if($pack->temperature_zone > $temperature_zone){
              $temperature_zone = $pack->temperature_zone;
            }
            $index_bundle = $index_pack;
            $index_items = $pack->index_items;
            $delivery_size += $pack->delivery_size;
            $total->price += $pack->total_price;
            $total->tax_price += $pack->total_tax_price;
            $total->notax_price += $pack->total_notax_price;
            $total->tax += $pack->total_tax;
            foreach($pack->total_by_tax_rate as $rate => $pacK_value){
              if(!$total->by_tax_rate[$rate]){
                $total->by_tax_rate[$rate] = new \StdClass;
              }
              $total->by_tax_rate[$rate]->notax_price += $pacK_value->notax_price;
              $total->by_tax_rate[$rate]->tax += $pacK_value->tax;
            }
            break;
          }
        }
      }
      
      foreach($delivery->size as $index_size => $size){
        if($size >= $delivery_size && $size){
          break;
        }
      }

      $price = (object) array(
        'delivery_price' => (int) $delivery->price[$prefecture_id][$index_size],
        'delivery_tax_price' => (int) $delivery->tax_price[$prefecture_id][$index_size],
        'delivery_notax_price' => (int) $delivery->notax_price[$prefecture_id][$index_size],
        'delivery_tax' => (int) $delivery->tax[$prefecture_id][$index_size],
        'delivery_by_tax_rate' => array(
          $delivery->tax_rate => (object) array(
            'notax_price' => (int) $notax_price,
            'tax' => (int) $tax
          )
        )
      );
      $total->price += $p->price;
      $total->tax_price += $p->tax_price;
      $total->notax_price += $p->prnotax_priceice;
      $total->tax += $p->tax;

      array_push($index_items, $index_p);
      $array = (object) array(
        'bundle' => $bundle,
        'temperature_zone' => $temperature_zone,
        'delivery_id' => $delivery->id,
        'delivery_tax_class' => $delivery->tax_class,
        'delivery_tax_rate' => $delivery->tax_rate,
        'index_items' => $index_items,
        'total_items' => (object) $total,
      );
      foreach($price as $column => $value){
        $array->{$column} = $value;
      }
      foreach($total as $column => $value){
        $array->{"total_".$column} = $value;
      }
      $packing[$p->index_delivery][$index_bundle] = $array;
    }
    $this->row = $packing;
    $this->set_status(true);
    return $this;
  }

  public function judgeFilter(object $post) :object{
    $ut = new utilityRepository;
    if(!$this->getSiteId()){
      $this->set_message('サイトが見つかりません。');
    }
    if(!$ut->mbStrLenCheck($post->data->first_name, 1, 120)){
      $this->set_message('姓をご確認下さい。');
      $this->set_invalid('first_name', '必須または120文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($post->data->last_name, 1, 120)){
      $this->set_message('名をご確認下さい。');
      $this->set_invalid('last_name', '必須または120文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($post->data->first_name_kana, 1, 120)){
      $this->set_message('姓（カナ）をご確認下さい。');
      $this->set_invalid('first_name_kana', '必須または120文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($post->data->last_name_kana, 1, 120)){
      $this->set_message('名（カナ）をご確認下さい。');
      $this->set_invalid('last_name_kana', '必須または120文字以内となります。');
    }
    //郵便番号と住所が合致するか検証がいるよね。
    if(!$post->data->postal_code){
      $this->set_message('郵便番号をご確認下さい。');
      $this->set_invalid('postal_code', '必須となります。');
    }
    if(!$post->data->prefecture_id){
      $this->set_message('都道府県をご確認下さい。');
      $this->set_invalid('prefecture_id', '必須となります。');
    }
    if(!$post->data->municipality){
      $this->set_message('市区町村をご確認下さい。');
      $this->set_invalid('municipality', '必須となります。');
    }
    if(!$post->data->address1){
      $this->set_message('番地をご確認下さい。');
      $this->set_invalid('address1', '必須となります。');
    }
    if(!$post->data->phone_number1){
      $this->set_message('電話番号をご確認下さい。');
      $this->set_invalid('phone_number', '必須となります。');
    }
    if($post->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if(!$this->_message && !$this->_invalid){
      $this->set_status(true);
    }else{
      $this->set_status(false);
    }
    return $this;
  }

}
// ?>