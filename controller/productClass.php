<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\productRepository;
use Smarty;

class productClass{
  
  use \host\cms\entity\productEntity;
  /*
   * 出力
   * @params int $id,
   */
  public function view($params){
    $params = filter_var_array($params, [
      'id' => [//商品ID
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'cid' => [//カテゴリID
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'sid' => [//出品者ID
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
      'orderBy' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    $pr = new productRepository;
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    $pr->setSiteId($site_id);
    $pr->setReleaseKbn(1);
    if(@$params['id']){
      $pr->setId($params['id']);
    }
    if(@$params['cid']){
      $pr->setCategoryId($params['cid']);
    }
    if(@$params['sid']){
      $pr->setSellerId($params['sid']);
    }
    if(@$params['p']){
      $pr->setPage($params['p']);
    }
    if(@$params['orderBy']){
      $pr->setOrderBy($params['orderBy']);
    }
    $pr->setLimit(20);
    if(@$params['limit']){
      $pr->setLimit($params['limit']);
    }
    return $pr->get();
  }
}

// ?>