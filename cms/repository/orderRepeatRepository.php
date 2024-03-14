<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\memberRepository;
use host\cms\repository\productRepository;
use host\cms\repository\orderRepeatDeliveryRepository;

class orderRepeatRepository extends dbRepository {
  
  use \host\cms\entity\orderRepeatEntity;
  
  /*
   * 注文単位
   * @return object array mixed $this
   */
  public function get() {
    //各種マスタは削除されていても必ず読み込む（過去の状況察知のため）
    self::setSelect("o.*, s.name as settlement_name, pref.name as prefecture_name");
    self::setFrom("order_repeat_tbl o 
        LEFT JOIN m_prefectures_tbl pref ON o.prefecture_id = pref.id 
        LEFT JOIN m_settlement_tbl s ON o.settlement_id = s.id ");
    self::setWhere("o.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("o.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("o.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($member_id = self::getMemberId()){
      self::setWhere("o.member_id = :member_id");
      self::setValue(":member_id", $member_id);
    }
    self::setOrder("o.created_date DESC");
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
      while($o = $stmt->fetch(\PDO::FETCH_OBJ)){
        $o->settlement_by_tax_rate = json_decode($o->settlement_by_tax_rate);
        $o->item_total_by_tax_rate = json_decode($o->item_total_by_tax_rate);
        $o->total_by_tax_rate = json_decode($o->total_by_tax_rate);
        //配送データ（商品）取得
        $d = new orderRepeatDeliveryRepository;
        $d->setSiteId($o->site_id);
        $d->setOrderRepeatId($o->id);
        $o->delivery = $d->get()->row;
        $this->row[] = $o;
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
      $query = $this->queryCreate($data->data, 'order_repeat_tbl');
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
    $ut = new utilityRepository;
    if(!$this->getSiteId()){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    if(!$post->data){
      $this->set_message('入力がありません。');
      return $this;
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
      $this->set_invalid('phone_number1', '必須となります。');
    }
    if(!$post->data->email_address){
      $this->set_message('メールアドレスをご確認下さい。');
      $this->set_invalid('email_address', '必須となります。');
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