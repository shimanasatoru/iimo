<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\mailSmtpRepository;
use Smarty;

class mailSmtpController{

  /*
   * 検索用
   * @params(request get) int $id, int $navigation_id
   */
  public function getAction(){
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'type' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'typeIs' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    $p = new mailSmtpRepository;
    $p->setSiteId($_SESSION['site']->id);
    if(@$params){
      if($params['id']){
        $p->setId($params['id']);
      }
      if($params['type']){
        $p->setType($params['type']);
      }
      $type_is = $params['typeIs'];
      if($type_is !== null){
        $p->setTypeIs($type_is);
      }
      if($params['dataType'] == 'json'){
        echo json_encode( $p->get(), JSON_UNESCAPED_UNICODE);
        return true;
      }
    }
    return $p->get();
  }
  
  public function indexAction(){
    $au = new \autoload;
    $ut = new utilityRepository;
    $tpl = new Smarty;
    
    $data = (object) array();
    $ms = new mailSmtpRepository;
    $ms->setSiteId($_SESSION['site']->id);
    $get_ms = $ms->get();
    if($get_ms->rowNumber == 1){
      $data = current($get_ms->row);
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'data' => $data
    ]);
    $tpl->display("mail/smtp/edit.tpl");
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var(@$params[2], FILTER_SANITIZE_SPECIAL_CHARS, ['options' => array('default' => null)]);
    $type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS, ['options' => array('default' => null)]);
    $data = array();
    if($id || $type){
      $mt = new mailSmtpRepository;
      $mt->setSiteId($_SESSION['site']->id);
      $mt->setLimit(1);
      if($id){
        $mt->setId($id);
      }
      if($type){
        $mt->setType($type);
      }
      $data = $mt->get()->row[0];
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'type' => $type,
      'data'  => $data
    ]);
    $tpl->display("mail/templates/edit.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new mailSmtpRepository;
    $_POST['site_id'] = $_SESSION['site']->id;
    switch($sw){
      case 'sort':
        $result = array('_status'=> false, '_message'=> array('IDが取得出来ません。'));
        $options = array(
          ['options'=> array('default'=>null, "regexp"=> "/[0-9\,]/")],
          ['options' => array('default' => 0)]
        );
        $ids = filter_input(INPUT_POST, 'ids', FILTER_VALIDATE_REGEXP, $options[0]);
        $page = filter_input(INPUT_POST, 'page', FILTER_VALIDATE_INT, $options[1]);
        $limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT, $options[1]);
        if($ids){
          $ids = explode(',', $ids);
          foreach($ids as $rank => $id){
            $rank = $rank + ($page * $limit) + 1;
            $n->setPost([
              'token'=> $_POST['token'], 
              'id'=> $id, 
              'site_id'=> $_SESSION['site']->id, 
              'rank'=> $rank], 'diff');
            $result = $n->update();
            if(!$result->_status){
              break;
            }
          }
        }
        break;
      default:
        $n->setPost($_POST, 'diff');
        $result = $n->push();
        break;
    }
    $dataType = filter_input( INPUT_GET, 'dataType', FILTER_SANITIZE_SPECIAL_CHARS);
    if($dataType == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  
  public function connectAction(){
    
    $require_filename = SWIFT_DIR; //機能ファイル参照
    if(!file_exists($require_filename)){
      return print "メール機能が見つかりません";
    }
    if(!$_SESSION['site']->id){
      return print "サイトが見つかりません";
    }

    $mt = new mailSmtpRepository;
    $mt->setSiteId($_SESSION['site']->id);
    $mt->setLimit(1);
    $data = $mt->get()->row[0];

    // 送信設定
    require_once $require_filename;
    try {
      if($data->smtp_server_secure){
        $transport = new \Swift_SmtpTransport( $data->smtp_server_name, $data->smtp_server_port, $data->smtp_server_secure);
      }else{
        $transport = new \Swift_SmtpTransport( $data->smtp_server_name, $data->smtp_server_port);
      }
      $transport->setUsername($data->smtp_user_name);
      $transport->setPassword($data->smtp_user_password);
      if($data->smtp_auth_mode){
        $transport->setAuthMode($data->smtp_auth_mode);
      }
      if($data->smtp_options){
        $transport->setStreamOptions(json_decode($data->smtp_options, true));
      }
    }
    catch (Swift_TransportException $e) {
      $this->set_message($e->getMessage());
      return $this;
    }
    return true;
  }
  
}

// ?>