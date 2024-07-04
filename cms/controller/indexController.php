<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use repository\accountRepository;//ここは（正）
use host\cms\repository\sellerRepository;
use host\cms\repository\logRepository;
use host\cms\repository\mailReceiveRepository;
use host\cms\repository\notificationRepository;
use Smarty;

class indexController{
  
  public function __construct () {
    $this->master = new \StdClass;
    if(!isset($_SESSION['user'])){
      $_SESSION['user'] = array();
    }
    if(!isset($_SESSION['site'])){
      $_SESSION['site'] = (object) array(
        'id' => null
      );
    }
  }
  
  /*
   * ログイン認証
   * @return mixed json object
   */
  public function authAction(){
    $a = new accountRepository;
    echo json_encode($a->auth($_POST), JSON_UNESCAPED_UNICODE);
    return;
  }
  
  /*
   * 出品者-ログイン認証
   * @return mixed json object
   */
  public function authSellerAction(){
    $a = new sellerRepository;
    echo json_encode($a->auth($_POST), JSON_UNESCAPED_UNICODE);
    return;
  }
  
  /*
   * ログアウト
   * @return redirect
   */
  public function logoutAction(){
    $address = ADDRESS_CMS.'?logout';
    if($_SESSION['user']->auth_id){
      $address .= '&a=manage';
    }
    session_destroy();
    header("Location: {$address}", true , 301);
  }
  
  public function indexAction(){
    $user = $_SESSION['user'];
    $site = $_SESSION['site'];

    $tpl = new Smarty;
    $au = new \autoload;
    $ut = new utilityRepository;
    if(!$user){
      $tpl->assign([
        'token' => $ut->h($ut->generate_token()),
        'request_uri' => $au->uriExplode()
      ]);
      $tpl->display("index/login.tpl");
      return false;
    }
    
    //システムからのお知らせ
    $n = new notificationRepository;
    $n->setReleaseKbn(1);
    $n->setLimit(10);
    $notification = $n->get();
    
    //サイトが指定されたら、各種データを取得
    $active = array();
    $daily_active = array();
    $content_active = array();
    $mail_receive = array();
    if($user->site_id){
      
      $request = filter_input_array(INPUT_GET, [
        'start_day' => [
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS
        ],
        'end_day' => [
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS
        ],
      ]);
      if(!$ut->dateCheck(@$request['start_day'])){
        $request['start_day'] = date('Y-m-d', strtotime('-10 day'));
      }
      if(!$ut->dateCheck(@$request['end_day'])){
        $request['end_day'] = date('Y-m-d');
      }

      $l = new logRepository;
      $l->setLimit(5);
      if(isset($site->id) && $site->id){
        $l->setSiteId($site->id);
      }else{
        $l->setSiteIds($user->site_id);
      }
      $active = $l->getPageActiveBk();

      $l = new logRepository;
      if(isset($site->id) && $site->id){
        $l->setSiteId($site->id);
      }else{
        $l->setSiteIds($user->site_id);
      }
      $l->setStartPeriod($request['start_day']);
      $l->setEndPeriod($request['end_day']);
      $daily_active = $l->getDailyActiveBk();
      
      $l = new logRepository;
      $l->setLimit(10);
      if(isset($site->id) && $site->id){
        $l->setSiteId($site->id);
      }else{
        $l->setSiteIds($user->site_id);
      }
      $content_active = $l->getContentActiveBk();
      
      $mr = new mailReceiveRepository;
      $mr->setLimit(5);
      if(isset($site->id) && $site->id){
        $mr->setSiteId($site->id);
      }else{
        $mr->setSiteIds($user->site_id);
      }
      $mail_receive = $mr->get();
    }

    $tpl->assign([
      'notification' => $notification,
      'active' => $active,
      'daily_active' => $daily_active,
      'content_active' => $content_active,
      'mail_receive' => $mail_receive,
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode()
    ]);
    $tpl->display("index/dashboard.tpl");
  }
  
  public function gAuthAction(){
    $au = new \autoload;
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $tpl->display("index/login.tpl");
  }

}

// ?>