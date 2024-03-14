<?php 
namespace host\cms\repository;

use host\cms\repository\memberRepository;

class reviewRepository extends dbRepository {
  
  use \host\cms\entity\reviewEntity;
  
  public function get() {
    self::setSelect("re.*, m.first_name, m.last_name");
    self::setFrom("member_review_tbl re LEFT JOIN member_tbl m ON re.member_id = m.id");
    self::setWhere("re.delete_kbn IS NULL AND m.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("re.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("re.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($member_id = self::getMemberId()){
      self::setWhere("re.member_id = :member_id");
      self::setValue(":member_id", $member_id);
    }
    if($account_id = self::getAccountId()){
      self::setWhere("re.account_id = :account_id");
      self::setValue(":account_id", $account_id);
    }
    if($product_id = self::getProductId()){
      self::setWhere("re.product_id = :product_id");
      self::setValue(":product_id", $product_id);
    }
    if($repeat_product_id = self::getRepeatProductId()){
      self::setWhere("re.repeat_product_id = :repeat_product_id");
      self::setValue(":repeat_product_id", $repeat_product_id);
    }
    self::setOrder("re.created_date DESC");
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
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
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

  public function push() {
    
    $post = $this->getPost();
    $site_id = $this->getSiteId();
    if(!$this->getSiteId()){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    
    $m = new memberRepository;
    $m->setSiteId($site_id);
    $member = $m->getSession();
    $account = $m->getSession('account');
    if(!get_object_vars($member) || !$member->id){
      $this->set_message('ログイン情報が見つかりません。');
      return $this;
    }
    if(!$post->data->product_id && !$post->data->repeat_product_id){
      $this->set_message('商品コードがありません。');
      return $this;
    }
    if(!$post->data->comment && !$post->data->delete_kbn){
      $this->set_message('入力がありません。');
      return $this;
    }
    $ut = new utilityRepository;
    if(!$ut->validate_token($post->token) || $_SERVER['REQUEST_METHOD'] != 'POST'){
      $this->set_message('トークンが発行されませんでした、再度お試しください。');
      return $this;
    }

    $this->connect();
    $this->beginTransaction();
    try {
      $push = (object) array(
        'account_id' => $account->id,
        'member_id' => $member->id,
        'site_id' => $site_id,
      );
      foreach($post->data as $column => $value){
        $push->{$column} = $value;
      }
      $query = $this->queryCreate($push, 'member_review_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    $this->set_status(true);
    return $this;
  }
}
// ?>