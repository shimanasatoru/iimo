<?php 
namespace host\cms\entity;

trait mailTemplatesEntity{
  
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
        'options'=> array('default'=>0)
      ],
      'type' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'subject' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'template' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'from_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'from_mail' => [
        'filter' => FILTER_VALIDATE_EMAIL,
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
  
  public $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  
  public $order_id;
  public function getOrderId(){
    return $this->order_id;
  }
  public function setOrderId(int $order_id) :void{
    $this->order_id = $order_id;
  }
  
  public $type;
  public function getType(){
    return $this->type;
  }
  public function setType(string $type) :void{
    $this->type = $type;
  }
  
  public $type_is;
  public function getTypeIs(){
    return $this->type_is;
  }
  public function setTypeIs(int $type_is) :void{
    $this->type_is = $type_is;
  }

}

// ?>