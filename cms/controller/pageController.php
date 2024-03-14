<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\navigationRepository;
use host\cms\repository\pageRepository;
use host\cms\repository\fieldRepository;
use host\cms\repository\pageModuleRepository;
use host\cms\repository\pageSettingRepository;
use host\cms\repository\sitesRepository;
use Smarty;

class pageController{

  /*
   * 検索用
   * @params(request get) int $id, int $navigation_id
   */
  public function getAction(){
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'navigation_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'p' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    $p = new pageRepository;
    $p->setSiteId($_SESSION['site']->id);
    if($params['id']){
      $p->setId($params['id']);
    }
    if($params['navigation_id']){
      $p->setNavigationId($params['navigation_id']);
    }
    if($params['p']){
      $p->setPage($params['p']);
    }
    $result = $p->get();
    if($params['dataType'] == 'json'){
      echo json_encode( $result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  public function indexAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $navigation_id = filter_var($params[1], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$navigation_id){
      return false;
    }
    $n = new navigationRepository;
    $n->setSiteId($_SESSION['site']->id);
    $n->setId($navigation_id);
    $navigation = @$n->get()->row[0];
    if($navigation->format_type != "listFormat"){
      header('Location: '.ADDRESS_CMS."pageStructure/{$navigation->id}/");
      return false;
    }
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'navigation' => $navigation,
      'permission' => $this->getPermission($navigation_id)
    ]);
    $tpl->display("page/index.tpl");
  }

  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $navigation_id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $page_id = filter_var($params[3], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $restore_datetime = filter_input(INPUT_GET, 'restore_datetime', FILTER_VALIDATE_REGEXP, ['options'=> array("regexp"=> "/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/")]);
    if(!$navigation_id){
      return false;
    }
    
    $n = new navigationRepository;
    $n->setSiteId($_SESSION['site']->id);
    $n->setId($navigation_id);
    $navigation = $n->get();
    
    $m = new pageModuleRepository;
    $module = $m->get();
    
    //入力項目
    $f = new fieldRepository;
    $f->setSiteId($_SESSION['site']->id);
    $f->setNavigationId($navigation_id);
    $f->setOrder("rank ASC");
    $field = $f->get();
    
    $data = array();
    $restore = array();
    if($page_id){
      $p = new pageRepository;
      $p->setSiteId($_SESSION['site']->id);
      $p->setId($page_id);
      $p->setLimit(1);
      $data = $p->get()->row[0];
      if($data){
        //復元データ取得
        $re = new pageRepository;
        $re->setSiteId($_SESSION['site']->id);
        $re->setId($page_id);
        $re->setLimit(5);
        $restore = $re->getRestore();
        if($restore_datetime){
          //復元データ展開
          $bk = new pageRepository;
          $bk->setSiteId($_SESSION['site']->id);
          $bk->setId($page_id);
          $bk->setLimit(5);
          $bk->setRestoreDatetime($restore_datetime);
          $data = $bk->getRestore()->row[0];
        }
      }
    }

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'module' => $module,
      'navigation' => $navigation->row[0],
      'field' => $field,
      'data'  => $data,
      'restore'  => $restore
    ]);
    $tpl->display("page/edit.tpl");
  }
  
  public function previewAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $navigation_id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$navigation_id){
      return false;
    }
    
    $n = new navigationRepository;
    $n->setSiteId($_SESSION['site']->id);
    $n->setId($navigation_id);
    $navigation = $n->get()->row[0];
    if(!$navigation || !$navigation->uri){
      return false;
    }
    
    $siteController = glob(DIR_HOST.'controller/*.php');
    foreach($siteController as $file){
      require_once($file);
    }
    $index = new \controller\indexController;
    $index->setUri($navigation->uri);
    $index->setPreviewFlg(1);
    if($_POST && $_SERVER['REQUEST_METHOD'] === 'POST'){
      $index->setPreviewPagePost($_POST);//プレビューポスト値を取得
    }
    return $index->indexAction();
  }
  
  public function pushAction(){
    if($this->getPermission($_POST['navigation_id'])){
      $au = new \autoload;
      $params = $au->uriExplode();
      $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
      $n = new pageRepository;
      $_POST['site_id'] = $_SESSION['site']->id;
      $_POST['account_id'] = $_SESSION['user']->id;
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
              $n->setPost([
                'token'=> $_POST['token'], 
                'id'=> $id, 
                'site_id'=> $_SESSION['site']->id, 
                'rank'=> $rank], 'diff');
              $result = $n->update();
              if(!$result->_status){
                break;
              }
            }
          }
          break;
        default:
          $n->setPost($_POST, 'diff');
          $result = $n->push();
          break;
      }
    }else{
      $result = array('_status'=> false, '_message'=> array('編集権限がありません。'));
    }
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  public function getPermission(int $navigation_id){
    $result = false;
    if(!$_SESSION['site']->edit_permission || $_SESSION['user']->permissions == 'administrator'){
      $result = true;
    }
    $nta = new navigationRepository;
    $nta->setNavigationId($navigation_id);
    $nta->setAccountId($_SESSION['user']->id);
    $account = @$nta->getAccount()->row[0];
    if($_SESSION['site']->edit_permission && $account){
      $result = true;
    }
    return $result;
  }
  /*
   * ページセッティング
   */
  public function settingAction(){
    if(!$_SESSION['site']->id){
      return false;
    }
    $s = new sitesRepository;
    $s->setId($_SESSION['site']->id);
    $site = $s->get()->row[0];
    
    $p = new pageSettingRepository;
    $p->setId($_SESSION['site']->id);
    $setting = $p->get()->row[0];

    $tpl = new Smarty;
    $au = new \autoload;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'site' => $site,
      'setting' => $setting
    ]);
    $tpl->display("page/setting.tpl");
  }
  /*
   * ページリンクを一括修正
   */
  public function fixPageLinkAction(){
    if(!$_SESSION['site']->id){
      return false;
    }
    
    $s = new sitesRepository;
    $s->setId($_SESSION['site']->id);
    $site = $s->get()->row[0];
    if(!$site){
      return false;
    }

    $pg = new pageRepository;
    $pg->setSiteId($site->id);
    $pg->setBeforName($site->server_url);
    $pg->setAfterName($site->url);
    $pg->setPost($_POST, 'diff');
    $result = $pg->fixPageLink();
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  /*
   * ページセッティングを保存
   */
  public function pushSettingAction(){
    $_POST['id'] = $_SESSION['site']->id;
    $_POST['directory'] = $_SESSION['site']->directory;
    if(!$_POST['id'] || !$_POST['directory']){
      return false;
    }
    $n = new pageSettingRepository;
    $result = $n->push($_POST);
    
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
}

// ?>