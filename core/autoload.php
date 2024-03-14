<?php 
class autoload {
  
  private $sysRoot;
  public function setSystemRoot($path){
    $this->sysRoot = rtrim($path, '/');
  }
  public function siteDirectory($param = null){
    if(!$param){
      $param = rtrim( $_SERVER['DOCUMENT_ROOT'], '/') . $_SERVER['REQUEST_URI'];
    }
    $directory = explode('/', str_replace(FIRST_ROOT_SITE, '', $param));
    return $directory[0];
  }
  public function uriExplode($param = null){
    if(!$param){
      // パラメーター取得（末尾の / は削除）
      $param = rtrim( $_SERVER['DOCUMENT_ROOT'], '/') . $_SERVER['REQUEST_URI'];
    }
    $firstRoot = null;
    if(strpos($param, FIRST_ROOT_CMS) !== false){
      $firstRoot = FIRST_ROOT_CMS;
    }elseif(strpos($param, FIRST_ROOT_SITE) !== false){
      $firstRoot = FIRST_ROOT_SITE. $this->siteDirectory($param) .'/';
    }
    $param = str_replace($firstRoot, '', $param);
    $param = preg_replace('/^\/|\/?$/', '', $param);
    $params = array();
    if ('' != $param) {
      //　パラメーターの「？」以降は削除
      $param = explode('?', $param);
      if (0 < count($param)) {
        $param = $param[0];
      }
      // パラメーターを"/"で分割
      $params = explode('/', $param);
    }
    return $params;
  }
  
  public function dispatch(){
    $params = $this->uriExplode();

    // クラスファイル読込
    spl_autoload_register(function ($path){
      $path = explode("\\", $path);
      if (count($path) == 1)
        return;

      $sysRoot = $this->sysRoot.'/';
      if (false !== strpos($path[0], 'host')) {
        $sysRoot = DIR_HOST; //use host\directory...の場合
        unset($path[0]);
      }
      $fileName = $sysRoot . implode("/", $path) . '.php';
      if(file_exists($fileName))
        require_once $fileName;
    });

    // 1番目のパラメーターをコントローラーとして取得
    if (FIRST_CONTROLLER != 'index') {
      array_shift($params);
    }
    $controller = FIRST_CONTROLLER;
    if ( FIRST_CONTROLLER == 'index' && 
      isset($_SESSION['user']) && $_SESSION['user'] && 0 < count($params) && $params[0]) {
      $controller = $params[0];
    }
    if (FIRST_CONTROLLER != 'index' && 0 < count($params) && $params[0]) {
      $controller = $params[0];
    }

    // パラメータより取得したコントローラー名によりクラス振分け
    $className = 'controller\\' . $controller . 'Controller';
    if(!class_exists($className)){
      $className = 'controller\\'.FIRST_CONTROLLER.'Controller';
    }
    
    // クラスインスタンス生成
    $controllerInstance = new $className();

    // 2番目のパラメーターをコントローラーとして取得
    $action = 'index';
    if (1 < count($params) && $params[1] && !preg_match('/^[0-9]+$/', $params[1])) {
      $action = $params[1];
    }

    // アクションメソッドを実行
    $actionMethod = $action . 'Action';
    if(!method_exists( $controllerInstance, $actionMethod)){
      $actionMethod = 'indexAction';
    }
    $controllerInstance->$actionMethod();
  }
}

// ?>