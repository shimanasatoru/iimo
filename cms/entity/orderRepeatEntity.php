<?php 
namespace host\cms\entity;

use host\cms\repository\utilityRepository;

trait orderRepeatEntity{
  
  public $row;
  public $corporation_kbn;
  public $honorific_title;
  public function __construct () {
    $ut = new utilityRepository;
    $this->prefectures = $ut->masterLoader('prefectures');
    $this->row = array();
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
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'status_payment' => [
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

}

// ?>