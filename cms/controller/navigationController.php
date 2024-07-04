<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\navigationRepository;
use Smarty;

class navigationController{

  /*
   * 検索用
   * @params int $id,
   */
  public function getAction(){
    if(!@$_SESSION['site']->id){
      echo json_encode(array('_status' => false));
      return false;
    }
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    $n = new navigationRepository;
    if(@$params['id']){
      $n->setId($params['id']);
    }
    $n->setSiteId($_SESSION['site']->id);
    $n->setOrder("rank ASC");
    $result = $n->get();
    if(@$params['dataType'] == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
    $tpl->display("navigation/index.tpl");
  }
  
  public function directoryPathCreateAction(){
    if(!$_SESSION['site']->id){
      return print "サイトを選択して下さい";
    }
    
    $n = new navigationRepository;
    $n->setSiteId($_SESSION['site']->id);
    $n->setOrder("rank ASC");
    $n->directoryPathCreate();
  }
  
  public function sitemapCreateAction($reload = true){
    if(!$_SESSION['site']->id){
      return print "サイトを選択して下さい";
    }
    
    $n = new navigationRepository;
    $n->setSiteId($_SESSION['site']->id);
    $n->setReleaseKbn(1);
    $n->setOrder("rank ASC");
    if($reload && $n->sitemapCreate()){
      $address = ADDRESS_CMS.'?reload';
      header("Location: {$address}", true , 301);
    }
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);

    $data = array();
    $history = array();
    $accounts = array();
    if($id){
      $n = new navigationRepository;
      $n->setSiteId($_SESSION['site']->id);
      $n->setId($id);
      $data = $n->get()->row[0];

      $h = new navigationRepository;
      $h->setSiteId($_SESSION['site']->id);
      $h->setId($id);
      $h->setLimit(5);
      $history = $h->getBk();
      
      if($data){
        $nta = new navigationRepository;
        $nta->setNavigationId($data->id);
        $accounts = $nta->getAccount()->row;
      }
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'parent_data' => $this->getAction(),
      'data'  => $data,
      'history'  => $history,
      'accounts'  => $accounts
    ]);
    $tpl->display("navigation/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new navigationRepository;
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
      case 'move':
        $n->setPost($_POST, 'diff');
        $result = $n->update();
        break;
      case 'permission':
        $result = array('_status'=> false, '_message'=> array('IDが取得出来ません。'));
        $post = filter_input_array(INPUT_POST, [
          'token' => [
            'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
            'options'=> array('default'=>null)
          ],
          'account_id' => [
            'filter' => FILTER_VALIDATE_INT,
            'options'=> array('default'=>null)
          ],
          'navigation_id' => [
            'filter' => FILTER_VALIDATE_INT,
            'flags'=> FILTER_REQUIRE_ARRAY,
            'options'=> array('default'=>null)
          ]
        ]);
        if($post['token'] && $post['account_id'] && is_array($post['navigation_id'])){
          $accounts['navigation_id'] = array();
          $accounts['account_id'] = array();
          $accounts['delete_kbn'] = array();
          foreach($post['navigation_id'] as $navigation_id => $value){
            $delete_kbn = false;
            if(!$value){
              $delete_kbn = 1;
            }
            $accounts['navigation_id'][] = $navigation_id;
            $accounts['account_id'][] = $post['account_id'];
            $accounts['delete_kbn'][] = $delete_kbn;
          }
          $n->setPost([
            'token' => $post['token'],
            'site_id' => $_POST['site_id'],
            'accounts' => $accounts], 'diff');
          $result = $n->updateAccountToNavigation();
          if(!$result->_status){
            break;
          }
        }
        break;
      default:
        $n->setPost($_POST, 'diff');
        $result = $n->push();
        break;
    }
    
    if($result->_status == true){
      $this->sitemapCreateAction(false); //サイトマップ生成
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