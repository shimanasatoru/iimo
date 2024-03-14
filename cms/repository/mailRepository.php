<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\mailSmtpRepository;

class mailRepository extends dbRepository{
  
  use \host\cms\entity\mailEntity;

  /*
   * 送信履歴の取得
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("mail_send_tbl");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($order_id = self::getOrderId()){
      self::setWhere("order_id = :order_id");
      self::setValue(":order_id", $order_id);
    }
    if($order_delivery_id = self::getOrderDeliveryId()){
      self::setWhere("order_delivery_id = :order_delivery_id");
      self::setValue(":order_delivery_id", $order_delivery_id);
    }
    if($member_id = self::getMemberId()){
      self::setWhere("member_id = :member_id");
      self::setValue(":member_id", $member_id);
    }
    self::setOrder("id DESC");
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
      $table = array(
        '&#13;&#10;'=> "\n",
        '&#10;'=> "\n"
      );
      $search = array_keys($table);
      $replace = array_values($table);
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->from_mail = json_decode($d->from_mail, true);
        $d->body = str_replace($search,$replace,$d->body);
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
    $push = $this->getPost();
    $token = $push->token;
    $data = $push->data;
    
    $ut = new utilityRepository;
    if(!$data->site_id){
      $this->set_message('サイトを選択してください。');
    }
    if(!$data->to_mail){
      $this->set_message('送信先がありません。');
    }
    if(!$data->from_mail){
      $this->set_message('送信元がありません。');
    }
    if(!$data->subject){
      $this->set_message('件名がありません。');
    }
    if(!$data->body){
      $this->set_message('本文がありません。');
    }
    if($token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($data, 'mail_send_tbl');
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

  /*
   * メール送信
   * @return object array mixed $this
   */
  public function sendSwift() :object{    
    
    $require_filename = SWIFT_DIR; //機能ファイル参照
    if(!file_exists($require_filename)){
      $this->set_message("swift機能が稼働していないため終了します。");
      return $this;
    }
    if(!$this->getSiteId()){
      $this->set_message('サイトがありません。');
      return $this;
    }
    
    $smtp = new mailSmtpRepository;
    $smtp->setSiteId($this->getSiteId());
    $smtp->get();
    if($smtp->rowNumber != 1){
      $this->set_message('メール設定（SMTP）がありません。');
    }
    $smtp = current($smtp->row);
    if(!$smtp->smtp_server_name || !$smtp->smtp_server_port || !$smtp->smtp_user_name || !$smtp->smtp_user_password){
      $this->set_message("メール設定情報がないため終了します。");
      return $this;
    }
    if(!$this->getToMail()){
      $this->set_message('宛先がありません。');
    }
    if(!$this->getFromMail()){
      $this->set_message('送り主がありません。');
    }
    if(!$this->getSubject()){
      $this->set_message('件名がありません。');
    }
    if(!$this->getBody()){
      $this->set_message('内容がありません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    // 送信設定
    require_once $require_filename;
    if($smtp->smtp_server_secure){
      $transport = new \Swift_SmtpTransport( $smtp->smtp_server_name, $smtp->smtp_server_port, $smtp->smtp_server_secure);
    }else{
      $transport = new \Swift_SmtpTransport( $smtp->smtp_server_name, $smtp->smtp_server_port);
    }
    $transport->setUsername($smtp->smtp_user_name);
    $transport->setPassword($smtp->smtp_user_password);
    if($smtp->smtp_auth_mode){
      $transport->setAuthMode($smtp->smtp_auth_mode);
    }
    if($smtp->smtp_options){
      $transport->setStreamOptions(json_decode($smtp->smtp_options, true));
    }
    $mailer = new \Swift_Mailer($transport);

    // メール作成
    $message = new \Swift_Message($this->getSubject());
    $message->setFrom($this->getFromMail());
    if($this->getReplyToMail()){
      $message->setReplyTo($this->getReplyToMail());//返信先
    }
    $message->setTo($this->getToMail());
    $message->setBody($this->getBody());
    try {
      if($result = $mailer->send($message, $e)){
        $this->set_status(true);
        return $this;
      }
    }
    catch (Swift_TransportException $e) {
      $this->set_message($e->getMessage());
      return $this;
    }
    return $this;
  }
}

// ?>