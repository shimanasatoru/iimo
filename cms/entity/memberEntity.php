<?php 
namespace host\cms\entity;

use host\cms\repository\utilityRepository;

trait memberEntity{

  public $member;
  public function __construct () {
    $ut = new utilityRepository;
    $this->prefectures = $ut->masterLoader('prefectures');
    $this->member = new \StdClass;
    if(!@$_SESSION['member']){
      $_SESSION['member'] = $this->member;
    }
  }
  
  private $id;
  public function getId(){
    return $this->id;
  }
  public function setId(int $id) :void{
    $this->id = $id;
  }
  
  public $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  
  public $member_id;
  public function getMemberId(){
    return $this->member_id;
  }
  public function setMemberId(int $member_id) :void{
    $this->member_id = $member_id;
  }
  
  public $status_kbn;
  public function getStatusKbn(){
    return $this->status_kbn;
  }
  public function setStatusKbn(string $status_kbn) :void{
    $this->status_kbn = $status_kbn;
  }
  
  private $email_address;
  public function getEmailAddress(){
    return $this->email_address;
  }
  public function setEmailAddress(string $email_address) :void{
    $this->email_address = $email_address;
  }
  
  public $else_id;
  public function getElseId(){
    return $this->else_id;
  }
  public function setElseId(int $else_id) :void{
    $this->else_id = $else_id;
  }
  
  private $password;
  public function getPassword(){
    return $this->password;
  }
  public function setPassword(string $password) :void{
    $this->password = $password;
  }
  
  private $temporary_password;
  public function getTemporaryPassword(){
    return $this->temporary_password;
  }
  public function setTemporaryPassword(string $temporary_password) :void{
    $this->temporary_password = $temporary_password;
  }

  public $temporary_password_date;
  public function getTemporaryPasswordDate(){
    return $this->temporary_password_date;
  }
  public function setTemporaryPasswordDate(string $temporary_password_date) :void{
    $this->temporary_password_date = $temporary_password_date;
  }
  
  public $send_mail;
  public function getSendMail(){
    return $this->send_mail;
  }
  public function setSendMail(int $send_mail) :void{
    $this->send_mail = $send_mail;
  }
  
  //ログイン
  private $post_login;
  public function getPostLogin(){
    return $this->post_login;
  }
  public function setPostLogin(array $post, $type = null) :void{
    $data = filter_var_array($post, [
      'token' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'email_address' => [
        'filter' => FILTER_VALIDATE_EMAIL,
        'options'=> array('default'=>null)
      ],
      'password' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'longtime' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    switch($type){
      case 'diff'://引数があるものだけとする
        $diff = array();
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff[$key] = $value;
          }
        }
        $this->post_login = $diff;
        break;
      default :
        $this->post_login = $data;
        break;
    }
  }
  
  //会員登録
  private $post_member;
  public function getPostMember(){
    return $this->post_member;
  }
  // @params array $post, $type null:全カラム or diff:差分
  public function setPostMember(array $post, $type = null) :void{
    $data = filter_var_array($post, [
      'token' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'rank' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'status_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'first_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'last_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'first_name_kana' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'last_name_kana' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'postal_code' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-]/")
      ],
      'prefecture_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'municipality' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'address1' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'address2' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'phone_number1' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null, "regexp"=> "/[0-9]/")
      ],
      'phone_number2' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null, "regexp"=> "/[0-9]/")
      ],
      'fax_number' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null, "regexp"=> "/[0-9]/")
      ],
      'mail_reject' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'gender' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'birthday' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-]/")
      ],
      'email_address' => [
        'filter' => FILTER_VALIDATE_EMAIL,
        'options'=> array('default'=>null)
      ],
      'password' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      '_password' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'temporary_password' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    
    if($data['phone_number1']){
      $data['phone_number1'] = implode("-", $data['phone_number1']);
    }
    if($data['phone_number2']){
      $data['phone_number2'] = implode("-", $data['phone_number2']);
    }
    if($data['fax_number']){
      $data['fax_number'] = implode("-", $data['fax_number']);
    }

    switch($type){
      case 'diff'://引数があるものだけとする
        $diff = array();
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff[$key] = $value;
          }
        }
        $this->post_member = $diff;
        break;
      default :
        $this->post_member = $data;
        break;
    }
  }
}

// ?>