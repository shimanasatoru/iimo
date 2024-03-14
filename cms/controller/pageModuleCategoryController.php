<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\pageModuleCategoryRepository;
use host\cms\repository\templateRepository;
use host\cms\repository\templateModuleRepository;
use Smarty;

class pageModuleCategoryController{

  /*
   * 検索用
   * @params int $id,
   */
  public function getAction(){
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
    $result = array('_status' => false);
    if(@$params['theme']){//テーマは必須
      $c = new pageModuleCategoryRepository;
      $c->setModuleTheme($params['theme']);
      if($_SESSION['site']->id){
        $c->setSiteId($_SESSION['site']->id);
      }
      if($params['id']){
        $c->setId($params['id']);
      }
      $c->setOrder("rank ASC");
      $result = $c->get();
    }
    if($params['dataType'] == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);

    $data = array();
    if($id){
      $n = new pageModuleCategoryRepository;
      if($_SESSION['site']->id){
        $n->setSiteId($_SESSION['site']->id);
      }
      $n->setId($id);
      $n->setLimit(1);
      $get_category = $n->get();
      $data = $get_category->row[0];
    }
    
    $tm = new templateModuleRepository;
    $theme = $tm->get();
    if($_SESSION['site']->design_authority == 'original'){
      //オリジナル権限
      $t = new templateRepository;
      $t->setDirectory($_SESSION['site']->directory);
      $t->setLevelStop(1);
      $theme = $t->get();
    }
    
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $params,
      'theme' => $theme,
      'parent_data' => $this->getAction(),
      'data'  => $data
    ]);
    $tpl->display("page/module/category/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new pageModuleCategoryRepository;
    $_POST['site_id'] = $_SESSION['site']->id;
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
      case 'move':
        $n->setPost($_POST, 'diff');
        $result = $n->update();
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