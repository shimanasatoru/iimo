<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\templateModuleRepository;
use Smarty;

class templateModuleController{

  /*
   * 検索用
   * @params int $id,
   */
  public function getAction(){
    
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS, ['options' => array('default' => null)]);
    $params = filter_input_array(INPUT_GET, [
      'theme' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'directory' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'outputType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'levelStop' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
    ]);
    $t = new templateModuleRepository;
    if(@$params['theme']){
      $t->setDesignTheme($params['theme']);
    }
    if(@$params['directory']){
      $t->setDesignDirectory($params['directory']);
    }
    if(@$params['outputType']){
      $t->setOutputType($params['outputType']);
    }
    if(@$params['levelStop']){
      $t->setLevelStop($params['levelStop']);
    }
    $result = $t->get();
    if(@$params['dataType'] == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  public function indexAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $params = filter_input_array(INPUT_GET, [
      'theme' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ]
    ]);
    $t = new templateModuleRepository;
    $t->setDesignTheme(@$params['theme']);
    if($theme = $t->getDesignTheme()){
      $_SESSION['site'] = $_SESSION['cms'] = $_SESSION['KCFINDER'] = new \stdClass;
      $_SESSION['cms']->design_theme = $theme;
    }
    $au = new \autoload;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
    ]);
    $tpl->display("template/module/index.tpl");
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $params = filter_input_array(INPUT_GET, [
      'theme' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'directory' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'file' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
    ]);
    if(!$params['theme'] || !$params['directory']){
      return false;
    }
    $t = new templateModuleRepository;
    $t->setDesignTheme($params['theme']);
    $t->setDesignDirectory($params['directory']);
    
    $data = array();
    if($params['file']){
      $t->setDesignFile($params['file']);
      $get_file = $t->get();
      $data = $get_file->row[0];
    }
    $au = new \autoload;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'data'  => $data
    ]);
    $tpl->display("template/module/edit.tpl");
  }
  
  public function themeAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $action = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS, ['options' => array('default' => null)]);
    $params = filter_input_array(INPUT_GET, [
      'theme' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
    ]);
    switch($action){
      default: //edit
        $t = new templateModuleRepository;
        $t->setDesignTheme($params['theme']);
        $data = $t;
      break;
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'data'  => $data
    ]);
    $tpl->display("template/theme/edit.tpl");
  }

  public function pushAction(){
    $_POST['site_id'] = $_SESSION['site']->id;
    $_POST['site_directory'] = $_SESSION['site']->directory;
    $params = filter_input_array(INPUT_GET, [
      'type' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ]
    ]);
    $t = new templateModuleRepository;
    $t->setPost($_POST, 'diff');
    switch($params['type']){
      case 'theme':
        $result = $t->themePush();
        break;
      default:
        $result = $t->push();
        break;
    }
    if($params['dataType'] == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  
}

// ?>