<?php 
namespace host\cms\entity;

trait productCategoryEntity{
  
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
      'parent_id' => [
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
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'release_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
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
  
  public $domain;
  public function getDomain(){
    return $this->domain;
  }
  public function setDomain(string $domain) :void{
    $this->domain = $domain;
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