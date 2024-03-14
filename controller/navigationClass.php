<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\navigationRepository;
use Smarty;

class navigationClass{
  
  use \host\cms\entity\navigationEntity;
  /*
   * 出力
   * @params int $id,
   */
  public function view($params){
    $params = filter_var_array($params, [
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    $n = new navigationRepository;
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    if(@$params['id']){
      $n->setId($params['id']);
    }
    $n->setSiteId($site_id);
    $n->setReleaseKbn(1);
    return $n->get();
  }
}

// ?>