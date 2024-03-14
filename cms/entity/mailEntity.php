<?php 
namespace host\cms\entity;

trait mailEntity{
  
  public $row;
  public function __construct () {
    $this->row = array();
  }
  
  public $post;
  public function getPost(){
    return $this->post;
  }
  // @params array $post, $type null:全カラム or diff:差分
  public function setPost(array $post, $type = null) :void{
    
    //前提処理
    foreach($post as $column => $value){
      if(is_array($value)){
        $post[$column] = json_encode($value, JSON_UNESCAPED_UNICODE);
      }
    }

    $token = filter_var(@$post['token'], FILTER_SANITIZE_SPECIAL_CHARS, [
      'options'=> array('default'=>null)
    ]);

    $data = filter_var_array($post, [
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'site_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'order_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'order_delivery_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'broadcast_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'form_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'member_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'to_mail' => [
        'filter' => FILTER_VALIDATE_EMAIL,
        'options'=> array('default'=>null)
      ],
      'from_mail' => [
        'options'=> array('default'=>null)
      ],
      'subject' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'body' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'reservation' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'send_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ]
    ]);

    switch($type){
      case 'diff'://引数があるものだけとする
        $diff_data = array();
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff_data[$key] = $value;
          }
        }
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $diff_data
        );
        break;
      default :
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $data
        );
        break;
    }
  }

  public $id;
  public function setId(int $id) :void{
    $this->id = $id;
  }
  public function getId(){
    return $this->id;
  }
  
  public $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  
  public $order_id;
  public function getOrderId(){
    return $this->order_id;
  }
  public function setOrderId(int $order_id) :void{
    $this->order_id = $order_id;
  }
  
  public $order_delivery_id;
  public function getOrderDeliveryId(){
    return $this->order_delivery_id;
  }
  public function setOrderDeliveryId(int $order_delivery_id) :void{
    $this->order_delivery_id = $order_delivery_id;
  }
  
  public $member_id;
  public function getMemberId(){
    return $this->member_id;
  }
  public function setMemberId(int $member_id) :void{
    $this->member_id = $member_id;
  }

  public $type;
  public function getType(){
    return $this->type;
  }
  public function setType(string $type) :void{
    $this->type = $type;
  }
  
  public $account;
  public function setAccount(string $account) :void{
    $this->account = $account;
  }
  public function getAccount(){
    return $this->account;
  }
  
  public $reservation;
  public function setReservation(int $reservation) :void{
    $this->reservation = $reservation;
  }
  public function getReservation(){
    return $this->reservation;
  }
  
  public $to_mail;
  public function setToMail(array $to_mail) :void{
    $this->to_mail = $to_mail;
  }
  public function getToMail(){
    return $this->to_mail;
  }
  
  public $from_mail;
  public function setFromMail(array $from_mail) :void{
    $this->from_mail = $from_mail;
  }
  public function getFromMail(){
    return $this->from_mail;
  }
  
  public $replyto_mail;
  public function setReplyToMail(array $replyto_mail) :void{
    $this->replyto_mail = $replyto_mail;
  }
  public function getReplyToMail(){
    return $this->replyto_mail;
  }
  
  public $subject;
  public function setSubject(string $subject) :void{
    $this->subject = $subject;
  }
  public function getSubject(){
    return $this->subject;
  }
  
  public $body;
  public function setBody(string $body) :void{
    $this->body = $body;
  }
  public function getBody(){
    $table = array(
      '&#13;&#10;'=> "\n",
      '&#10;'=> "\n"
    );
    $search = array_keys($table);
    $replace = array_values($table);
    return str_replace($search,$replace,$this->body);
  }
  
  public $smtp_server_name;
  public function setServerName(string $smtp_server_name) :void{
    $this->smtp_server_name = $smtp_server_name;
  }
  public function getServerName(){
    return $this->smtp_server_name;
  }
  
  public $smtp_server_port;
  public function setServerPort(int $smtp_server_port) :void{
    $this->smtp_server_port = $smtp_server_port;
  }
  public function getServerPort(){
    return $this->smtp_server_port;
  }
  
  public $smtp_server_secure;
  public function setServerSecure(string $smtp_server_secure) :void{
    $this->smtp_server_secure = $smtp_server_secure;
  }
  public function getServerSecure(){
    return $this->smtp_server_secure;
  }
  
  public $smtp_user_name;
  public function setUserName(string $smtp_user_name) :void{
    $this->smtp_user_name = $smtp_user_name;
  }
  public function getUserName(){
    return $this->smtp_user_name;
  }
  
  public $smtp_user_password;
  public function setUserPassword(string $smtp_user_password) :void{
    $this->smtp_user_password = $smtp_user_password;
  }
  public function getUserPassword(){
    return $this->smtp_user_password;
  }
  
  public $smtp_auth_mode;
  public function setAuthMode(string $smtp_auth_mode) :void{
    $this->smtp_auth_mode = $smtp_auth_mode;
  }
  public function getAuthMode(){
    return $this->smtp_auth_mode;
  }

  public $smtp_options;
  public function setOptions(array $smtp_options) :void{
    $this->smtp_options = $smtp_options;
  }
  public function getOptions(){
    return $this->smtp_options;
  }
  
  public $keyword = array();
  public function setKeyword(string $keyword) :void{
    $keyword = mb_convert_kana($keyword, 's');
    $ary_keyword = preg_split('/[\s]+/', $keyword, -1, PREG_SPLIT_NO_EMPTY);
    $this->keyword = $ary_keyword;
  }
  public function getKeyword() : array{
    return $this->keyword;
  }
  
}

// ?>