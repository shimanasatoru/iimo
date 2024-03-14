<?php 
namespace controller;

class objectController{
  public $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  public function controller(array $params){
    if(!$params['name']){
      return false;
    }
    $site_id = $this->getSiteId();
    if(!$site_id){
      return false;
    }
    $className = $params['name'];
    $loadClass = 'controller\\' . $className . 'Class';
    $class = new $loadClass;
    $class->setSiteId($site_id);
    if($params['action']){
      $action = $params['action'];
      return $class->{$action}($params);
    }
    return $class;
  }
}


// ?>