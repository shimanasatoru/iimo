<?php 
namespace host\cms\entity;

trait orderSettlementEntity{
  
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

    $token = filter_var(@$post['token'], FILTER_SANITIZE_SPECIAL_CHARS, [
      'options'=> array('default'=>null)
    ]);

    $data = filter_var_array($post, [
      'settlement_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_response_id' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'settlement_response' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'settlement_tax_class' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_tax_rate' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_tax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_notax_price' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_tax' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'settlement_by_tax_rate' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
    ]);

    $zeus_token_get = filter_var(@$post['zeus_token_get'], FILTER_VALIDATE_INT, [
      'options'=> array('default'=>null)
    ]);

    switch($type){
      case 'diff'://引数があるものだけとする
        $diff_data = array();
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff_data[$key] = $value;
          }
        }
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $diff_data,
          'zeus_token_get' => $zeus_token_get
        );
        break;
      default :
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $data,
          'zeus_token_get' => $zeus_token_get
        );
        break;
    }
  }

  public $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }

  
}

// ?>