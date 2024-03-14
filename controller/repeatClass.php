<?php  
namespace controller; 
 
use host\cms\repository\utilityRepository; 
use host\cms\repository\repeatRepository; 
use Smarty; 
 
class repeatClass{ 
   
  use \host\cms\entity\repeatEntity; 
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
      ] 
    ]); 
    $pr = new repeatRepository; 
    $site_id = $this->getSiteId(); 
    if(!$site_id){ 
      return $this; 
    } 
    $pr->setSiteId($site_id); 
    $pr->setReleaseKbn(1); 
    if(@$params['id']){ 
      $pr->setId($params['id']); 
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