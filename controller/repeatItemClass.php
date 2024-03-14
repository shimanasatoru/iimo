<?php  
namespace controller; 
 
use host\cms\repository\utilityRepository; 
use host\cms\repository\repeatItemRepository; 
use Smarty; 
 
class repeatItemClass{ 
   
  use \host\cms\entity\repeatItemEntity; 
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
      'rpid' => [ 
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
      ] 
    ]); 
    $pr = new repeatItemRepository; 
    $site_id = $this->getSiteId(); 
    if(!$site_id){ 
      return $this; 
    } 
    $pr->setSiteId($site_id); 
    $pr->setReleaseKbn(1); 
    if(@$params['id']){ 
      $pr->setId($params['id']); 
    } 
    if(@$params['rpid']){ 
      $pr->setRepeatProductId($params['rpid']); 
    } 
    if(@$params['p']){ 
      $pr->setPage($params['p']); 
    } 
    $pr->setLimit(20); 
    if(@$params['limit']){ 
      $pr->setLimit($params['limit']); 
    } 
    return $pr->get(); 
  } 
} 
 
// ?>