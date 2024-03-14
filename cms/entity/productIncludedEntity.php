<?php 
namespace host\cms\entity;

trait productIncludedEntity{
  
  public $row;
  public function __construct () {
    $this->row = array();
    $this->method = (object) array(
      'default' => (object) array(
        'name' => "機能なし",
      ),
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
      'rank' => [
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
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'explanatory_text' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'select_field_tax_class' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'select_field_tax_rate' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'input_field_status' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'input_field_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'input_field_tax_class' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'input_field_tax_rate' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'input_field_unit_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'input_field_unit_tax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'input_field_unit_notax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'input_field_unit_tax' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    
    $field = filter_var_array($post, [
      'select_field_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'flags' => FILTER_FORCE_ARRAY, 
        'options'=> array('default'=>null)
      ],
      'select_field_unit_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags' => FILTER_FORCE_ARRAY, 
        'options'=> array('default'=>null)
      ],
      'select_field_unit_tax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags' => FILTER_FORCE_ARRAY, 
        'options'=> array('default'=>null)
      ],
      'select_field_unit_notax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags' => FILTER_FORCE_ARRAY, 
        'options'=> array('default'=>null)
      ],
      'select_field_unit_tax' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags' => FILTER_FORCE_ARRAY, 
        'options'=> array('default'=>null)
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
        $diff_field = array();
        foreach($field as $key => $value){
          if(isset($post[$key])){
            $diff_field[$key] = $value;
          }
        }
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $diff_data,
          'field' => (object) $diff_field
        );
        break;
      default :
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $data,
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
  
  public $ids;
  public function getIds(){
    return $this->ids;
  }
  public function setIds(array $ids) :void{
    $this->ids = $ids;
  }
  
  public $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  
  public $release_kbn;
  public function getReleaseKbn(){
    return $this->release_kbn;
  }
  public function setReleaseKbn(int $release_kbn) :void{
    $this->release_kbn = $release_kbn;
  }
}

// ?>