<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\productRepository;
use host\cms\repository\productCategoryRepository;
use host\cms\repository\productStockRepository;
use host\cms\repository\unitRepository;
use Smarty;

class productStockController{

  public function indexAction(){
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    
    $site_id = $_SESSION['site']->id;
    
    $p = new productRepository;
    $p->setSiteId($site_id);
    $p->setLimit(10);
    $get_product = $p->get();
    $data = $get_product;

    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'data' => $data
    ]);
    $tpl->display("product/stock/index.tpl");
  }

  public function pushAction(){
    $stock = new productStockRepository;
    $_POST['site_id'] = $_SESSION['site']->id;
    $stock->setPost($_POST, 'diff');
    $result = $stock->push();
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
}

// ?>