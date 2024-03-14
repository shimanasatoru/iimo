<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\productIncludedRepository;
use host\cms\repository\productStockRepository;
use host\cms\repository\campaignRepository;

class productRepository extends dbRepository {
  
  use \host\cms\entity\productEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setWhere("p.delete_kbn IS NULL AND s.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("p.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("p.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($seller_id = self::getSellerId()){
      self::setWhere("p.seller_id = :seller_id");
      self::setValue(":seller_id", $seller_id);
    }
    $join_category = null;
    if($category_id = self::getCategoryId()){
      $join_category = "
        LEFT JOIN ( 
          SELECT 
            product_id,
            1 AS bool
          FROM product_category_at_tbl at 
          LEFT JOIN product_category_tbl ca ON at.category_id = ca.id 
          WHERE at.category_id = :category_id AND at.delete_kbn IS NULL AND ca.delete_kbn IS NULL AND ca.release_kbn = 1 
          GROUP BY product_id 
        ) category ON category.product_id = p.id
      ";
      self::setWhere("category.bool = :category_bool");
      self::setValue(":category_id", $category_id);
      self::setValue(":category_bool", 1);
    }
    if($stock_status = self::getStockStatus()){
      self::setWhere("p.stock_status = :stock_status");
      self::setValue(":stock_status", $stock_status);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("p.release_kbn = :release_kbn AND (p.release_start_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') >= DATE_FORMAT(p.release_start_date, '%Y-%m-%d')) AND (p.release_end_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') <= DATE_FORMAT(p.release_end_date, '%Y-%m-%d'))");
      self::setValue(":release_kbn", $release_kbn);
    }
    self::setOrder("p.rank ASC");
    if($order_by = self::getOrderBy()){
      switch($order_by){
        case 1: self::setOrder("p.unit_tax_price ASC");
          break;
        case 2: self::setOrder("p.unit_tax_price DESC");
          break;
        case 3: self::setOrder("se.prefecture_id ASC");
          break;
        case 4: self::setOrder("se.prefecture_id DESC");
          break;
      }
    }
    self::setSelect("
      p.*, 
      s.domain, 
      s.directory, 
      se.store_name as seller_store_name,
      sePref.name as seller_prefecture_name,
      u.name as unit_name,
      d.name as delivery_name,
      t.code as temperature_zone_code,
      t.name as temperature_zone_name,
      t.badge as temperature_zone_badge
    ");
    self::setFrom("
      product_tbl p 
      LEFT JOIN site_tbl s ON p.site_id = s.id 
      LEFT JOIN seller_tbl se ON se.id = p.seller_id 
      LEFT JOIN m_prefectures_tbl sePref ON sePref.id = se.prefecture_id  
      LEFT JOIN m_unit_tbl u ON p.unit_id = u.id 
      LEFT JOIN m_temperature_zone_tbl t ON p.temperature_zone = t.id
      LEFT JOIN m_delivery_tbl d ON p.delivery_id = d.id 
      {$join_category}
      ");
    $q = self::getSelect()
        .self::getFrom()
        .self::getWhere()
        .self::getGroupBy()
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

      $today = date("Y-m-d");
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){

        $d->release = 0;
        if($d->release_kbn == 1 
           && (!$d->release_start_date || strtotime($today) >= strtotime($d->release_start_date)) 
           && (!$d->release_end_date || strtotime($today) <= strtotime($d->release_end_date))){
          $d->release = 1;
        }elseif($d->release_kbn == 2){
          $d->release = 2;
        }

        $category_stmt = self::prepare('
          SELECT * FROM product_category_at_tbl a 
          LEFT JOIN product_category_tbl c ON a.category_id = c.id 
          WHERE a.product_id = :id AND a.delete_kbn IS NULL ORDER BY a.rank ASC
        ');
        $category_stmt->execute([':id' => $d->id]);
        $d->category = array();
        while($c = $category_stmt->fetch(\PDO::FETCH_OBJ)){
          $d->category[] = $c;
        }
        
        //フィールド・在庫
        $stock = new productStockRepository;
        $stock->setSiteId($d->site_id);
        $stock->setProductId($d->id);
        $d->fields = (object) json_decode($d->fields, true);
        $d->fields_stock = $this->createStockArray($d->fields, $stock->get()->row);

        $files_stmt = self::prepare('
          SELECT * FROM product_files_tbl 
          WHERE product_id = :id AND delete_kbn IS NULL ORDER BY rank ASC
        ');
        $files_stmt->execute([':id' => $d->id]);
        $d->files = array();
        while($f = $files_stmt->fetch(\PDO::FETCH_OBJ)){
          $f->url = null;
          if($d->directory && $f->name){
            $f->url = ($d->domain ? $d->domain : ADDRESS_SITE."/".$d->directory)."/datas/product/{$d->id}/{$f->name}";
          }
          $d->files[] = $f;
        }
        
        $d->option_include_id = json_decode($d->option_include_id, true);
        $d->option_include = array();
        if($d->option_include_id){
          $pi = new productIncludedRepository;
          $pi->setSiteId($d->site_id);
          $pi->setIds($d->option_include_id);
          $pi->setReleaseKbn(1);
          $d->option_include = $pi->get()->row;
        }

        $d->campaign_id = json_decode($d->campaign, true);
        $d->campaign = array();
        if($d->campaign_id){
          $ca = new campaignRepository;
          $ca->setSiteId($d->site_id);
          $ca->setIds($d->campaign_id);
          $ca->setUsageKbn(1);
          $campaign = $ca->get()->row;
          if($campaign){
            foreach($campaign as $c){
              $discount = $this->calcDiscountPrice(
                $d->tax_class, 
                $d->tax_rate, 
                $c->discount_type, 
                $c->discount_number, 
                $d->unit_price
              );
              $c->discount_unit_price = $discount['price'];
              $c->discount_unit_tax_price = $discount['tax_price'];
              $c->discount_unit_notax_price = $discount['notax_price'];
              $c->discount_unit_tax = $discount['tax'];
              $d->campaign[] = $c;
            }
          }
        }
        
        $d->caution_include_value = json_decode($d->caution_include_value, true);
        
        
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
  
  /*
   * ディスカウント価格を生成
   * @param $tax_class, $tax_rate, $d_type, $d_number, $price
   * @return (array) $price, $tax_price, $notax_price, $tax
   */
  public function calcDiscountPrice(int $tax_class,int $tax_rate,string $d_type,int $d_number,int $price){
    list($discount_price, $tax_price, $notax_price, $tax) = [0,0,0,0];
    if($tax_class >= 0 && $tax_rate >= 0 && $d_type && $d_number >= 0 && $price >= 0){
      switch($d_type){
        case "rate":
          $discount_price = round($price * ($d_number / 100));
          break;
        case "yen":
          $discount_price = $price - $d_number;
          break;
      }
      $tax_price = $notax_price = $discount_price;
      if($tax_class == 1){
        /*外税・税別の場合は、税込み価格を計算（四捨五入）*/
        $tax = round($discount_price * ($tax_rate / 100));
        $tax_price = $discount_price + $tax;
      }else{
        /*内税・税込の場合は、税抜き価格を計算（四捨五入）*/
        $tax = round($discount_price * ($tax_rate / 100) / (($tax_rate + 100) / 100));
        $notax_price = $discount_price - $tax;
      }
    }
    return array(
      'price' => $discount_price,
      'tax_price' => $tax_price,
      'notax_price' => $notax_price,
      'tax' => $tax,
    );
  }
  
  /*
   * 在庫表を生成
   * @param $fields, $key = 1, $code = "", $name = ""
   * @return (array) $code, $name
   */
  public function createStockArray($fields, $stock, $key = 1, $code = "", $name = "", $unit_price = 0, $unit_tax_price = 0, $unit_notax_price = 0, $unit_tax = 0, $unit_delivery_size = 0){
    if(!$fields->field_name[$key]){
      $return = array(
        'code' => null,
        'name' => null,
        'unit_price' => 0,
        'unit_tax_price' => 0,
        'unit_notax_price' => 0,
        'unit_delivery_size' => 0,
        'unit_tax' => 0,
        'quantity' => 0,
        'fluctuating_quantity' => 0,
      );
      $stock_key = array_search( null, array_column($stock, 'code'));
      if($stock_key !== false){
        $return['quantity'] = $stock[$stock_key]->quantity;
        $return['fluctuating_quantity'] = $stock[$stock_key]->fluctuating_quantity;
      }
      return array(
        (object) $return
      );
    }
    
    $array = array();
    foreach($fields->field_name[$key] as $_code => $_name){
      if(!$_name){
        continue;
      }
      
      $_key = $key + 1;
      $_code = ($code === "" ? "" : $code.",") . "{$_code}";
      $_name = ($name === "" ? "" : $name.",") . "{$_name}";
      $_unit_price = $unit_price + $fields->field_unit_price[$key][$_code];
      $_unit_tax_price = $unit_tax_price + $fields->field_unit_tax_price[$key][$_code];
      $_unit_notax_price = $unit_notax_price + $fields->field_unit_notax_price[$key][$_code];
      $_unit_tax = $unit_tax + $fields->field_unit_tax[$key][$_code];
      $_unit_delivery_size = $unit_delivery_size + $fields->field_unit_delivery_size[$key][$_code];
      $quantity = 0;
      $fluctuating_quantity = 0;
      
      $stock_key = array_search($_code, array_column($stock, 'code'));
      if($stock_key !== false){
        $quantity = $stock[$stock_key]->quantity;
        $fluctuating_quantity = $stock[$stock_key]->fluctuating_quantity;
      }
      
      if(!$fields->field_name[$_key]){
        $array[] = (object) array(
          'code' => $_code,
          'name' => $_name,
          'unit_price' => $_unit_price,
          'unit_tax_price' => $_unit_tax_price,
          'unit_notax_price' => $_unit_notax_price,
          'unit_delivery_size' => $_unit_delivery_size,
          'unit_tax' => $_unit_tax,
          'quantity' => $quantity,
          'fluctuating_quantity' => $fluctuating_quantity
        );
      }
      if($fields->field_name[$_key]){
        $array = array_merge($array, $this->createStockArray($fields, $stock, $_key, $_code, $_name, $_unit_price, $_unit_tax_price, $_unit_notax_price, $_unit_tax, $_unit_delivery_size));
      }
    }
    return $array;
  }


  public function push(){
    $push = $this->getPost();
    $token = $push->token;
    $data = $push->data;
    $category = $push->category;
    $files = $push->files;
    $field = $push->field;
    
    $ut = new utilityRepository;
    if(!$data->site_id){
      $this->set_message('サイトを選択してください。');
    }
    if(!$ut->mbStrLenCheck($data->name, 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
    }
    if(!is_int($data->unit_price)){
      $this->set_invalid('unit_price', '必須となります。');
    }
    if(!is_int($data->unit_id)){
      $this->set_invalid('unit_id', '必須となります。');
    }
    if(!is_int($data->delivery_id)){
      $this->set_invalid('delivery_id', '必須となります。');
    }
    if($token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $data->fields = json_encode($field, JSON_UNESCAPED_UNICODE);
    $data->campaign = json_encode($data->campaign, JSON_UNESCAPED_UNICODE);
    $data->option_include_id = json_encode($data->option_include_id, JSON_UNESCAPED_UNICODE);
    $data->caution_include_value = json_encode($data->caution_include_value, JSON_UNESCAPED_UNICODE);

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($data, 'product_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $data->id ? $data->id : $this->lastInsertId();
      if(!$data->id && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("
          UPDATE product_tbl SET rank = rank + 1 
          WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL
        ");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $data->site_id
        ])){
          $this->rollBack();
        }
        //カテゴリ(更新で一旦削除して再度更新)
        $stmt = $this->prepare("
          UPDATE product_category_at_tbl SET delete_kbn = 1 
          WHERE site_id = :site_id AND product_id = :product_id
        ");
        if(!$stmt->execute([
          ':product_id'=> $this->_lastId, 
          ':site_id'=> $data->site_id
        ])){
          $this->rollBack();
        }
      }

      if(isset($category->category_id) && $category->category_id !== "false"){
        foreach($category->category_id as $rank => $category_id){
          $stmt = $this->prepare("
            INSERT INTO product_category_at_tbl ( 
              site_id, product_id, rank, category_id
            ) VALUES (
              :site_id, :product_id, :rank, :category_id
            ) ON DUPLICATE KEY UPDATE category_id = :category_id,  delete_kbn = :delete_kbn
          ");
          if(!$stmt->execute([
            ':site_id'    => $data->site_id,
            ':product_id' => $this->_lastId,
            ':rank'       => $rank,
            ':category_id'=> $category_id,
            ':delete_kbn' => null
          ])){
            $this->rollBack();
          }
        }        
      }

      //ディレクトリ生成・QRコード生成
      $dirPath = DIR_SITE.$_SESSION['site']->directory.'/datas/product/'.$this->_lastId;
      if($ut->createDir($dirPath) != true){
        $this->set_message("ディレクトリ({$dirPath})生成が出来ません。権限を確認してください。");
        return $this;
      }

      //データ保存処理 並び替え含む
      if(isset($files->files_sort_id) && $files->files_sort_id !== "false"){
        $files_sort_id = explode(',', $files->files_sort_id);
        foreach($files_sort_id as $rank=>$id){ //IDがあったら更新、無ければ新規
          if($id && !ctype_digit($id)){
            $this->set_message($id.': ファイル生成時にエラーが発生しました。');
            continue;
          }

          //画像ファイル生成
          $file = array('name'=> null, 'tmp_name'=> null, 'size'=> null, 'mime'=> null, 'error'=> null);
          if(!$id && isset($_FILES['images']['name'][$rank])){
            $file = array(
              'name'=> $_FILES['images']['name'][$rank],
              'tmp_name'=> $_FILES['images']['tmp_name'][$rank],
              'size'=> $_FILES['images']['size'][$rank],
              'mime'=> $_FILES['images']['type'][$rank],
              'error'=> $_FILES['images']['error'][$rank]
            );
            $newName = pathinfo($file['name'], PATHINFO_FILENAME);
            $file['name'] = $ut->uploadedFile($file['name'], $file['tmp_name'], $file['error'],  $dirPath, $newName);
            if(!$file['name']){
              $this->set_message('ファイル名を生成できません。');
              return $this;
            }
          }
          $query = 'INSERT INTO product_files_tbl ( 
                      id, product_id, rank, use_name, name, size, mime
                    ) VALUES (
                      :id, :product_id, :rank, :use_name, :name, :size, :mime
                    ) ON DUPLICATE KEY UPDATE rank = :rank, delete_kbn = :delete_kbn';
          $params = [
            ':id'         => $id ? $id : null,
            ':product_id' => $this->_lastId,
            ':rank'       => $rank,
            ':use_name'   => 'image',
            ':name'       => $file['name'],
            ':size'       => $file['size'],
            ':mime'       => $file['mime'],
            ':delete_kbn' => null
          ];
          $stmt = $this->prepare($query);
          if(!$stmt->execute($params)){
            $this->rollBack();
          }
        }
      }
      //データ削除処理
      if($files->delete_images){
        foreach ($files->delete_images as $key => $id) {
          $delete = [
            'id' => $id,
            'delete_kbn' => 1
          ];
          $query = $this->queryCreate($delete, 'product_files_tbl');
          $stmt = $this->prepare($query['query']);
          if(!$stmt->execute($query['params'])){
            $this->rollBack();
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
    return $this;
  }
  
  public function update(){
    $push = $this->getPost();
    $ut   = new utilityRepository;
    if(!$push->data->id){
      $this->set_message('IDが取得できません。');
    }
    if(!$push->data->site_id){
      $this->set_message('サイトを選択してください。');
    }
    if($push->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push->data, 'product_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push->data->id ? $push->data->id : $this->lastInsertId();
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }
}
// ?>