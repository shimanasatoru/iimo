<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\repeatRepository;
use host\cms\repository\repeatItemRepository;
use host\cms\repository\productIncludedRepository;
use host\cms\repository\deliveryRepository;
use host\cms\repository\campaignRepository;
use host\cms\repository\unitRepository;
use Smarty;

class repeatController{

  /*
   * 検索用
   * @params request get
   */
  public function getRepeatAction(){
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
    $p = new repeatRepository;
    if(@$params['id']){
      $p->setId($params['id']);
    }
    $p->setSiteId($_SESSION['site']->id);
    $p->setOrder('p.rank ASC');
    $result = $p->get();
    if(isset($params['dataType']) && $params['dataType'] == 'json'){
      echo json_encode( $result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  public function getItemAction(){
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'repeat_product_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    $i = new repeatItemRepository;
    if(@$params['id']){
      $i->setId($params['id']);
    }
    if(@$params['repeat_product_id']){
      $i->setRepeatProductId($params['repeat_product_id']);
    }
    $i->setSiteId($_SESSION['site']->id);
    $i->setOrder('rank ASC');
    $result = $i->get();
    if(isset($params['dataType']) && $params['dataType'] == 'json'){
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
    $tpl->display("repeat/index.tpl");
  }
  
  public function itemAction(){
    $site_id = $_SESSION['site']->id;
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $params = $au->uriExplode();
    $id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$id){
      return false;
    }
    
    $repeat = new repeatRepository;
    $repeat->setSiteId($site_id);
    $repeat->setId($id);
    $repeat->setLimit(1);
    $data = $repeat->get()->row[0];
    if(!$data){
      return false;
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'repeat' => $data
    ]);
    $tpl->display("repeat/item.tpl");
  }
  
  public function editRepeatAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);

    $p = new repeatRepository;
    $site_id = $_SESSION['site']->id;
    $data = array();
    if($id){
      $p->setSiteId($site_id);
      $p->setId($id);
      $p->setLimit(1);
      $get_product = $p->get();
      $data = $get_product->row[0];
    }
    
    $u = new unitRepository;
    $u->setSiteId($site_id);
    $unit = $u->get()->row;
    
    $d = new deliveryRepository;
    $d->setSiteId($site_id);
    $delivery = $d->get()->row;

    $ca = new campaignRepository;
    $ca->setSiteId($site_id);
    $campaign = $ca->get()->row;
    
    $pi = new productIncludedRepository;
    $pi->setSiteId($site_id);
    $included = $pi->get()->row;

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $params,
      'tax_rate'=> $p->tax_rate,
      'unit'=> $unit,
      'delivery'=> $delivery,
      'campaign'=> $campaign,
      'temperature_zone'=> $p->temperature_zone,
      'included' => $included,
      'cycle_unit' => $p->cycle_unit,
      'week' => $p->week,
      'data'  => $data
    ]);
    $tpl->display("repeat/edit_repeat.tpl");
  }
  
  public function editItemAction(){
    $site_id = $_SESSION['site']->id;
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $params = $au->uriExplode();
    $repeat_id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$repeat_id){
      return false;
    }
    
    $r = new repeatRepository;
    $r->setSiteId($site_id);
    $r->setId($repeat_id);
    $r->setLimit(1);
    $repeat = $r->get()->row[0];
    if(!$repeat){
      return false;
    }
    
    $id = filter_var(@$params[3], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $i = new repeatItemRepository;
    $data = array();
    if($id){
      $i->setSiteId($site_id);
      $i->setId($id);
      $i->setLimit(1);
      $data = $i->get()->row[0];
    }

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $params,
      'repeat' => $repeat,
      'data'  => $data
    ]);
    $tpl->display("repeat/edit_item.tpl");
  }

  public function pushRepeatAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new repeatRepository;
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
              'site_id'=> $_POST['site_id'], 
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
  
  public function pushItemAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $_POST['site_id'] = $_SESSION['site']->id;
    
    $item = new repeatItemRepository;
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
            $item->setPost([
              'token'=> $_POST['token'], 
              'id'=> $id, 
              'site_id'=> $_POST['site_id'], 
              'rank'=> $rank], 'diff');
            $result = $item->update();
            if(!$result->_status){
              break;
            }
          }
        }
        break;
      default:
        $item->setPost($_POST, 'diff');
        $result = $item->push();
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