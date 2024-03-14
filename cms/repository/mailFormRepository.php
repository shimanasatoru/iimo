<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\mailFieldRepository;
use host\cms\repository\mailRepository;
use host\cms\repository\siteRepository;

class mailFormRepository extends dbRepository {
  
  use \host\cms\entity\mailFormEntity;
  
  //フォームの値を送信
  public function pushPost(array $post){
    $filter = $this->filterPost($post);
    if(!$filter->get_status() || $this->confirm){
      return $filter;
    }

    $this->connect();
    $this->beginTransaction();
    try {
      //データを保存->メールを整形して送信
      $data = $filter->row[0];
      $detail = <<<EOD
==================================================
{$data->name}
==================================================

EOD;
      
      //値を保存
      $q = $this->queryCreate([
        'site_id' => $data->site_id,
        'form_id' => $data->id
      ], 'mail_form_receive_tbl');
      $stmt = $this->prepare($q['query']);
      if(!$stmt->execute($q['params'])){
        $this->rollBack();
      }
      $this->_lastId = $this->lastInsertId();
      
      $sendTo = array();
      $table = array();
      $save = array();
      foreach($data->fields as $field){
        $value = json_encode($field->value, JSON_UNESCAPED_UNICODE);
        if($value == "null" || $value == "\"\""){
          $value = "___";
        }
        $detail.= <<<EOD
{$field->name} : {$value}

EOD;
        //送信先を割り当てる
        if($field->field_type == 'input_reply_email' && $field->value){
          $sendTo[] = $field->value;
        }
        //値をテンプレートで使用できるようにする
        $table['{$field['.$field->id.']}'] = $value;
      
        //値を保存
        $save = [
          'site_id' => $field->site_id,
          'receive_id' => $this->_lastId,
          'form_id' => $field->form_id,
          'field_id' => $field->id,
          'name' => $field->name,
          'value' => $value
        ];
        $q = $this->queryCreate($save, 'mail_form_receive_field_tbl');
        $stmt = $this->prepare($q['query']);
        if(!$stmt->execute($q['params'])){
          $this->rollBack();
        }
      }
      $detail.= <<<EOD
==================================================
EOD;
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();

    /* ここでCCを取得する */
    if(is_array($data->cc_mail)){
      foreach($data->cc_mail as $cc){
        $sendTo[] = $cc;
      }
    }
    if(!$sendTo || !$data->from_name || !$data->from_mail){
      return $this;
    }
    $subject  = $data->subject ? $data->subject : $data->name;
    $from = json_encode([$data->from_mail=>$data->from_name], JSON_UNESCAPED_UNICODE);
    //$frommail = [$data->from_mai=>serviceName];
    $table['{$detail}'] = $detail;
    $table['&#13;&#10;'] = "\n";
    $search = array_keys($table);
    $replace = array_values($table);
    $subject = str_replace($search, $replace, $data->subject);
    $body = str_replace($search, $replace, $data->body);
    if(!$subject){
      $subject = $data->name;
    }
    if(!$body){
      $body = $detail;
    }
    $save = array();
    foreach($sendTo as $to){
      if(!$to){
        continue;
      }
      //メール送信・履歴保存
      $tomail = [$to];
      $save = [
        'site_id' => $data->site_id,
        'form_id' => $data->id,
        'to_mail' => $to,
        'from_mail' => $from,
        'subject' => $subject,
        'body' => $body,
        //'reservation' => 1
      ];
      $q = $this->queryCreate($save, 'mail_send_tbl');
      $stmt = $this->prepare($q['query']);
      if(!$stmt->execute($q['params'])){
        $this->rollBack();
      }
      
      $ml = new mailRepository;
      $ml->setSiteId($data->site_id);
      $ml->setToMail([$to]);
      $ml->setFromMail([$data->from_mail => $data->from_name]);
      if($data->replyto_mail && $data->replyto_name){
        $ml->setReplyToMail([$data->replyto_mail => $data->replyto_name]);
      }
      $ml->setSubject($subject);
      $ml->setBody($body);
      $result = $ml->sendSwift();
      if(!$result->get_status()){
        return $result;
      }
    }
    return $this;
  }
  
  //フォームの値を確認
  public function confirmPost(array $post){
    
  }
  
  //フォームの値フィルター
  public $confirm;
  public function filterPost(array $post){
    $request = filter_var_array( $post, [
      'token' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'recaptchaToken' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'confirm' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'post' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ]
    ]);
    $this->post = $request;
    $this->confirm = $request['confirm'];
    $ut = new utilityRepository;
    if(!$this->getSiteId()){
      $this->set_message('サイトを選択してください。');
    }
    if(!$this->row[0]){
      $this->set_message('フォームがありません。');
    }
    if($request['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }    
    if($this->_message || $this->_invalid){
      $this->set_status(false);
      return $this;
    }
    
    $form = $this->row[0];
    foreach($form->fields as $d){
      $d->value = $request['post'][$d->id];
      if($d->required && !$d->value){
        $this->set_message("{$d->name}は必須となります。");
        $d->invalid = "必須項目となります。";
      }
      if(is_numeric($d->max_length_limit) && mb_strlen($d->value) > $d->max_length_limit){
        $this->set_message("{$d->name}は{$d->max_length_limit}文字までとなります。");
        $d->invalid = "{$d->max_length_limit}文字までとなります。";
      }
      //数字・電話番号・メール・郵便番号のチェックがいる
    }
    if($form->g_recaptcha_v3_sitekey && $form->g_recaptcha_v3_secretkey){
      if(!$request['recaptchaToken']){
        $this->set_message('トークン(recaptcha)が発行されないため、送信出来ません。');
      }
      $result = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$form->g_recaptcha_v3_secretkey}&response={$request['recaptchaToken']}");
      $chk = json_decode($result);
      if ($chk->success != true){
        $this->set_message('トークンB(recaptcha)が発行されないため、送信出来ません。');
      }
    }
    if($this->_message || $this->_invalid){
      $this->set_status(false);
      return $this;
    }
    $this->set_status(true);
    return $this;
  }
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("*");
    self::setFrom("mail_form_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("release_kbn = :release_kbn");
      self::setValue(":release_kbn", $release_kbn);
    }    
    self::setOrder("rank ASC");
    $q = self::getSelect()
        .self::getFrom()
        .self::getWhere()
        .self::getGroupBy()
        .self::getOrder()
        .self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      $fieldColumns = self::getExplodeColumns();//fieldカラムを取得
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->cc_mail = explode("\n", str_replace(array("&#13;&#10;", "\r\n", "\r", "\n"), "\n", $d->cc_mail));
        $f = new mailFieldRepository;
        $f->setSiteId($d->site_id);
        $f->setFormId($d->id);
        $f->setReleaseKbn(1);
        $field_get = $f->get();
        $d->fields = $field_get->row;
        $this->row[] = $d;
      }
      $this->rowNumber = $stmt->rowCount();
      if($this->rowNumber > 0){
        $this->set_status(true);
      }
      $stmt_allNumber = $this->prepare("SELECT FOUND_ROWS() as `allNumber`");
      $stmt_allNumber->execute();
      while($d = $stmt_allNumber->fetch(\PDO::FETCH_OBJ)){
        $this->totalNumber = $d->allNumber;
      }
      $this->pageRange = $this->getPageRange();
    } catch (\Exception $e) {
      $this->set_message($e->getMessage());
    }
    return $this;
  }

  public function push(){
    $push = $this->getPost();
    $ut = new utilityRepository;
    if(!$push['site_id']){
      $this->set_message('サイトを選択してください。');
    }
    if(!$ut->mbStrLenCheck($push['name'], 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
    }
    if($push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    unset($push['token']);

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'mail_form_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE mail_form_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $push['site_id']
        ])){
          $this->rollBack();
        }
      }
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }
  
  public function update(){
    $push = $this->getPost();
    $ut   = new utilityRepository;
    if(!$push['id']){
      $this->set_message('IDが取得できません。');
    }
    if(!$push['site_id']){
      $this->set_message('サイトを選択してください。');
    }
    if($push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    unset($push['token']);

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'mail_form_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }
}
// ?>