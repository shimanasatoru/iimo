<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use Smarty;

class masterClass{
  use \host\cms\entity\masterEntity;
  /*
   * 都道府県
   * @params int $id,
   */
  public function prefectures(){
    $ut = new utilityRepository;
    return $ut->masterLoader('prefectures');
  }
}

// ?>