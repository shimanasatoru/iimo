<?php 
namespace repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class accountRepository extends dbRepository {
  
  use \entity\accountEntity;
  
  /*
   * ログイン認証
   * @return object array mixed $this
   */
  public function auth(array $post){
    $push  = filter_var_array($post, [
      'token' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'auth' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'account' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'password' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'recaptchaToken' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ]
    ]);
    $ut = new utilityRepository;
    if(!$ut->validate_token($push['token']) || $_SERVER['REQUEST_METHOD'] != 'POST'){
      $this->set_message('再度お試しください。');
    }
    if(!$push['account'] || !$push['password']){
      $this->set_message('アカウントまたはパスワードが正しくありません。E0101');
    }
    //gooogle recapcha v3
    if(G_RECAPTCHA_V3_SITEKEY && G_RECAPTCHA_V3_SECRETKEY){
      if(!$push['recaptchaToken']){
        $this->set_message('トークン(recaptcha)が発行されないため、送信出来ません。');
        return $this;
      }
      $url = 'https://www.google.com/recaptcha/api/siteverify';
      $data = array(
        'secret' => G_RECAPTCHA_V3_SECRETKEY,
        'response' => $push['recaptchaToken']
      );
      $context = array(
        'http' => array(
          'method'  => 'POST',
          'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
          'content' => http_build_query($data)
        )
      );
      $result = file_get_contents($url, false, stream_context_create($context));
      $chk = json_decode($result);
      if ($chk->success != true || $chk->score <= 0.5){//スコア0.5以下は終了
        $this->set_message('トークンB(recaptcha)が発行されないため、送信出来ません。');
        return $this;
      }
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $account = new accountRepository;
    $account->setAccount($push['account']);
    $account->setLimit(1);
    $account->get();
    if($account->rowNumber != 1){
      $this->set_message('アカウントまたはパスワードが正しくありません。E0102');
      return $this;
    }
    
    $a = $account->row[0];
    if( !password_verify($push['password'], isset($a->password) ? $a->password
        : '$2y$10$abcdefghijklmnopqrstuv' // ユーザ名が存在しないときだけ極端に速くなるのを防ぐ
    )){
      $this->set_message('アカウントまたはパスワードが正しくありません。E0103');
      return $this;
    }
    
    // セッションIDの追跡を防ぐ
    // セッション自身はオブジェクト化できない、またはしない方がよい
    session_regenerate_id(true);
    $a->authcode = "manage";
    $a->password = true;
    $_SESSION['user'] = $a;

    $user = filter_var_array( $_SERVER, [
      'HTTP_CONNECTION' => FILTER_SANITIZE_SPECIAL_CHARS,
      'HTTP_CACHE_CONTROL' => FILTER_SANITIZE_SPECIAL_CHARS,
      'HTTP_UPGRADE_INSECURE_REQUESTS' => FILTER_SANITIZE_SPECIAL_CHARS,
      'HTTP_USER_AGENT' => FILTER_SANITIZE_SPECIAL_CHARS,
      'HTTP_ACCEPT' => FILTER_SANITIZE_SPECIAL_CHARS,
      'HTTP_ACCEPT_ENCODING' => FILTER_SANITIZE_SPECIAL_CHARS,
      'HTTP_ACCEPT_LANGUAGE' => FILTER_SANITIZE_SPECIAL_CHARS,
      'HTTP_COOKIE' => FILTER_SANITIZE_SPECIAL_CHARS,
      'PATH' => FILTER_SANITIZE_SPECIAL_CHARS,
      'REMOTE_ADDR' => FILTER_SANITIZE_SPECIAL_CHARS
    ]);
    $time = new \DateTime();
    $user['account_id'] = $a->id;
    $q = $this->queryCreate($user, 'login_log_tbl');
    
    try {
      $this->connect();
      $this->beginTransaction();
      $stmt = $this->prepare($q['query']);
      if(!$stmt->execute($q['params'])){
        $this->rollBack();
      }
      $this->_lastId = $this->lastInsertId();
      $this->set_message('ログインしました');
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      exit($e->getMessage());
    }
    $this->commit();
    return $this;
  }

  public function get() {
    self::setSelect("
    a.*, 
    s.site_id,
    s.site_release_kbn,
    s.site_release_start_date,
    s.site_release_end_date,
    s.site_name,
    s.site_directory,
    s.design_theme,
    s.site_count
    ");
    self::setFrom("account_tbl a 
    LEFT JOIN ( 
      SELECT 
        sta.account_id,
        GROUP_CONCAT(COALESCE(s.id, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS site_id,
        GROUP_CONCAT(COALESCE(s.release_kbn, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS site_release_kbn,
        GROUP_CONCAT(COALESCE(s.release_start_date, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS site_release_start_date,
        GROUP_CONCAT(COALESCE(s.release_end_date, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS site_release_end_date,
        GROUP_CONCAT(COALESCE(s.name, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS site_name,
        GROUP_CONCAT(COALESCE(s.directory, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS site_directory,
        GROUP_CONCAT(COALESCE(s.design_theme, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS design_theme,
        count(s.id) AS site_count 
      FROM site_to_account_tbl sta
      LEFT JOIN site_tbl s ON sta.site_id = s.id 
      WHERE sta.delete_kbn IS NULL AND s.delete_kbn IS NULL GROUP BY sta.account_id 
    ) s ON a.id = s.account_id 
    ");
    self::setExplodeColumns([
      "site_id", 
      "site_release_kbn",
      "site_release_start_date",
      "site_release_end_date",
      "site_name",
      "site_directory",
      "design_theme"
    ]);
    self::setWhere("a.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("a.id = :id");
      self::setValue(":id", $id);
    }
    if($ac = self::getAccount()){
      self::setWhere("a.account = :account");
      self::setValue(":account", $ac);
    }
    if($pe = self::getPermissionsArray()){
      $in = null;
      foreach($pe as $key_pe => $array_pe){
        $in.= ($in ? "," : "") . ":permissions{$key_pe}";
        self::setValue(":permissions{$key_pe}", $array_pe);
      }
      if($in){
        self::setWhere("a.permissions IN ({$in})");
      }
    }
    if($id_parent_id = self::getIdOrParentId()){
      self::setWhere("(a.id = :id_parent_id OR a.parent_id = :id_parent_id)");
      self::setValue(":id_parent_id", $id_parent_id);
    }
    if($keyword = self::getKeyword()){
      $where;
      foreach($keyword as $i => $word){
        $where.= ($where ? " OR " : "") . "(a.account LIKE :key{$i} OR a.name LIKE :key{$i})";
        self::setValue(":key{$i}", "%{$word}%");
      }
      self::setWhere($where);
    }
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
        $d = self::filterExplode($d);
        $d = self::filterJsonDecode($d);
        $d->permissions_obj = new \StdClass;
        if($this->permissions->{$d->permissions}){
          $d->permissions_obj = $this->permissions->{$d->permissions};
        }
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
    $ut  = new utilityRepository;
    if(!$ut->mbStrLenCheck($push['account'], 8, 16)){
      $this->set_invalid('account', 'アカウントは8文字～16文字となります。');
    }else{
      //アカウント重複チェック
      if(!$push['id'] && !$this->accountDuplicateCheck($push['account'])){
        $this->set_invalid('account', 'アカウント名は既に存在しています。');
      }
    }
    if(!$push['id'] && !$ut->mbStrLenCheck($push['password'], 8, 16)){
      $this->set_invalid('password', 'パスワードは8文字～16文字となります。');
    }
    //パスワードが入力されていたら暗号化処理
    if($push['password']){
      $push['password'] = password_hash($push['password'], PASSWORD_DEFAULT);
    }else{
      unset($push['password']);
    }
    if(!$push['name'] && !$ut->mbStrLenCheck($push['name'], 1, 20)){
      $this->set_invalid('password', 'お名前は1文字～20文字となります。');
    }
    if(!$push['token'] || $push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    unset($push['token']);
    
    $this->connect();
    $this->beginTransaction();
    try {
      $time = new \DateTime();
      $query = $this->queryCreate($push, 'account_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      exit($e->getMessage());
    }
    $this->commit();
    return $this;
  }
  
  /*
   * アカウント重複チェック
   * @return boolean
   */
  public function accountDuplicateCheck($account){
    $ut = new utilityRepository;
    $val_account = filter_var($account, FILTER_SANITIZE_SPECIAL_CHARS);
    if(!$ut->mbStrLenCheck($val_account, 8, 16)){
      return false;
    }
    
    $ac = new accountRepository;
    $ac->setAccount($val_account);
    $ac->setLimit(1);
    $get_account = $ac->get();
    if($get_account->rowNumber > 0){
      return false;
    }
    return true;
  }
  
}
// ?>