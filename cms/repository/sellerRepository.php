<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\sitesRepository;
use repository\accountRepository;

class sellerRepository extends dbRepository {
  
  use \host\cms\entity\sellerEntity;
  
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
      'account' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'password' => [
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
    if($this->_message || $this->_invalid){
      return $this;
    }

    $seller = new sellerRepository;
    $seller->setOpenPassword(1);
    $seller->setAccount($push['account']);
    $seller->setLimit(1);
    $seller->get();
    if($seller->rowNumber != 1){
      $this->set_message('アカウントまたはパスワードが正しくありません。E0102');
      return $this;
    }
    
    $u = $seller->row[0];
    if( !password_verify($push['password'], isset($u->password) ? $u->password
        : '$2y$10$abcdefghijklmnopqrstuv' // ユーザ名が存在しないときだけ極端に速くなるのを防ぐ
    )){
      $this->set_message('アカウントまたはパスワードが正しくありません。E0103');
      return $this;
    }
    if(!$u->site_id){
      $this->set_message('所属サイトがみつかりません。E0104');
      return $this;
    }
    
    $sites = new sitesRepository;
    $sites->setId($u->site_id);
    $sites->setLimit(1);
    $si = $sites->get();
    if($si->rowNumber != 1){
      $this->set_message('所属サイトがみつかりません。E0105');
      return $this;
    }
    
    // セッションIDの追跡を防ぐ
    // セッション自身はオブジェクト化できない、またはしない方がよい
    session_regenerate_id(true);
    $u->authcode = "seller";
    $u->password = true;
    $_SESSION['user'] = $u;
    $_SESSION['site'] = $si->row[0];
    $_SESSION['cms'] = $_SESSION['KCFINDER'] = new \stdClass;

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
    $user['seller_id'] = $u->id;
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
    self::setSelect("se.*, pref.name as prefecture_name");
    self::setFrom("seller_tbl se LEFT JOIN m_prefectures_tbl pref ON se.prefecture_id = pref.id");
    self::setWhere("se.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("se.id = :id");
      self::setValue(":id", $id);
    }
    if($ac = self::getAccount()){
      self::setWhere("se.account = :account");
      self::setValue(":account", $ac);
    }
    if($keyword = self::getKeyword()){
      $where;
      foreach($keyword as $i => $word){
        $where.= ($where ? " OR " : "") . "(se.account LIKE :key{$i} OR se.name LIKE :key{$i})";
        self::setValue(":key{$i}", "%{$word}%");
      }
      self::setWhere($where);
    }
    if($status = self::getStatus()){
      self::setWhere("se.status = :status");
      self::setValue(":status", $status);
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
        if(!$this->getOpenPassword() && $d->password){
          $d->password = true;
        }
        $d->status_obj = $this->status_value[$d->status];
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
    if(!$push['site_id']){
      $this->set_message('サイトが選択されていません。');
    }
    if(!$push['name'] && !$ut->mbStrLenCheck($push['name'], 1, 20)){
      $this->set_invalid('password', '担当者名は1文字～20文字となります。');
    }
    if(!$ut->mbStrLenCheck($push['account'], 8, 20)){
      $this->set_invalid('account', 'アカウントは8文字～20文字となります。');
    }else{
      //アカウント重複チェック
      if(!$push['id'] && !$this->sellerDuplicateCheck($push['account'])){
        $this->set_invalid('account', 'アカウント名は既に存在しています。');
      }
    }
    if(!$push['id'] && !$ut->mbStrLenCheck($push['password'], 8, 20)){
      $this->set_invalid('password', 'パスワードは8文字～20文字となります。');
    }
    //パスワードが入力されていたら暗号化処理
    if($push['password']){
      $push['password'] = password_hash($push['password'], PASSWORD_DEFAULT);
    }else{
      unset($push['password']);
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
      $query = $this->queryCreate($push, 'seller_tbl');
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
   * アカウントセラー重複チェック
   * @return boolean
   */
  public function sellerDuplicateCheck($account){
    $ut = new utilityRepository;
    $val_account = filter_var($account, FILTER_SANITIZE_SPECIAL_CHARS);
    if(!$ut->mbStrLenCheck($val_account, 8, 20)){
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