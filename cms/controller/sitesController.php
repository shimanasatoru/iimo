<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\sitesRepository;
use host\cms\repository\pageSettingRepository;
use Smarty;

class sitesController{
  
  public function getAction(){
    $si = new sitesRepository;
    $thisUser = $_SESSION['user'];
    switch($thisUser->permissions){
      case 'administrator':
        break;
      case 'agent':
        $si->setAgentId($thisUser->id);
        break;
      case 'manager':
        $si->setAccountId($thisUser->id);
        break;
      case 'staff':
        $si->setAccountId($thisUser->id);
        break;
      default:
        exit;
    }
    if($id = filter_input(INPUT_GET, 'id')){
      $si->setId($id);
    }
    if($keyword = filter_input(INPUT_GET, 'keyword')){
      $si->setKeyword($keyword);
    }
    if($p = filter_input(INPUT_GET, 'p')){
      $si->setPage($p);
    }
    $si->setLimit(50);
    if($limit = filter_input(INPUT_GET, 'limit')){
      $si->setLimit($limit);
    }
    $si->setOrder("id DESC");
    $result = $si->get();
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
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'data'  => $this->getAction()
    ]);
    $tpl->display("site/index.tpl");
  }
  
  public function loginAction(){
    $site_id = filter_input(INPUT_GET, 'site_id', FILTER_VALIDATE_INT); 
    $site = (object) array( 'id' => null );
    $page_setting = new \stdClass;

    if($site_id){
      $si = new sitesRepository;
      $si->setId($site_id);
      $si->setLimit(1);
      $site = $si->get()->row[0];
      if(!$site){
        print "サイトがありません。";
        return false;
      }
      $ps = new pageSettingRepository;
      $ps->setId($site_id);
      $ps->setLimit(1);
      $page_setting = $ps->get()->result;
    }

    $_SESSION['site'] = $site;
    $_SESSION['page_setting'] = $page_setting;
    $_SESSION['cms'] = $_SESSION['KCFINDER'] = new \stdClass;
    header("Location: ".ADDRESS_CMS, true , 301);
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    
    $s = new sitesRepository;
    $data = array();
    if($id){
      $s->setId($id);
      $s->setLimit(1);
      $get_site = $s->get();
      $data = $get_site->row[0];
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'design_authority' => $s->design_authority,
      'prefecture' => $ut->masterLoader('prefectures'),
      'data'  => $data
    ]);
    $tpl->display("site/edit.tpl");
  }
  
  public function pushAction(){
    $ac = new sitesRepository;
    $result = $ac->push($_POST);
    $dataType = filter_input(INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }

}

// ?>