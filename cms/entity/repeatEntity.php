<?php 
namespace host\cms\entity;

use host\cms\repository\utilityRepository;

trait repeatEntity{
  
  public $row;
  public $tax_rate;
  public $temperature_zone;
  public $caution_include_value;
  public function __construct () {
    $ut = new utilityRepository;
    $this->row = array();
    $this->tax_rate = $ut->masterLoader('tax_rate');
    $this->temperature_zone = $ut->masterLoader('temperature_zone');
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
    $this->cycle_unit = array(
      (object) array(
        'name' => "毎月",
        'value' => "month"
      ),
      (object) array(
        'name' => "毎週",
        'value' => "week"
      ),
    );
    $this->week = array(
      (object) array( 'name' => "日" ),
      (object) array( 'name' => "月" ),
      (object) array( 'name' => "火" ),
      (object) array( 'name' => "水" ),
      (object) array( 'name' => "木" ),
      (object) array( 'name' => "金" ),
      (object) array( 'name' => "土" ),
    );
  }
  
  public $post;
  public function getPost(){
    return $this->post;
  }
  // @params array $post, $type null:全カラム or diff:差分
  public function setPost(array $post, $type = null) :void{
    if(!isset($post['rank']) && (!isset($post['id']) || !$post['id'])){
      $post['rank'] = 0;//新規登録時限定::順位を設置する
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
      'release_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'release_start_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'release_end_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'rank' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'repeat_type' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'first_shipping_date_class' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'first_shipping_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'delivery_date_cycle_unit' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'delivery_date_cycle' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'delivery_week_cycle' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'shipping_date_cycle' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_date_cycle' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'cancel_skip_date_cycle' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'cycle_number_limit' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'model' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'overview' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'explanatory_text1' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'explanatory_text2' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
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
      'unit_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'delivery_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'unit_delivery_size' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'temperature_zone' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'stock_status' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'campaign' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'caution_include_value' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'option_include_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    $category = filter_var_array($post, [
      'category_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ]
    ]);
    $field = filter_var_array($post, [
      'field_use' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'field_type' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'field_title' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'field_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'field_total_stock' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'field_unit_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'field_unit_tax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'field_unit_notax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'field_unit_tax' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'field_unit_delivery_size' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ]
    ]);
    
    if(isset($data['labels'])){
      $data['labels'] = json_encode(explode('&#13;&#10;', $data['labels']), JSON_UNESCAPED_UNICODE);
    }

    switch($type){
      case 'diff'://引数があるものだけとする
        $diff = array(
          'token' => $token,
          'data' => array(),
          'category' => array(),
          'field' => array()
        );
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff['data'][$key] = $value;
          }
        }
        foreach($category as $key => $value){
          if(isset($post[$key])){
            $diff['category'][$key] = $value;
          }
        }
        foreach($field as $key => $value){
          if(isset($post[$key])){
            $diff['field'][$key] = $value;
          }
        }
        $this->post = $diff;
        break;
      default :
        $this->post = array(
          'token' => $token,
          'data' => $data,
          'category' => $category,
          'field' => $field
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
  
  public $member_id;
  public function getMemberId(){
    return $this->member_id;
  }
  public function setMemberId(int $member_id) :void{
    $this->member_id = $member_id;
  }

  public $category_id;
  public function getCategoryId(){
    return $this->category_id;
  }
  public function setCategoryId(int $category_id) :void{
    $this->category_id = $category_id;
  }
  
  public $release_kbn;
  public function getReleaseKbn(){
    return $this->release_kbn;
  }
  public function setReleaseKbn(int $release_kbn) :void{
    $this->release_kbn = $release_kbn;
  }
  
  public $item_id;
  public function getItemId(){
    return $this->item_id;
  }
  public function setItemId(int $item_id) :void{
    $this->item_id = $item_id;
  }

}

// ?>