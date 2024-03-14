<?php 
namespace host\cms\entity;

trait repeatItemEntity{
  
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
      'repeat_product_id' => [
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
      'sales_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'sales_start_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'sales_end_date' => [
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
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
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
    
    switch($type){
      case 'diff'://引数があるものだけとする
        $diff = array(
          'token' => $token,
          'data' => array(),
          'files' => array()
        );
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff['data'][$key] = $value;
          }
        }
        foreach($files as $key => $value){
          if(isset($post[$key])){
            $diff['files'][$key] = $value;
          }
        }
        $this->post = $diff;
        break;
      default :
        $this->post = array(
          'token' => $token,
          'data' => $data,
          'files' => $files
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
  
  public $repeat_product_id;
  public function getRepeatProductId(){
    return $this->repeat_product_id;
  }
  public function setRepeatProductId(int $repeat_product_id) :void{
    $this->repeat_product_id = $repeat_product_id;
  }
  
  public $release_kbn;
  public function getReleaseKbn(){
    return $this->release_kbn;
  }
  public function setReleaseKbn(int $release_kbn) :void{
    $this->release_kbn = $release_kbn;
  }
  
  public $sales_kbn;
  public function getSalesKbn(){
    return $this->sales_kbn;
  }
  public function setSalesKbn(int $sales_kbn) :void{
    $this->sales_kbn = $sales_kbn;
  }
  
  public $sales_date;
  public function getSalesDate(){
    return $this->sales_date;
  }
  public function setSalesDate(string $sales_date) :void{
    $this->sales_date = $sales_date;
  }
}

// ?>