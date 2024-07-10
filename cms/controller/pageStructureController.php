<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\navigationRepository;
use host\cms\repository\pageRepository;
use host\cms\repository\fieldRepository;
use host\cms\repository\pageModuleRepository;
use host\cms\repository\pageModuleCategoryRepository;
use host\cms\repository\pageStructureRepository;
use controller\pageController;
use Smarty;

class pageStructureController{

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
    ]);
    $p = new pageStructureRepository;
    if(@$params['id']){
      $p->setId($params['id']);
    }
    if(@$params['navigation_id']){
      $p->setNavigationId($params['navigation_id']);
    }
    $p->setSiteId(@$_SESSION['site']->id);
    $result = $p->get();
    if(@$params['dataType'] == 'json'){
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
    $navigation = $n->get();
    $navigation = $navigation->row[0];

    $tpl = new Smarty;
    $ut = new utilityRepository;
    $p = new pageController;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'navigation' => $navigation,
      'permission' => $p->getPermission($navigation_id)
    ]);
    $tpl->display("page/structure/index.tpl");
  }

  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $uri = $au->uriExplode();
    $site = $_SESSION['site'];
    $options = array('options' => array('default' => null));
    $navigation_id = filter_var(@$uri[2], FILTER_VALIDATE_INT, $options);
    $id = filter_var(@$uri[3], FILTER_VALIDATE_INT, $options);
    $params = filter_input_array(INPUT_GET, [
      'module_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    if(!$site || !isset($site->id) || !$navigation_id){
      return false;
    }

    $n = new navigationRepository;
    $n->setSiteId($site->id);
    $n->setId($navigation_id);
    $navigation = $n->get()->row[0];

    $module_id = @$params['module_id'];
    if(!$id && !$module_id){
      //モジュール選択画面
      $module_category_id = filter_input(INPUT_GET, 'module_category_id', FILTER_VALIDATE_INT, $options);
      $ca = new pageModuleCategoryRepository;
      $ca->setModuleTheme($site->design_theme);
      $pm = new pageModuleRepository;
      $pm->setModuleTheme($site->design_theme);
      if($site->design_authority != "default"){
        $pm->setSiteId($site->id);
        $ca->setSiteId($site->id);
      }
      if($module_category_id){
        $pm->setModuleCategoryId($module_category_id);
      }
      $tpl->assign([
        'token' => $ut->h($ut->generate_token()),
        'request_uri' => $au->uriExplode(),
        'navigation' => $navigation,
        'category' => $ca->get(),
        'module' => $pm->get()
      ]);
      $tpl->display("page/module/select.tpl");
      return false;
    }
    //モジュールプレビュー
    $p = new pageStructureRepository;
    $p->setSiteUri($navigation->uri);
    $p->setSiteId($site->id);
    $p->setLimit(1);
    if($id){
      $p->setId($id);
    }
    if($module_id){
      $p->setModuleId($module_id);
    }
    $p->preview();
    return false;
  }
  
  public function originalEditAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $uri = $au->uriExplode();
    $site = $_SESSION['site'];
    $navigation_id = filter_var(@$uri[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $page_id = filter_var(@$uri[3], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $bk_id = filter_input(INPUT_GET, 'bk_id', FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $preview = filter_input(INPUT_GET, 'preview', FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$site || !$navigation_id){
      return false;
    }
    //デザイン権限がオリジナルの場合のみとする
    if($site->design_authority != "original"){
      //print "デザイン権限がオリジナルの場合のみ使用可能です。";
      //return false;
    }
    
    $n = new navigationRepository;
    $n->setSiteId($site->id);
    $n->setId($navigation_id);
    $navigation = $n->get()->row[0];
    
    if($preview && $navigation->uri){
      //途中プレビューストラクチャ
      $ps = new pageStructureRepository;
      $ps->setSiteUri($navigation->uri);
      $ps->setSiteId($site->id);
      $ps->setLimit(1);
      if($_POST && $_SERVER['REQUEST_METHOD'] === 'POST'){
        $ps->setPreviewStructurePost($_POST);//プレビューポスト値を取得
      }
      $ps->preview();
      return false;
    }
    
    //修正画面
    $data = array();
    $restore = array();
    if($page_id){
      $p = new pageStructureRepository;
      $p->setSiteUri($navigation->uri);
      $p->setSiteId($site->id);
      $p->setLimit(1);
      $p->setId($page_id);
      $data = $p->get()->row[0];
      if($data){
        //復元データ取得
        $re = new pageStructureRepository;
        $re->setBackupTableFlg(1);
        $re->setSiteUri($navigation->uri);
        $re->setSiteId($site->id);
        $re->setId($page_id);
        $re->setLimit(5);
        $restore = $re->get();
        if($bk_id){
          //復元データ展開
          $bk = new pageStructureRepository;
          $bk->setBackupTableFlg(1);
          $bk->setSiteUri($navigation->uri);
          $bk->setSiteId($site->id);
          $bk->setId($page_id);
          $bk->setBkId($bk_id);
          $bk->setLimit(1);
          $data = $bk->get()->row[0];
        }
      }
    }

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'navigation' => $navigation,
      'data'  => $data,
      'restore'  => $restore
    ]);
    $tpl->display("page/structure/edit.tpl");
  }

  public function pushAction(){
    $p = new pageController;
    if($p->getPermission($_POST['navigation_id'])){
      $au = new \autoload;
      $params = $au->uriExplode();
      $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
      $n = new pageStructureRepository;
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
  
  
}

// ?>