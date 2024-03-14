<?php 
namespace host\cms\entity;

use host\cms\repository\utilityRepository;

trait orderEntity{
  
  public $row;
  public $prefectures;
  public $member_value;
  public $status_read_value;
  public $corporation_kbn;
  public $honorific_title;
  public function __construct () {
    $ut = new utilityRepository;
    $this->prefectures = $ut->masterLoader('prefectures');
    $this->row = array();
    $this->member_value = array(
      (object) array(
        'name' => "ゲスト",
        'badge' => '<span class="badge badge-secondary">ゲスト</span>'
      ),
      (object) array(
        'name' => "会員",
        'badge' => '<span class="badge badge-primary">会員</span>'
      ),
    );
    $this->status_read_value = array(
      (object) array(
        'name' => "未読",
        'badge' => '<span class="badge badge-danger">NEW</span>'
      ),
      (object) array(
        'name' => "既読",
        'badge' => ""
      ),
    );
    $this->corporation_kbn = array(
      (object) array(
        'name' => "個人",
      ),
      (object) array(
        'name' => "法人",
      ),
    );
    $this->honorific_title = array(
      (object) array(
        'name' => "様",
      ),
      (object) array(
        'name' => "御中",
      ),
      (object) array(
        'name' => "殿",
      ),
      (object) array(
        'name' => "行",
      ),
      (object) array(
        'name' => "係",
      ),
      (object) array(
        'name' => "宛",
      ),
      (object) array(
        'name' => "先生",
      ),
    );
  }
  
  public $post;
  public function getPost(){
    return $this->post;
  }
  // @params array $post, $type null:全カラム or diff:差分
  public function setPost(array $post, $type = null) :void{

    //前提処理
    $exclusion = array('phone_number1', 'phone_number2', 'fax_number');
    foreach($post as $column => $value){
      if(is_array($value) && !in_array($column, $exclusion)){
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
      'account_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'member_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'seller_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'status_read' => [
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
      'honorific_title' => [
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
      'corporation_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'company_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'position_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'department_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'postal_code' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
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
      'email_address' => [
        'filter' => FILTER_VALIDATE_EMAIL,
        'options'=> array('default'=>null)
      ],
      'settlement_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'settlement_response_id' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'settlement_response' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'settlement_tax_class' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_tax_rate' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_tax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_notax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_tax' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_by_tax_rate' => [
        'options'=> array('default'=>null)
      ],
      'item_total_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'item_total_tax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'item_total_notax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'item_total_tax' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'item_total_by_tax_rate' => [
        'options'=> array('default'=>null)
      ],
      'total_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'total_tax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'total_notax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'total_tax' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'total_by_tax_rate' => [
        'options'=> array('default'=>null)
      ],
      'session_information' => [
        'options'=> array('default'=>null)
      ]
    ]);

    $terms = filter_var(@$post['terms'], FILTER_VALIDATE_INT, [
      'options'=> array('default'=>null)
    ]);
    
    $member_registration = filter_var(@$post['member_registration'], FILTER_VALIDATE_INT, [
      'options'=> array('default'=>null)
    ]);
    
    $_password = filter_var(@$post['_password'], FILTER_SANITIZE_SPECIAL_CHARS, [
      'options'=> array('default'=>null)
    ]);
    
    $member = filter_var_array($post, [
      'first_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'last_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'honorific_title' => [
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
      'corporation_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'company_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'position_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'department_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'postal_code' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
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
      'email_address' => [
        'filter' => FILTER_VALIDATE_EMAIL,
        'options'=> array('default'=>null)
      ],
      'mail_reject' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'password' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
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
        $diff_data = array();
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff_data[$key] = $value;
          }
        }
        $diff_member = array();
        foreach($member as $key => $value){
          if(isset($post[$key])){
            $diff_member[$key] = $value;
          }
        }
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $diff_data,
          'terms' => $terms,
          'member_registration' => $member_registration,
          '_password' => $_password,
          'member' => (object) $diff_member
        );
        break;
      default :
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $data,
          'terms' => $terms,
          'member_registration' => $member_registration,
          '_password' => $_password,
          'member' => (object) $member
        );
        break;
    }
  }

  public $id;
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
  
  public $account_id;
  public function getAccountId(){
    return $this->account_id;
  }
  public function setAccountId(int $account_id) :void{
    $this->account_id = $account_id;
  }
  
  public $member_id;
  public function getMemberId(){
    return $this->member_id;
  }
  public function setMemberId(int $member_id) :void{
    $this->member_id = $member_id;
  }
  
  public $seller_id;
  public function getSellerId(){
    return $this->seller_id;
  }
  public function setSellerId(int $seller_id) :void{
    $this->seller_id = $seller_id;
  }
  
  public $exclude;
  public function getExclude(){
    return $this->exclude;
  }
  public function setExclude(bool $exclude) :void{
    $this->exclude = $exclude;
  }
  
  public $member_registration;
  public function getMemberRegistration(){
    return $this->member_registration;
  }
  public function setMemberRegistration(bool $member_registration) :void{
    $this->member_registration = $member_registration;
  }
  
  public $session_information;
  public function getSessionInformation(){
    return $this->session_information;
  }
  public function setSessionInformation(string $session_information) :void{
    $this->session_information = $session_information;
  }

  
  
}

// ?>