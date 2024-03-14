<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\notificationRepository;
use Smarty;

class notificationController{
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
      'release_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'keyword' => [
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
    $n = new notificationRepository;
    if(@$params['release_kbn']){
      $n->setReleaseKbn($params['release_kbn']);
    }    
    if(@$params['id']){
      $n->setId($params['id']);
    }
    if(@$params['keyword']){
      $n->setKeyword($params['keyword']);
    }
    if(@$params['p']){
      $n->setPage($params['p']);
    }
    $n->setLimit(20);
    if(@$params['limit']){
      $n->setLimit($params['limit']);
    }
    $result = $n->get();
    if(@$params['dataType'] == 'json'){
      echo json_encode( $result, JSON_UNESCAPED_UNICODE);
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
      'request_uri' => $au->uriExplode()
    ]);
    $tpl->display("notification/index.tpl");
  }
  
  public function viewAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    
    $m = new notificationRepository;
    $data = array();
    if($id){
      $m->setId($id);
      $m->setLimit(1);
      $data = $m->get()->row[0];
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $params,
      'data'  => $data
    ]);
    $tpl->display("notification/view.tpl");
  }
  
  public function postsAction(){
    if($_SESSION['user']->permissions != 'administrator'){
      exit;
    }
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode()
    ]);
    $tpl->display("notification/posts.tpl");
  }
    
  public function editAction(){
    if($_SESSION['user']->permissions != 'administrator'){
      exit;
    }    
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    
    $m = new notificationRepository;
    $data = array();
    if($id){
      $m->setId($id);
      $m->setLimit(1);
      $data = $m->get()->row[0];
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $params,
      'data'  => $data
    ]);
    $tpl->display("notification/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $m = new notificationRepository;
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
            $m->setPost([
              'token'=> $_POST['token'], 
              'id'=> $id, 
              'rank'=> $rank
            ], 'diff');
            $result = $m->update();
          }
        }
        break;
      default:
        $_POST['account_id'] = $_SESSION['user']->id;
        $m->setPost($_POST, 'diff');
        $result = $m->push();
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