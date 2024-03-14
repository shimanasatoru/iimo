<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\productCategoryRepository;
use Smarty;

class productCategoryClass{
  
  use \host\cms\entity\productCategoryEntity;
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
    ]);
    $pr = new productCategoryRepository;
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    $pr->setSiteId($site_id);
    $pr->setReleaseKbn(1);
    return $pr->get();
  }
}

// ?>