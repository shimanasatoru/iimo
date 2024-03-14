<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\navigationRepository;
use host\cms\repository\fieldRepository;
use Smarty;

class fieldController{

  /*
   * 検索用
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
    $n = new fieldRepository;
    if(@$params['id']){
      $n->setId($params['id']);
    }
    if(@$params['navigation_id']){
      $n->setNavigationId($params['navigation_id']);
    }
    $n->setSiteId(@$_SESSION['site']->id);
    $n->setOrder("rank ASC");
    $result = $n->get();
    if(@$params['dataType'] == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  public function indexAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var($params[1], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$id){
      return false;
    }
    
    $n = new navigationRepository;
    $n->setSiteId($_SESSION['site']->id);
    $n->setId($id);
    $navigation = $n->get();

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'navigation' => $navigation->row[0],
      'data'  => $this->getAction()
    ]);
    $tpl->display("navigation/field/index.tpl");
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $navigation_id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $field_id = filter_var($params[3], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$navigation_id){
      return false;
    }
    
    $n = new navigationRepository;
    $n->setSiteId($_SESSION['site']->id);
    $n->setId($navigation_id);
    $navigation = $n->get();
    
    $n = new fieldRepository;
    $field_type = $n->getFieldType();
    $data = array();
    if($field_id){
      $n->setSiteId($_SESSION['site']->id);
      $n->setId($field_id);
      $n->setLimit(1);
      $get_navigation = $n->get();
      $data = $get_navigation->row[0];
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'field_type'  => $field_type,
      'navigation' => $navigation->row[0],
      'data'  => $data,
    ]);
    $tpl->display("navigation/field/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new fieldRepository;
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
            $rank = $rank + ($page * $limit);
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