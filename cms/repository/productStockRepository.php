<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class productStockRepository extends dbRepository {
  
  use \host\cms\entity\productStockEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("product_stock_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($product_id = self::getProductId()){
      self::setWhere("product_id = :product_id");
      self::setValue(":product_id", $product_id);
    }
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
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $this->row[] = $d;
      }
      $this->rowNumber = $stmt->rowCount();
      if($this->rowNumber > 0){
        $this->set_status(true);
      }
      $stmt_allNumber = $this->prepare("SELECT FOUND_ROWS() as `allNumber`");
      $stmt_allNumber->execute();
      while($d = $stmt_allNumber->fetch(\PDO::FETCH_OBJ)){
        $this->totalNumber = $d->allNumber;
      }
      $this->pageRange = $this->getPageRange();
    } catch (\Exception $e) {
      $this->set_message($e->getMessage());
    }
    return $this;
  }

  public function push(){
    
    $post = $this->getPost();

    $ut = new utilityRepository;
    if(!$post->data->site_id){
      $this->set_message('サイトを選択してください。');
    }
    if(!$post->data->product_id){
      $this->set_message('商品番号を取得できません。');
    }
    if($post->data->code && !preg_match("/[0-9,]+$/", $post->data->code)){
      $this->set_message('商品コードを取得できません。');
    }
    if(!is_int($post->data->quantity)){
      $this->set_message('数量は数字となります。');
    }
    if(!is_int($post->data->fluctuating_quantity)){
      $this->set_message('数量は数字となります。');
    }
    if($post->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    
    $this->connect();
    $this->beginTransaction();
    try {
      //経過テーブル
      $query = $this->queryCreate($post->data, 'product_stock_bk');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $post->data->id ? $post->data->id : $this->lastInsertId();
      
      //正規テーブル
      $stmt = $this->prepare("
        INSERT INTO product_stock_tbl ( 
          site_id, product_id, code, quantity, fluctuating_quantity
        ) VALUES (
          :site_id, :product_id, :code, :quantity, :fluctuating_quantity
        ) ON DUPLICATE KEY UPDATE quantity = :quantity, fluctuating_quantity = :fluctuating_quantity, delete_kbn = :delete_kbn
      ");
      if(!$stmt->execute([
        ':site_id'    => $post->data->site_id,
        ':product_id' => $post->data->product_id,
        ':code'       => $post->data->code,
        ':quantity'   => $post->data->quantity,
        ':fluctuating_quantity'=> $post->data->fluctuating_quantity,
        ':delete_kbn' => null
      ])){
        $this->rollBack();
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
}
// ?>