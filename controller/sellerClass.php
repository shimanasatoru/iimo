<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\sellerRepository;
use Smarty;

class sellerClass{
  
  use \host\cms\entity\sellerEntity;
  /*
   * 出力
   * @params int $id,
   */
  public function view($params){
    $params = filter_var_array($params, [
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
      'orderBy' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    $se = new sellerRepository;
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    $se->setSiteId($site_id);
    $se->setStatus(1);
    if(@$params['id']){
      $se->setId($params['id']);
    }
    if(@$params['p']){
      $se->setPage($params['p']);
    }
    if(@$params['orderBy']){
      $se->setOrderBy($params['orderBy']);
    }
    $se->setLimit(20);
    if(@$params['limit']){
      $se->setLimit($params['limit']);
    }
    return $se->get();
  }
}

// ?>