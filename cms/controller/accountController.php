<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\navigationRepository;
use repository\accountRepository;
use Smarty;

class accountController{

  public function getAction(){
    $ac = new accountRepository;
    $thisUser = $_SESSION['user'];
    switch($thisUser->permissions){
      case 'administrator':
        break;
      case 'agent':
        $ac->setIdOrParentId($thisUser->id);
        $ac->setPermissionsArray('agent');
        $ac->setPermissionsArray('manager');
        break;
      case 'manager':
        $ac->setIdOrParentId($thisUser->id);
        $ac->setPermissionsArray('manager');
        $ac->setPermissionsArray('staff');
        break;
      case 'staff':
        $ac->setPermissionsArray('staff');
        break;
      default:
        exit;
    }

    if($keyword = filter_input(INPUT_GET, 'keyword')){
      $ac->setKeyword($keyword);
    }
    if($p = filter_input(INPUT_GET, 'p')){
      $ac->setPage($p);
    }
    $ac->setLimit(50);
    if($limit = filter_input(INPUT_GET, 'limit')){
      $ac->setLimit($limit);
    }
    $ac->setOrder("id DESC");
    $result = $ac->get();
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
    $tpl->display("account/index.tpl");
  }
  
  public function indexAddAction(){
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'data'  => $this->getAction()
    ]);
    $tpl->display("account/index_add.tpl");
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $id = filter_var(@$au->uriExplode()[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    
    $account = new accountRepository;
    $data = array();
    if($id){
      $account->setId($id);
      $account->setLimit(1);
      $account->get();
    }
    
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'permissions' => $account->permissions,
      'data' => @$account->row[0]
    ]);
    $tpl->display("account/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $sw = filter_var(@$au->uriExplode()[2], FILTER_SANITIZE_SPECIAL_CHARS);
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
          $account = new accountRepository;
          $ids = explode(',', $ids);
          foreach($ids as $rank => $id){
            $rank = $rank + ($page * $limit) + 1;
            $account->setPost([
              'token'=> $_POST['token'], 
              'id'=> $id, 
              'site_id'=> $_SESSION['site']->id, 
              'rank'=> $rank], 'diff');
            $result = $account->update();
            if(!$result->_status){
              break;
            }
          }
        }
        break;
      default:
        $account = new accountRepository;
        $account->setPost($_POST, 'diff');
        $result = $account->push();
        break;
    }
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  public function permissionAction(){
    $au = new \autoload;
    $account_id = filter_var(@$au->uriExplode()[2], FILTER_VALIDATE_INT);
    if(!$account_id || $_SESSION['user']->permissions != "administrator"){
      return false;
    }
    
    $ac = new accountRepository;
    $ac->setId($account_id);
    $ac->setLimit(1);
    $account = $ac->get()->row[0];
    if(!$account){
      return false;
    }

    $n = new navigationRepository;
    $n->setSiteId($_SESSION['site']->id);
    $navigation = $n->get();
    $site_key = array_search($navigation->site_id, $account->site_id);
    if(!$navigation->site_id || $site_key === false){
      return false;
    }
    $nta = new navigationRepository;
    $nta->setSiteId($_SESSION['site']->id);
    $nta->setAccountId($account_id);
    $permission = array();
    if($get_nta = $nta->getAccount()->row){
      foreach($get_nta as $row){
        $permission[] = $row->navigation_id;
      }
    }
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'account' => $account,
      'site_name' => $account->site_name[$site_key],
      'data'  => $navigation->row,
      'permission' => $permission
    ]);
    $tpl->display("account/permission.tpl");
  }
}

// ?>