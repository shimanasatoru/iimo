<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\sitesRepository;
use host\cms\repository\pageModuleRepository;
use host\cms\repository\pageModuleCategoryRepository;
use host\cms\repository\templateRepository;
use host\cms\repository\templateModuleRepository;
use Smarty;

class pageModuleController{

  /*
   * 検索用
   * @params request get
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
      'theme' => [
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
      ],
    ]);
    
    $result = array('_status' => false);
    if(@$params['theme']){//テーマは必須
      $pm = new pageModuleRepository;
      $pm->setModuleTheme($params['theme']);
      if($_SESSION['site']->id && $_SESSION['site']->design_authority != 'default'){
        $pm->setSiteId($_SESSION['site']->id);
      }
      if(@$params['id']){
        $pm->setId($params['id']);
      }
      if(@$params['theme']){
        $pm->setModuleTheme($params['theme']);
      }
      if(@$params['p']){
        $pm->setPage($params['p']);
      }
      $pm->setLimit(20);
      if(@$params['limit']){
        $pm->setLimit($params['limit']);
      }
      $result = $pm->get();
    }
    if($params['dataType'] == 'json'){
      echo json_encode( $result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  /*
   * カテゴリ取得
   * @params int $id,
   */
  public function categoryGetAction(){
    $u = $_SESSION['user'];
    $s = $_SESSION['site'];
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'theme' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    $result = array('_message' => array("テーマを選択して下さい。"), '_status' => false);
    if(@$params['theme']){//テーマは必須
      /*
       * システム管理者の場合、サイト選択なしで「default」、選択ありで「default」or「original」
       */
      $c = new pageModuleCategoryRepository;
      $c->setModuleTheme($params['theme']);
      if(isset($s->design_authority) && $s->design_authority == "original" && $s->id){
        $c->setSiteId($s->id);
      }
      if($params['id']){
        $c->setId($params['id']);
      }
      $c->setOrder("rank ASC");
      $result = $c->get();
    }
    if(@$params['dataType'] == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }

  /*
   * モジュール作成一覧画面
   * permissions => システム管理者（administrator）サイト編集者（manage）スタッフ（staff）
   * システム管理者の場合、サイト選択なしで「default」、選択ありで「default」or「original」
   * サイト編集者、スタッフの場合、サイト選択なしで「false」、選択ありで「default」or「original」
   */
  public function indexAction(){
    $u = $_SESSION['user'];
    $s = $_SESSION['site'];
    if(in_array($u->permissions, ["manager","staff"]) && $s->design_authority != 'original'){
      print "モジュール権限がありません。";
      return false;
    }
    $theme = filter_input(INPUT_GET, 'theme', FILTER_SANITIZE_SPECIAL_CHARS, ['options' => array('default' => null)]);
    if(!$theme && in_array($u->permissions, ["manager","staff"])){
      header("Location: ./?theme={$s->design_theme}", true, 301);
      return false;
    }
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'category' => $this->categoryGetAction()
    ]);
    $tpl->display("page/module/index.tpl");
  }
  
  public function editAction(){
    $u = $_SESSION['user'];
    $s = $_SESSION['site'];
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    
    //システム管理者でサイト選択状態時、デザイン権限「default」ならsite_idをnull判定
    $site_id = $s->id;
    if(isset($s->design_authority) && 
       in_array($u->permissions, ["administrator"]) && $s->design_authority == 'default'){
      $site_id = null;
    }

    $m = new pageModuleRepository;
    $data = array();
    if($id){
      if($site_id){ //site_idの判定
        $m->setSiteId($site_id);
      }
      $m->setId($id);
      $m->setLimit(1);
      $get_module = $m->get();
      $data = $get_module->row[0];
    }

    $tm = new templateModuleRepository;
    $theme = $tm->get();
    if(isset($s->design_authority) && $s->design_authority == 'original'){
      //オリジナル権限
      $t = new templateRepository;
      $t->setDirectory($s->directory);
      $t->setLevelStop(1);
      $theme = $t->get();
    }

    $tpl->assign([
      'site_id'  => $site_id,
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $params,
      'category' => $this->categoryGetAction(),
      'theme' => $theme,
      'module_type' => $m->module_type,
      'data'  => $data
    ]);
    $tpl->display("page/module/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new pageModuleRepository;
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
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
}

// ?>