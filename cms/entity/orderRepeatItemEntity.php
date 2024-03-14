<?php 
namespace host\cms\entity;

trait orderRepeatItemEntity{
  
  public $item;
  public $row;
  public function __construct () {
    $this->item = new \StdClass;
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
      'order_repeat_delivery_id' => [
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
      'repeat_product_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'repeat_product_item_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'rank' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'model' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'image' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'temperature_zone' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'tax_class' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'tax_rate' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'unit_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'unit_tax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'unit_notax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'unit_tax' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'quantity' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'tax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'notax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'tax' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'by_tax_rate' => [
        'options'=> array('default'=>null)
      ],
      'field' => [
        'options'=> array('default'=>null)
      ],
      'option_include' => [
        'options'=> array('default'=>null)
      ],
      'campaign' => [
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
      ]
    ]);
    
    $input = filter_var_array($post, [
      'index' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'repeat_product_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'repeat_product_item_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'index_delivery' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'quantity' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'option_select' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'option_input' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'campaign_code' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ]
    ]);
    
    if(!$input['index_delivery']){
      $input['index_delivery'] = 0;
    }

    switch($type){
      case 'diff'://引数があるものだけとする
        $diff_data = array();
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff_data[$key] = $value;
          }
        }
        $diff_input = array();
        foreach($input as $key => $value){
          if(isset($post[$key])){
            $diff_input[$key] = $value;
          }
        }
        $this->post = (object) array(
          'token' => $token,
          'data' =>  (object) $diff_data,
          'input' => (object) $diff_input
        );
        break;
      default:
        $this->post = (object) array(
          'token' => $token,
          'data' =>  (object) $data,
          'input' => (object) $input
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
  
  public $order_repeat_delivery_id;
  public function getOrderRepeatDeliveryId(){
    return $this->order_repeat_delivery_id;
  }
  public function setOrderRepeatDeliveryId(int $order_repeat_delivery_id) :void{
    $this->order_repeat_delivery_id = $order_repeat_delivery_id;
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
  
  public $repeat_id;
  public function getRepeatId(){
    return $this->repeat_id;
  }
  public function setRepeatId(int $repeat_id) :void{
    $this->repeat_id = $repeat_id;
  }

  public $charge_type;//一般商品、定期商品の判定
  public function getChargeType(){
    return $this->charge_type;
  }
  public function setChargeType($charge_type) :void{
    $this->charge_type = $charge_type;
  }
  
}

// ?>