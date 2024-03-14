<?php 
namespace controller;

use host\cms\repository\memberRepository;
use host\cms\repository\reviewRepository;
use Smarty;

class reviewClass{
  
  private $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  
  public function getFunction(){
    $params = filter_input_array(INPUT_POST, [
      'function' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ]
    ]);
    if(@$params['function']){
      return $params['function'];
    }
    return null;
  }
  
  public function getDataType(){
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ]
    ]);
    if(@$params['dataType']){
      return $params['dataType'];
    }
    return null;
  }

  public function load(){
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    $m = new memberRepository;
    $m->setSiteId($site_id);
    $member = $m->getSession();
    $function = $this->getFunction();
    
    $r = new reviewRepository;
    $r->setSiteId($site_id);
    if($member->id){
      $r->setMemberId($member->id);
    }
    switch($function){
      case 'additionReview':
        $r->setPost($_POST);
        $r->push();
        if($r->_status){
          $param = $_SERVER['QUERY_STRING'] ? "&".$_SERVER['QUERY_STRING'] : null;
          header('Location: ?thanks'.$param, false);
        }
        break;
    }
    if($this->getDataType() == 'json'){
      echo json_encode( $r->get(), JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $r->get();
  }
  
  public function view($params){
    $params = filter_var_array($params, [
      'id' => [//レビューID
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'pid' => [//商品ID
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'rid' => [//リピートID
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'mid' => [//会員ID
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'p' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'limit' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    $re = new reviewRepository;
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    $re->setSiteId($site_id);
    if(@$params['id']){
      $re->setId($params['id']);
    }
    if(@$params['pid']){
      $re->setProductId($params['pid']);
    }
    if(@$params['rid']){
      $re->setRpeatProductId($params['rid']);
    }
    if(@$params['mid']){
      $re->setMemberId($params['mid']);
    }
    if(@$params['p']){
      $re->setPage($params['p']);
    }
    $re->setLimit(20);
    if(@$params['limit']){
      $re->setLimit($params['limit']);
    }
    return $re->get();
  }

  
}

// ?>