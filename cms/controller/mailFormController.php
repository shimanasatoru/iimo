<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\mailFormRepository;
use host\cms\repository\mailFieldRepository;
use host\cms\repository\mailReceiveRepository;
use host\cms\repository\fieldRepository;
use Smarty;

class mailFormController{

  /*
   * 検索用
   * @params(request get) int $id, int $navigation_id
   */
  public function getFormAction(){
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    $p = new mailFormRepository;
    $p->setSiteId($_SESSION['site']->id);
    if($params['id']){
      $p->setId($params['id']);
    }
    $result = $p->get();
    if($params['dataType'] == 'json'){
      echo json_encode( $result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  /*
   * 検索用
   * @params(request get) int $id, int $navigation_id
   */
  public function getFieldAction(){
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'form_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
    ]);
    $p = new mailFieldRepository;
    $p->setSiteId($_SESSION['site']->id);
    if($params['id']){
      $p->setId($params['id']);
    }
    if($params['form_id']){
      $p->setFormId($params['form_id']);
    }
    $result = $p->get();
    if($params['dataType'] == 'json'){
      echo json_encode( $result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
  /*
   * 検索用
   * @params(request get) int $id, int $navigation_id
   */
  public function getReceiveAction(){
    $params = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'form_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'keyword' => [
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
    $m = new mailReceiveRepository;
    $m->setSiteId($_SESSION['site']->id);
    if($params['id']){
      $m->setId($params['id']);
    }
    if($params['form_id']){
      $m->setFormId($params['form_id']);
    }
    if($params['keyword']){
      $m->setKeyword($params['keyword']);
    }
    if($params['p']){
      $m->setPage($params['p']);
    }
    $m->setLimit(20);
    if($params['limit']){
      $m->setLimit($params['limit']);
    }
    if($params['dataType'] == 'json'){
      echo json_encode( $m->get(), JSON_UNESCAPED_UNICODE);
      return false;
    }
    if($params['dataType'] == 'csv'){
      $put = array();
      $format = array();
      if($m->get()->row){
        foreach($m->get()->row as $i => $row){
          if($i == 0){
            foreach($row->fields as $field){
              $format[$field->name] = $field->name;
            }
          }
          foreach($row->fields as $field){
            if(is_array($field->value)){
              $field->value = implode("、", $field->value);
            }
            $put[$i][$field->name] = $field->value;
          }
        }
      }
      array_unshift($put, $format);
      $ut = new utilityRepository;
      $ut->downloadCsv("mailForm_{$_SESSION['site']->id}_{$form_id}_".time(), $put);
      return false;
    }
    return $m->get();
  }
  
  public function indexAction(){
    $tpl = new Smarty;
    $au = new \autoload;
    $ut = new utilityRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode()
    ]);
    $tpl->display("mail/form/index.tpl");
  }
  
  public function editAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);

    $data = array();
    if($id){
      $n = new mailFormRepository;
      $n->setSiteId($_SESSION['site']->id);
      $n->setId($id);
      $n->setLimit(1);
      $get_form = $n->get();
      $data = $get_form->row[0];
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'data'  => $data
    ]);
    $tpl->display("mail/form/edit.tpl");
  }
  
  public function editFormAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $id = filter_var($params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $data = array();
    if($id){
      $n = new mailFormRepository;
      $n->setSiteId($_SESSION['site']->id);
      $n->setId($id);
      $n->setLimit(1);
      $get_form = $n->get();
      $data = $get_form->row[0];
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'data'  => $data
    ]);
    $tpl->display("mail/form/edit_form.tpl");
  }
  
  public function editFieldAction(){
    $tpl = new Smarty;
    $ut = new utilityRepository;
    $au = new \autoload;
    $params = $au->uriExplode();
    $form_id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $field_id = filter_var(@$params[3], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$form_id){
      return false;
    }
    
    $m = new mailFormRepository;
    $m->setSiteId($_SESSION['site']->id);
    $m->setId($form_id);
    $m->setLimit(1);
    $get_form = $m->get();
    $form = $get_form->row[0];
    
    $data = array();
    if($field_id){
      $f = new mailFieldRepository;
      $f->setSiteId($_SESSION['site']->id);
      $f->setId($field_id);
      $f->setLimit(1);
      $get_field = $f->get();
      $data = $get_field->row[0];
    }
    
    $f = new fieldRepository;
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode(),
      'field_type' => $f->getFieldType(),
      'form' => $form,
      'data' => $data
    ]);
    $tpl->display("mail/form/edit_field.tpl");
  }
  
  public function receiveAction(){
    $tpl = new Smarty;
    $au = new \autoload;
    $ut = new utilityRepository;
    $params = $au->uriExplode();
    $form_id = filter_var(@$params[2], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    $receive_id = filter_var(@$params[3], FILTER_VALIDATE_INT, ['options' => array('default' => null)]);
    if(!$form_id){
      return false;
    }
    if($receive_id){
      $f = new mailReceiveRepository;
      $f->setSiteId($_SESSION['site']->id);
      $f->setFormId($form_id);
      $f->setId($receive_id);
      $f->setLimit(1);
      $data = $f->get()->row[0];
      $tpl->assign([
        'token' => $ut->h($ut->generate_token()),
        'request_uri' => $au->uriExplode(),
        'data' => $data
      ]);
      $tpl->display("mail/receive/detail.tpl");
      return false;
    }
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'request_uri' => $au->uriExplode()
    ]);
    $tpl->display("mail/receive/index.tpl");
  }
  
  public function pushFormAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new mailFormRepository;
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
  
  public function pushFieldAction(){
    $au = new \autoload;
    $params = $au->uriExplode();
    $sw = filter_var($params[2], FILTER_SANITIZE_SPECIAL_CHARS);
    $n = new mailFieldRepository;
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