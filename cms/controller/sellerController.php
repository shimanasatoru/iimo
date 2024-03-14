<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\sellerRepository;
use Smarty;

class sellerController{

  public function getAction(){
    
    $user = $_SESSION['user'];
    $site = $_SESSION['site'];
    
    $seller = new sellerRepository;
    $seller->setSiteId($site->id);
    if($user->authcode != "manage"){
      $seller->setId($user->id);
    }
    if($keyword = filter_input(INPUT_GET, 'keyword')){
      $seller->setKeyword($keyword);
    }
    if($p = filter_input(INPUT_GET, 'p')){
      $seller->setPage($p);
    }
    $seller->setLimit(50);
    if($limit = filter_input(INPUT_GET, 'limit')){
      $seller->setLimit($limit);
    }
    $seller->setOrder("id DESC");
    $result = $seller->get();
    $dataType = filter_input(INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result);
      return true;
    }
    return $result;
  }
  
  public function indexAction(){
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    
    $user = $_SESSION['user'];
    if($user->authcode != "manage"){
      header("Location: ".ADDRESS_CMS."seller/edit/{$user->id}", true , 301);
      return;
    }
    
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'data'  => $this->getAction()
    ]);
    $tpl->display("seller/index.tpl");
  }

  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $id = filter_var(@$au->uriExplode()[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    
    $user = $_SESSION['user'];
    if($user->authcode != "manage"){
      if(!$id = $user->id){
        return false;//出品者は自身のみ編集可
      }
    }
    
    $data = array();
    if($id){
      $seller = new sellerRepository;
      $seller->setId($id);
      $seller->setLimit(1);
      $data = $seller->get()->row[0];
    }
    
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'prefecture' => $ut->masterLoader('prefectures'),
      'data' => $data
    ]);
    $tpl->display("seller/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $sw = filter_var(@$au->uriExplode()[2], FILTER_SANITIZE_SPECIAL_CHARS);
    
    $user = $_SESSION['user'];
    $site = $_SESSION['site'];
    if($user->authcode != "manage"){
      $_POST['id'] = $user->id;
      $sw = null;
    }
    
    $seller = new sellerRepository;
    switch($sw){
      case 'sort':
        $result = array('_status'=> false, '_message'=> array('IDが取得出来ません。'));
        $options = array(
          ['options'=> array('default'=>null, "regexp"=> "/[0-9\,]/")],
          ['options' => array('default' => 0)]
        );
        $ids = filter_input(INPUT_POST, 'ids', FILTER_VALIDATE_REGEXP, $options[0]);
        $page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT, $options[1]);
        $limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT, $options[1]);
        if($ids){
          $ids = explode(',', $ids);
          foreach($ids as $rank => $id){
            $rank = $rank + ($page * $limit) + 1;
            $seller->setPost([
              'token'=> $_POST['token'], 
              'id'=> $id, 
              'site_id'=> $site_id, 
              'rank'=> $rank], 'diff');
            $result = $seller->update();
            if(!$result->_status){
              break;
            }
          }
        }
        break;
      default:
        $_POST['site_id'] = $site->id;
        $seller->setPost($_POST, 'diff');
        $result = $seller->push();
        break;
    }
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }

}

// ?>