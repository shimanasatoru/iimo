<?php 
namespace host\cms\entity;

trait campaignEntity{
  
  public $row;
  public $method;
  public function __construct () {
    $this->row = array();
    $this->method = (object) array(
      'couponCode' => (object) array(
        'name' => "クーポンコード入力で割引",
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
    $data = filter_var_array($post, [
      'token' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
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
      'usage_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'usage_start_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'usage_end_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'method' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'coupon_code' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'discount_number' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'discount_type' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'combined_use' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'explanatory_text' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
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
        $this->post = $diff;
        break;
      default :
        $this->post = $data;
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
  
  public $usage_kbn;
  public function getUsageKbn(){
    return $this->usage_kbn;
  }
  public function setUsageKbn(int $usage_kbn) :void{
    $this->usage_kbn = $usage_kbn;
  }
  
}

// ?>