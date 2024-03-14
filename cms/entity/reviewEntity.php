<?php 
namespace host\cms\entity;

use host\cms\repository\utilityRepository;

trait reviewEntity{

  public function __construct () {
  }
  
  private $id;
  public function getId(){
    return $this->id;
  }
  public function setId(int $id) :void{
    $this->id = $id;
  }
  
  private $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  
  private $member_id;
  public function getMemberId(){
    return $this->member_id;
  }
  public function setMemberId(int $member_id) :void{
    $this->member_id = $member_id;
  }
  
  private $account_id;
  public function getAccountId(){
    return $this->account_id;
  }
  public function setAccountId(int $account_id) :void{
    $this->account_id = $account_id;
  }
  
  public $product_id;
  public function getProductId(){
    return $this->product_id;
  }
  public function setProductId(int $product_id) :void{
    $this->product_id = $product_id;
  }
  
  private $repeat_product_id;
  public function getRepeatProductId(){
    return $this->repeat_product_id;
  }
  public function setRepeatProductId(int $repeat_product_id) :void{
    $this->repeat_product_id = $repeat_product_id;
  }
  
  public function get() {
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    return $this;
  }
  
  private $post;
  public function getPost(){
    return $this->post;
  }
  public function setPost(array $post, $type = null) :void{
    
    $token = filter_var(@$post['token'], FILTER_SANITIZE_SPECIAL_CHARS, [
      'options'=> array('default'=>null)
    ]);
    
    $data = filter_var_array($post, [
      'id' => [
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
      'comment' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    switch($type){
      case 'diff':
        $diff = array();
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff[$key] = $value;
          }
        }
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $diff
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
}

// ?>