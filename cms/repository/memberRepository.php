<?php 
namespace host\cms\repository;

use host\cms\repository\sitesRepository;
use host\cms\repository\mailRepository;
use host\cms\repository\paymentZeusSecureLinkRepository;

class memberRepository extends dbRepository {
  
  use \host\cms\entity\memberEntity;

  //結果返却
  public function get() {
    $site_id = $this->getSiteId();
    if(!$site_id){
      return $this;
    }
    $this->member = $this->getSession();
    return $this;
  }
  
  //セッション
  private $session;
  public function getSession($name = null){
    switch($name){
      case 'account':
        $result = $_SESSION['user'];
        break;
      default:
        $result = $_SESSION['member'];
        break;
    }
    return $result;
  }
  public function setSession(object $array) :void{
    $_SESSION['member'] = $array;
  }
  public function unsetSession() :void{
    $_SESSION['member'] = '';
  }
  //ログイン
  public function setLogin(array $post){
    $this->setPostLogin($post);
    $this->FilterLogin();
    if($this->_message || $this->_invalid){
      return $this;
    }
  }
  //フィルターログイン
  public function FilterLogin() :object{
    $this->set_status(false);
    $ut = new utilityRepository;
    if(!$this->getSiteId()){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    $post = $this->getPostLogin();
    if(!$post){
      $this->set_message('入力がありません。');
      return $this;
    }
    if(!$post['email_address']){
      $this->set_message('アカウントをご確認下さい。');
      $this->set_invalid('email_address', '必須となります。');
      return $this;
    }
    if(!$ut->mbStrLenCheck($post['password'], 1, 120)){
      $this->set_message('パスワードをご確認下さい。');
      $this->set_invalid('last_name', '必須となります。');
      return $this;
    }
    if(!$ut->validate_token($post['token']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
      $this->set_message('トークンが発行されませんでした、再度お試しください。');
      return $this;
    }
    //会員検索
    $this->setEmailAddress($post['email_address']);
    $this->setStatusKbn(1);
    $this->setLimit(1);
    $get_member = $this->getMember();
    if($get_member->rowNumber != 1){
      $this->set_message('アカウントまたはパスワードが正しくありません。E0102');
      return $this;
    }
    $m = current($get_member->row);
    if($m->old_member == 1){
      //旧会員はMD5でハッシュ
      if( !md5($post['password'], isset($m->password) ? $m->password
          : '$2y$10$abcdefghijklmnopqrstuv' // ユーザ名が存在しないときだけ極端に速くなるのを防ぐ
      )){
        $this->set_message('アカウントまたはパスワードが正しくありません。E0103');
        return $this;
      }
    }else{
      //新会員はこっち
      if( !password_verify($post['password'], isset($m->password) ? $m->password
          : '$2y$10$abcdefghijklmnopqrstuv' // ユーザ名が存在しないときだけ極端に速くなるのを防ぐ
      )){
        $this->set_message('アカウントまたはパスワードが正しくありません。E0104');
        return $this;
      }
    }
    $m->password = true;
    $this->setSession($m);
    $this->set_status(true);
    return $this;
  }
  //ログアウト
  public function setLogout(){
    $this->unsetSession();
    return $this;
  }
  //（仮）登録・会員変更
  public function setMember(array $post) {
    $member = $this->getSession();
    if(get_object_vars($member)){
      $this->setId($member->id);
    }
    $this->setPostMember($post, 'diff');
    $this->filterMember();
    if(!$this->_status || $this->_message || $this->_invalid){
      return $this;
    }
    //（仮）登録はメール送信、変更はセッション書き換え
    $push = $this->pushMember();
    if(!$push->_status || $push->_message || $push->_invalid){
      return $this;
    }
    if(get_object_vars($member) && @$this->getMember()->row[0]){
      $this->setSession($this->getMember()->row[0]);
    }
    return $this;
  }
  //フィルター（仮）登録・会員変更
  public function filterMember() :object{
    $this->set_status(false);
    $ut = new utilityRepository;
    if(!$this->getSiteId()){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    $post = $this->getPostMember();
    if(!$post){
      $this->set_message('入力がありません。');
      return $this;
    }
    if(!$ut->mbStrLenCheck($post['first_name'], 1, 120)){
      $this->set_message('姓をご確認下さい。');
      $this->set_invalid('first_name', '必須または120文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($post['last_name'], 1, 120)){
      $this->set_message('名をご確認下さい。');
      $this->set_invalid('last_name', '必須または120文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($post['first_name_kana'], 1, 120)){
      $this->set_message('姓（カナ）をご確認下さい。');
      $this->set_invalid('first_name_kana', '必須または120文字以内となります。');
    }
    if(!$ut->mbStrLenCheck($post['last_name_kana'], 1, 120)){
      $this->set_message('名（カナ）をご確認下さい。');
      $this->set_invalid('last_name_kana', '必須または120文字以内となります。');
    }
    //郵便番号と住所が合致するか検証がいるよね。
    if(!$post['postal_code']){
      $this->set_message('郵便番号をご確認下さい。');
      $this->set_invalid('postal_code', '必須となります。');
    }
    if(!$post['prefecture_id']){
      $this->set_message('都道府県をご確認下さい。');
      $this->set_invalid('prefecture_id', '必須となります。');
    }
    if(!$post['municipality']){
      $this->set_message('市区町村をご確認下さい。');
      $this->set_invalid('municipality', '必須となります。');
    }
    if(!$post['address1']){
      $this->set_message('番地をご確認下さい。');
      $this->set_invalid('address1', '必須となります。');
    }
    if(!$post['phone_number1']){
      $this->set_message('電話番号をご確認下さい。');
      $this->set_invalid('phone_number1', '必須となります。');
    }
    if(!$post['email_address']){
      $this->set_message('アカウントをご確認下さい。');
      $this->set_invalid('email_address', '必須となります。');
    }
    
    $id = $this->getId();
    if(!$id || ($id && ($post['password'] || $post['_password']))){
      if(!$ut->mbStrLenCheck($post['password'], 8, 16)){
        $this->set_message('パスワードをご確認下さい。');
        $this->set_invalid('password', '8文字以上16文字までとなります。');
      }
      if($post['password'] != $post['_password']){
        $this->set_message('確認用パスワードをご確認下さい。');
        $this->set_invalid('_password', '確認用パスワードが一致しません。');
      }
    }
    if(!$ut->validate_token($post['token']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
      $this->set_message('トークンが発行されませんでした、再度お試しください。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $an = new memberRepository;
    $an->setSiteId($this->getSiteId());
    if($this->getId()){//変更時は自身以外とする
      $an->setElseId($this->getId());
    }
    $an->setEmailAddress($post['email_address']);
    $an->setStatusKbn(1);
    $an->setLimit(1);
    $get_another = $an->getMember();
    if($get_another->rowNumber >= 1){
      $this->set_message('このメールアドレスは使用できません。（既存または、誤りがあります。）');
      return $this;
    }
    $this->set_status(true);
    return $this;
  }

  //会員取得
  public function getMember() {
    self::setSelect("*");
    self::setFrom("member_tbl");
    self::setWhere("delete_kbn IS NULL");
    if($else_id = self::getElseId()){
      self::setWhere("id != :id");
      self::setValue(":id", $else_id);
    }else{
      if($id = self::getId()){
        self::setWhere("id = :id");
        self::setValue(":id", $id);
      }
    }
    if($site_id = self::getSiteId()){
      self::setWhere("site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($status_kbn = self::getStatusKbn()){
      if($status_kbn == "NULL"){
        self::setWhere("status_kbn IS NULL");
      }else{
        self::setWhere("status_kbn = :status_kbn");
        self::setValue(":status_kbn", $status_kbn);
      }
    }
    if($email_address = self::getEmailAddress()){
      self::setWhere("email_address = :email_address");
      self::setValue(":email_address", $email_address);
    }
    if($temporary_password = self::getTemporaryPassword()){
      self::setWhere("temporary_password = :temporary_password AND DATE_FORMAT(temporary_password_date, '%Y%m%d%H%i%s') >= DATE_FORMAT(now(), '%Y%m%d%H%i%s')");
      self::setValue(":temporary_password", $temporary_password);
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
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->phone_number1 = explode("-", $d->phone_number1);
        $d->phone_number2 = explode("-", $d->phone_number2);
        $d->fax_number = explode("-", $d->fax_number);
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
  
  //仮会員・変更保存
  public function pushMember(){
    $push = $this->getPostMember();
    if(!$push){
      return $this;
    }
    $site_id = $this->getSiteId();
    if(!$site_id){
      $this->set_message("サイト番号がありません。");
      return $this;
    }

    $this->set_status(false);

    //整形
    $push['id'] = $this->getId();
    $push['site_id'] = $this->getSiteId();
    if($push['password']){
      $push['password'] = password_hash($push['password'], PASSWORD_DEFAULT);
    }else{
      unset($push['password']);
    }
    if($this->getSendMail() && !$push['id']){
      $mt = new mailTemplatesRepository;
      $mt->setType('temporaryAccount');
      $mt->setSiteId($site_id);
      $template = $mt->get();
      if($template->rowNumber != 1){
        $this->set_message("メールテンプレートがないためご利用できません。");
        return $this;
      }
      $template = current($template->row);
      if(!$template->from_mail || !$template->from_name){
        $this->set_message("送信元がないためご利用できません。");
        return $this;
      }
      //一時パスワードを発行
      $ut = new utilityRepository;
      $time = new \DateTime();
      $time->modify('+30 minutes');
      $push['temporary_password'] = $ut->openSslRandom();
      $push['temporary_password_date'] = $time->format('Y-m-d H:i:s');
    }
    unset($push['token'], $push['_password']);
    
    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'member_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE member_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $push['site_id']
        ])){
          $this->rollBack();
        }
      }
      
      //メールテンプレートを読み込み、送信処理
      if($this->getSendMail() && !$push['id']){
        $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
        $url.= str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
        $url.= '?temporary='.$push['temporary_password'];
        $deadline = $push['temporary_password_date'];
        $body = <<<EOD
==================================================
【認証URL】{$url}
【認証期限】{$deadline}
==================================================

EOD;
        $change = array(
          '{$toMail}'=> $push['email_address'],
          '{$firstName}'=> $push['first_name'],
          '{$lastName}'=> $push['last_name'],
          '{$firstNameKana}'=> $push['first_name_kana'],
          '{$lastNameKana}'=> $push['last_name_kana'],
          '{$companyName}'=> $push['company_name'],
          '{$positionName}'=> $push['position_name'],
          '{$departmentName}'=> $push['department_name'],
          '{$detail}'=> $body,
          '&#13;&#10;'=> "\n",
          '&#10;'=> "\n"
        );
        $search = array_keys($change);
        $replace = array_values($change);
        $body = str_replace($search, $replace, $template->template);
        $to = [$push['email_address']];
        $from = [$template->from_mail => $template->from_name];
        $save = [
          'site_id' => $site_id,
          'to_mail' => json_encode($to, JSON_UNESCAPED_UNICODE),
          'from_mail' => json_encode($from, JSON_UNESCAPED_UNICODE),
          'subject' => $template->subject,
          'body' => $body,
          //'reservation' => 1
        ];
        $q = $this->queryCreate($save, 'mail_send_tbl');
        $stmt = $this->prepare($q['query']);
        if(!$stmt->execute($q['params'])){
          $this->rollBack();
          return $this;
        }
        $ml = new mailRepository;
        $ml->setSiteId($site_id);
        $ml->setToMail($to);
        $ml->setFromMail($from);
        $ml->setSubject($template->subject);
        $ml->setBody($body);
        $result = $ml->sendSwift();
        if(!$result->get_status()){
          $this->rollBack();
          return $result;
        }
      }
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    $this->set_status(true);
    return $this;
  }
  
  //（本）登録
  public function setTemporary(array $post){
    $this->setPostMember($post);
    $this->filterTemporary();
    if(!$this->_status || $this->_message || $this->_invalid){
      return $this;
    }
    $this->set_status(false);
    $id = $this->getId();
    $site_id = $this->getSiteId();
    if(!$id || !$site_id){
      $this->set_message('不明な番号です。');
      return $this;
    }
    $this->connect();
    $this->beginTransaction();
    try {
      $push = array(
        'id' => $id,
        'site_id' => $site_id,
        'status_kbn' => 1,
        'temporary_password' => null,
        'temporary_password_date' => null,
      );
      $query = $this->queryCreate($push, 'member_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    $this->set_status(true);
    return $this;
  }
  //フィルター（本）登録
  public function filterTemporary() :object{
    $post = $this->getPostMember();
    $this->set_status(false);
    $ut = new utilityRepository;
    if(!$this->getSiteId()){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    if(!$post){
      $this->set_message('入力がありません。');
      return $this;
    }
    if(!$ut->mbStrLenCheck($post['temporary_password'], 30, 30)){
      $this->set_message('仮パスワードをご確認下さい。');
      $this->set_invalid('temporary_password', '必須となります。');
      return $this;
    }
    if(!$ut->validate_token($post['token']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
      $this->set_message('トークンが発行されませんでした、再度お試しください。');
      return $this;
    }
    $m = new memberRepository;
    $m->setSiteId($this->getSiteId());
    $m->setTemporaryPassword($post['temporary_password']);
    $m->setTemporaryPasswordDate(date('Y-m-d H:i:s'));
    $m->setStatusKbn('NULL');
    $m->setLimit(1);
    $get_member = $m->getMember();
    if($get_member->rowNumber != 1){
      $this->set_message('有効期限切れ、または終了しました。');
      return $this;
    }
    $this->setId($get_member->row[0]->id);
    $this->set_status(true);
    return $this;
  }
  //（仮）再パスワード申請
  public function setReTemporaryPassword(array $post){
    $this->setPostMember($post);
    $this->filterTemporaryPassword();
    if(!$this->_status || $this->_message || $this->_invalid){
      return $this;
    }
    
    $this->set_status(false);
    $id = $this->getId();
    $site_id = $this->getSiteId();
    $email_address = $this->getEmailAddress();
    if(!$id || !$site_id || !$email_address){
      $this->set_message('不明な番号です。');
      return $this;
    }
    
    $ml = new mailTemplatesRepository;
    $ml->setType('reAccount');
    $ml->setSiteId($site_id);
    $template = $ml->get();
    if($template->rowNumber != 1){
      $this->set_message("メールテンプレートがないためご利用できません。");
      return $this;
    }
    $template = current($template->row);
    if(!$template->from_mail || !$template->from_name){
      $this->set_message("送信元がないためご利用できません。");
      return $this;
    }
    $this->connect();
    $this->beginTransaction();
    try {
      $ut = new utilityRepository;
      $time = new \DateTime();
      $time->modify('+30 minutes');
      $temporary_password = $ut->openSslRandom();
      $temporary_password_date = $time->format('Y-m-d H:i:s');
      $push = array(
        'id' => $id,
        'site_id' => $site_id,
        'temporary_password' => $temporary_password,
        'temporary_password_date' => $temporary_password_date
      );
      $query = $this->queryCreate($push, 'member_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      //メールテンプレートを読み込み、送信処理
      $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
      $url.= str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
      $url.= '?repassword='.$temporary_password;
      $deadline = $temporary_password_date;
      $body = <<<EOD
==================================================
【再発行URL】{$url}
【再発行期限】{$deadline}
==================================================

EOD;
      $change = array(
        '{$toMail}'=> $email_address,
        '{$detail}'=> $body,
        '&#13;&#10;'=> "\n",
        '&#10;'=> "\n"
      );
      $search = array_keys($change);
      $replace = array_values($change);
      $body = str_replace($search, $replace, $template->template);
      $to = [$email_address];
      $from = [$template->from_mail=>$template->from_name];
      $save = [
        'site_id' => $site_id,
        'to_mail' => json_encode($to, JSON_UNESCAPED_UNICODE),
        'from_mail' => json_encode($from, JSON_UNESCAPED_UNICODE),
        'subject' => $template->subject,
        'body' => $body,
        //'reservation' => 1
      ];
      $q = $this->queryCreate($save, 'mail_send_tbl');
      $stmt = $this->prepare($q['query']);
      if(!$stmt->execute($q['params'])){
        $this->rollBack();
      }
      $ml = new mailRepository;
      $ml->setSiteId($site_id);
      $ml->setToMail($to);
      $ml->setFromMail($from);
      $ml->setSubject($template->subject);
      $ml->setBody($body);
      $result = $ml->sendSwift();
      if(!$result->get_status()){
        return $result;
      }
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    $this->set_status(true);
    return $this;
  }
  //フィルター（仮）再パスワード申請
  public function filterTemporaryPassword() :object{
    $ut = new utilityRepository;
    if(!$this->getSiteId()){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    $post = $this->getPostMember();
    $this->set_status(false);
    if(!$post){
      $this->set_message('入力がありません。');
      return $this;
    }
    if(!$post['email_address']){
      $this->set_message('アカウントをご確認下さい。');
      $this->set_invalid('email_address', '必須となります。');
      return $this;
    }
    if(!$ut->validate_token($post['token']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
      $this->set_message('トークンが発行されませんでした、再度お試しください。');
      return $this;
    }
    $m = new memberRepository;
    $m->setSiteId($this->getSiteId());
    $m->setEmailAddress($post['email_address']);
    $m->setStatusKbn(1);
    $m->setLimit(1);
    $get_member = $m->getMember();
    if($get_member->rowNumber != 1){
      $this->set_message('アカウント（メールアドレス）をご確認下さい。');
      return $this;
    }
    $this->setId($get_member->row[0]->id);
    $this->setEmailAddress($post['email_address']);
    $this->set_status(true);
    return $this;
  }
  
  //（本）再パスワード申請
  public function setRepassword(array $post){
    $this->setPostMember($post);
    $this->filterRepassword();
    if(!$this->_status || $this->_message || $this->_invalid){
      return $this;
    }
    $this->set_status(false);
    $id = $this->getId();
    $site_id = $this->getSiteId();
    $password = $this->getPassword();
    if(!$id || !$site_id || !$password){
      $this->set_message('不明な番号です。');
      return $this;
    }
    $this->connect();
    $this->beginTransaction();
    try {
      $push = array(
        'id' => $id,
        'site_id' => $site_id,
        'status_kbn' => 1,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'temporary_password' => null,
        'temporary_password_date' => null,
      );
      $query = $this->queryCreate($push, 'member_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    $this->set_status(true);
    return $this;
  }
  //フィルター（本）再パスワード申請
  public function filterRepassword() :object{
    $post = $this->getPostMember();
    $this->set_status(false);
    $ut = new utilityRepository;
    if(!$this->getSiteId()){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    if(!$post){
      $this->set_message('入力がありません。');
      return $this;
    }
    if(!$ut->mbStrLenCheck($post['temporary_password'], 30, 30)){
      $this->set_message('仮パスワードをご確認下さい。');
      $this->set_invalid('temporary_password', '必須となります。');
      return $this;
    }
    if(!$ut->mbStrLenCheck($post['password'], 8, 16)){
      $this->set_message('パスワードをご確認下さい。');
      $this->set_invalid('password', '8文字以上16文字までとなります。');
      return $this;
    }
    if($post['password'] != $post['_password']){
      $this->set_message('確認用パスワードをご確認下さい。');
      $this->set_invalid('_password', '確認用パスワードが一致しません。');
      return $this;
    }
    if($post['password']){
      $post['old_member'] = null;//パスワードが変更されたら新会員とする
    }
    if(!$ut->validate_token($post['token']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
      $this->set_message('トークンが発行されませんでした、再度お試しください。');
      return $this;
    }
    $m = new memberRepository;
    $m->setSiteId($this->getSiteId());
    $m->setTemporaryPassword($post['temporary_password']);
    $m->setTemporaryPasswordDate(date('Y-m-d H:i:s'));
    $m->setStatusKbn(1);
    $m->setLimit(1);
    $get_member = $m->getMember();
    if($get_member->rowNumber != 1){
      $this->set_message('有効期限切れ、または終了しました。');
      return $this;
    }
    $this->setId($get_member->row[0]->id);
    $this->setPassword($post['password']);
    $this->set_status(true);
    return $this;
  }

  //退会
  public function setDelete(array $post) {
    $this->setPostMember($post);
    $this->filterDelete();
    if(!$this->_status || $this->_message || $this->_invalid){
      return $this;
    }
    $id = $this->getId();
    $site_id = $this->getSiteId();
    if(!$id || !$site_id){
      $this->set_message('不明な番号です。');
      return $this;
    }
    $this->connect();
    $this->beginTransaction();
    try {
      $push = array(
        'id' => $id,
        'site_id' => $site_id,
        'delete_kbn' => 1
      );
      $query = $this->queryCreate($push, 'member_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    $this->setLogout();
    $this->set_status(true);
    return $this;
  }
  //フィルター退会
  public function filterDelete() :object{
    $this->set_status(false);
    $ut = new utilityRepository;
    if(!$this->getSiteId()){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    $member = $this->getSession();
    if(!get_object_vars($member)){
      $this->set_message('ログイン情報が見つかりません。');
      return $this;
    }
    $post = $this->getPostMember();
    $this->set_status(false);
    if(!$post){
      $this->set_message('入力がありません。');
      return $this;
    }
    if(!$post['delete_kbn']){
      $this->set_message('削除キーご確認下さい。');
      $this->set_invalid('delete_kbn', '必須となります。');
    }
    if(!$ut->validate_token($post['token']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
      $this->set_message('トークンが発行されませんでした、再度お試しください。');
      return $this;
    }
    $this->setId($member->id);
    $this->set_status(true);
    return $this;
  }
  
  //api ゼウス
  public function setApi(array $post) {
    switch($post['api']){
      case 'change_paymentZeusSecureLink' :        
        $pzsl = new paymentZeusSecureLinkRepository;
        $pzsl->setAuthPost($post);
        $getToken = $pzsl->getToken();
        if((isset($getToken->_status) && !$getToken->_status) || 
           (isset($getToken->result->status) && $getToken->result->status != "success")){
          return $getToken;
        }
        
        $post['zeus_token_value'] = $getToken->result->token_key;
        $post['amount'] = 0;
        $pzsl->setSendPost($post);
        $getSend = $pzsl->send();
        return $getSend;
        break;
    }
    return false;
  }
  
  //順位更新
  public function update(){
    $ut   = new utilityRepository;
    $push = $this->getPostMember();
    $this->set_status(false);
    $push['site_id'] = $this->getSiteId();
    $push['id']      = $this->getId();
    
    if(!$push['site_id']){
      $this->set_message('サイトが見つかりません。');
      return $this;
    }
    if(!$push['id']){
      $this->set_message('会員番号が見つかりません。');
      return $this;
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
      $query = $this->queryCreate($push, 'member_tbl');
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