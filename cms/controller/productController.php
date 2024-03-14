<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\productRepository;
use host\cms\repository\productCategoryRepository;
use host\cms\repository\productIncludedRepository;
use host\cms\repository\deliveryRepository;
use host\cms\repository\campaignRepository;
use host\cms\repository\unitRepository;
use Smarty;

class productController{

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
      'series_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    $p = new productRepository;
    if(@$params['id']){
      $p->setId($params['id']);
    }
    if(@$params['series_id']){
      $p->setSeriesId($params['series_id']);
    }
    if(@$_SESSION['user']->authcode == "seller"){
      $p->setSellerId($_SESSION['user']->id);
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
  
  public function indexAction(){
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode()
    ]);
    $tpl->display("product/index.tpl");
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);

    $p = new productRepository;
    $site_id = $_SESSION['site']->id;
    $data = array();
    if($id){
      $p->setSiteId($site_id);
      if($_SESSION['user']->authcode == "seller"){
        $p->setSellerId($_SESSION['user']->id);
      }
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
    
    $pc = new productCategoryRepository;
    $pc->setSiteId($site_id);
    $category = $pc->get()->row;

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
      'category'=> $category,
      'campaign'=> $campaign,
      'temperature_zone'=> $p->temperature_zone,
      'caution_include_value'=> $p->caution_include_value,
      'included' => $included,
      'data'  => $data
    ]);
    $tpl->display("product/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new productRepository;
    $_POST['site_id'] = $_SESSION['site']->id;
    if($_SESSION['user']->authcode == "seller"){
      $_POST['seller_id'] = $_SESSION['user']->id;
    }
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
              'rank'=> $rank
            ], 'diff');
            $result = $n->update();
          }
        }
        break;
      case 'stock_status':
        $result = array('_status'=> false, '_message'=> array('IDが取得出来ません。'));
        $options = array(
          ['options'=> array('default'=>null, "regexp"=> "/[0-9\,]/")],
          ['options' => array('default' => 0)]
        );
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $stock_status = filter_input(INPUT_POST, 'stock_status', FILTER_VALIDATE_INT);
        $n->setPost([
          'token'=> $_POST['token'], 
          'id'=> $id, 
          'site_id'=> $_POST['site_id'], 
          'stock_status'=> $stock_status
        ], 'diff');
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