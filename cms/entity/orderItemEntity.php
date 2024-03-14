<?php 
namespace host\cms\entity;

trait orderItemEntity{
  
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
      'order_delivery_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'rank' => [
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
      'product_id' => [
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
      'product_included_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'campaign_id' => [
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
      'stock_code' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'stock_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'remarks' => [
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
      'caution_include_value' => [
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
      'unit_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
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
      'id' => [
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
        'options'=> array('default'=>null)
      ],
      'option_input' => [
        'options'=> array('default'=>null)
      ],
      'campaign_code' => [
        'options'=> array('default'=>null)
      ],
      'field_code' => [
        'options'=> array('default'=>null)
      ],
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
  
  public $order_delivery_id;
  public function getOrderDeliveryId(){
    return $this->order_delivery_id;
  }
  public function setOrderDeliveryId(int $order_delivery_id) :void{
    $this->order_delivery_id = $order_delivery_id;
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
  
  public $product_id;
  public function getProductId(){
    return $this->product_id;
  }
  public function setProductId(int $product_id) :void{
    $this->product_id = $product_id;
  }
  
  public $charge_authority;//商品の出品権限
  public function getChargeAuthority(){
    return $this->charge_authority;
  }
  public function setChargeAuthority($charge_authority) :void{
    $this->charge_authority = $charge_authority;
  }
  
  public $charge_seller_id;
  public function getChargeSellerId(){
    return $this->charge_seller_id;
  }
  public function setChargeSellerId($charge_seller_id) :void{
    $this->charge_seller_id = $charge_seller_id;
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