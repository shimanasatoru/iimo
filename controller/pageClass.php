<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\pageRepository;
use Smarty;

class pageClass{
  
  use \host\cms\entity\pageEntity;
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
      'release' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'nid' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'nids' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'fid' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'ftype' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'ct' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'cv' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'cvs' => [
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
      ]
    ]);
    $pa = new pageRepository;
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    $pa->setSiteId($site_id);
    if(@$params['release']){
      $pa->setReleaseKbn($params['release']);
    }else{
      $pa->setReleaseKbn(1);
    }
    if(@$params['id']){
      $pa->setId($params['id']);
    }
    if(@$params['nid']){
      $pa->setNavigationId($params['nid']);
    }
    if(@$params['nids']){
      $params['nids'] = explode("][",substr($params['nids'],1,strlen($params['nids'])-2));
      $pa->setNavigationIds($params['nids']);
    }
    if(@$params['fid']){
      $pa->setFieldId($params['fid']);
    }
    if(@$params['ftype']){
      $pa->setFormatType($params['ftype']);
    }
    if(@$params['ct']){
      $pa->setContentType($params['ct']);
    }
    if(@$params['cv']){
      $pa->setContentValue($params['cv']);
    }
    if(@$params['cvs']){
      $params['cvs'] = explode("][",substr($params['cvs'],1,strlen($params['cvs'])-2));
      $pa->setContentValues($params['cvs']);
    }
    if(@$params['p']){
      $pa->setPage($params['p']);
    }
    $pa->setLimit(20);
    if(@$params['limit']){
      $pa->setLimit($params['limit']);
    }
    return $pa->get();
  }
}

// ?>