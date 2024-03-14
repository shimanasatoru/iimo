<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\memberRepository;
use Smarty;

class memberController{

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
      'p' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'limit' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    $p = new memberRepository;
    $p->setSiteId($_SESSION['site']->id);
    if(@$params['id']){
      $p->setId($params['id']);
    }
    if(@$params['p']){
      $p->setPage($params['p']);
    }
    $p->setLimit(10);
    if(@$params['limit']){
      $p->setLimit($params['limit']);
    }
    $result = $p->getMember();
    if($params['dataType'] == 'json'){
      echo json_encode( $result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  /* 
   * 顧客の一括チェック
   * @Route("/checker", name="checker")
   * @param Request $request
   */
  public function checkerAction(){
    $au = new \autoload;
    $tpl = new Smarty;
    $ut  = new utilityRepository;
    $d = new memberController;
    $data = $d->getAction();
    $tpl->assign([
      'token'=> $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'data'=> $data
    ]);
    $tpl->display("member/checker.tpl");
  }

  public function indexAction(){
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode()
    ]);
    $tpl->display("member/index.tpl");
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $site_id = $_SESSION['site']->id;
    
    $m = new memberRepository;
    $data = array();
    if($id){
      $m->setSiteId($site_id);
      $m->setId($id);
      $m->setLimit(1);
      $data = $m->getMember()->row[0];
    }

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $params,
      'prefectures'=> $m->prefectures,
      'data'  => $data
    ]);
    $tpl->display("member/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var(@$params[2], FILTER_SANITIZE_SPECIAL_CHARS);

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
            $m = new memberRepository;
            $m->setSiteId($_SESSION['site']->id);
            $m->setId($id);
            $m->setPostMember([
              'token'=> $_POST['token'], 
              'rank'=> $rank], 'diff');
            $result = $m->update();
            if(!$result->_status){
              break;
            }
          }
        }
        break;
      default:
        $_POST['status_kbn'] = 1;//管理画面からは本登録とする
        
        $m = new memberRepository;
        $m->setSiteId($_SESSION['site']->id);
        $m->setId($_POST['id']);
        $m->setPostMember($_POST);
        $m->filterMember();
        if($m->_status){
          $m->pushMember();
        }
        $result = $m;
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