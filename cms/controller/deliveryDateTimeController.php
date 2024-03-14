<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\deliveryDateTimeRepository;
use Smarty;

class deliveryDateTimeController{

  /*
   * 検索用
   * @params request get
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
    ]);
    $p = new deliveryDateTimeRepository;
    if($params['id']){
      $p->setId($params['id']);
    }
    $p->setSiteId($_SESSION['site']->id);
    $result = $p->get();
    if($params['dataType'] == 'json'){
      echo json_encode( $result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }

  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $data = array();
    $n = new deliveryDateTimeRepository;
    $n->setSiteId($_SESSION['site']->id);
    $n->setLimit(1);
    $get_datetime = $n->get();
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'prefectures'=> $ut->masterLoader('prefectures'),
      'temperature_zone'=> $ut->masterLoader('temperature_zone'),
      'data'  => $get_datetime->row[0]
    ]);
    $tpl->display("delivery/date_time.tpl");
  }
  
  public function pushAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new deliveryDateTimeRepository;
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
}

// ?>