<?php 
namespace host\cms\entity;

use host\cms\repository\utilityRepository;

trait productEntity{
  
  public $row;
  public $tax_rate;
  public $temperature_zone;
  public $caution_include_value;
  public function __construct () {
    $ut = new utilityRepository;
    $this->row = array();
    $this->tax_rate = $ut->masterLoader('tax_rate');
    $this->temperature_zone = $ut->masterLoader('temperature_zone');
    $this->caution_include_value = array(
      1 => (object) array(
        'name' => "同梱不可",
      ),
      2 => (object) array(
        'name' => "常温便との同梱不可",
      ),
      3 => (object) array(
        'name' => "冷蔵便との同梱不可",
      ),
      4 => (object) array(
        'name' => "冷凍便との同梱不可",
      )
    );
    $this->order_by_value = array(
      1 => (object) array(
        'name' => "価格（昇順）",
      ),
      2 => (object) array(
        'name' => "価格（降順）",
      ),
      3 => (object) array(
        'name' => "都道府県（昇順）",
      ),
      4 => (object) array(
        'name' => "都道府県（降順）",
      )
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
      'seller_id' => [
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
      'model' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'labels' => [
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
      'materials' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'expiry_date_text' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'preservation_method' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'exchanges' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'provide' => [
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
      'use_review' => [
        'filter' => FILTER_VALIDATE_INT,
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
    $files = filter_var_array($post, [
      'files_sort_id' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'delete_images' => [
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
        $diff_data = array();
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff_data[$key] = $value;
          }
        }
        $diff_category = array();
        foreach($category as $key => $value){
          if(isset($post[$key])){
            $diff_category[$key] = $value;
          }
        }
        $diff_files = array();
        foreach($files as $key => $value){
          if(isset($post[$key])){
            $diff_files[$key] = $value;
          }
        }
        $diff_field = array();
        foreach($field as $key => $value){
          if(isset($post[$key])){
            $diff_field[$key] = $value;
          }
        }
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $diff_data,
          'category' => (object) $diff_category,
          'files' => (object) $diff_files,
          'field' => (object) $diff_field
        );
        break;
      default :
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $data,
          'category' => (object) $category,
          'files' => (object) $files,
          'field' => (object) $field
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
  
  public $seller_id;
  public function getSellerId(){
    return $this->seller_id;
  }
  public function setSellerId(int $seller_id) :void{
    $this->seller_id = $seller_id;
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
  
  public $stock_status;
  public function getStockStatus(){
    return $this->stock_status;
  }
  public function setStockStatus(int $stock_status) :void{
    $this->stock_status = $stock_status;
  }
  
  public $order_by;
  public function getOrderBy(){
    return $this->order_by;
  }
  public function setOrderBy(int $order_by) :void{
    $this->order_by = $order_by;
  }
}

// ?>