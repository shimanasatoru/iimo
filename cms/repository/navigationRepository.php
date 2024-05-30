<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\pageRepository;

class navigationRepository extends dbRepository {
  
  use \host\cms\entity\navigationEntity;
  /*
   * ツリー出力 再帰
   * @param array $list
   * @return array
   */
  public $target_row;
  public function tree(&$list, $parent, $level = 0, $dirPath = array()){
    $tree = array();
    foreach ($parent as $key=>$value){
      if($level > 0){
        $dirPath[$level] = $value->directory_name;
      }
      if(isset($list[$value->id])){
        $value->children = $this->tree($list, $list[$value->id], $level + 1, $dirPath);
      }
      if($level > 0 && $value->format_type != 'link'){
        $value->uri .= implode('/', $dirPath);
        $value->url .= implode('/', $dirPath);
      }
      if(self::getId() && self::getId() == $value->id){
        $this->target_row = $value;
      }
      $tree[] = $value;
    }
    return $tree;
  }
  /*
   * 全件取得
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("na.*, si.directory as site_directory, si.domain");
    self::setFrom("navigation_tbl na LEFT JOIN site_tbl si ON si.id = na.site_id");
    self::setWhere("na.delete_kbn IS NULL AND si.delete_kbn IS NULL");
    self::setOrder("na.rank ASC");
    if($site_id = self::getSiteId()){
      self::setWhere("na.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("(na.release_kbn = :release_kbn1 OR na.release_kbn = :release_kbn2) AND (na.release_start_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') >= DATE_FORMAT(na.release_start_date, '%Y-%m-%d')) AND (na.release_end_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') <= DATE_FORMAT(na.release_end_date, '%Y-%m-%d'))");
      self::setValue(":release_kbn1", 1);
      self::setValue(":release_kbn2", 3);
    }
    if($release_password = self::getReleasePassword()){
      self::setWhere("na.release_password = :release_password");
      self::setValue(":release_password", $release_password);
    }
    $q = self::getSelect().
          self::getFrom().
          self::getWhere().
          self::getGroupBy().
          self::getOrder().
          self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      $array = array();
      $new = array();
      $password = $this->getReleasePasswordStatusIds();
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->release_password_status = null;
        if($d->release_kbn == 3){
          if($password && $password[$d->id]){
            $d->release_password_status = true;
          }else{
            $d->release_password_status = false;
          }
        }
        if($d->format_type != 'link'){
          $d->url = $d->domain.'/';
          $d->uri = DIR_SITE.($d->site_directory ? $d->site_directory . '/' : null);
          if(!$d->domain){
            $d->url = ADDRESS_SITE.($d->site_directory ? $d->site_directory . '/' : null);
          }
        }
        if(!$d->directory_name){
          $d->directory_name = $d->id;
        }
        $array[] = $d;
        $new[$d->parent_id][] = $d;
      }
      $ut = new utilityRepository;
      $this->row = $this->tree($new, array($array[0]));
      if($id = self::getId()){
        //ID指定の場合、URL成型が必要なため全件取得後、抜き出す
        $this->row[0] = $this->target_row;
        $this->rowNumber = 1;
      }else{
        $this->rowNumber = $stmt->rowCount();
      }
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
  
  /*
   * リリース認証
   * @return object array mixed $this
   */
  public function releaseOauth() {
    $id = $this->getReleaseId();
    $password = $this->getReleasePassword();
    if($id && $password){
      $this->setId($id);
      $this->setReleasePassword($password);
      if($data = $this->get()->row[0]){
        $this->setReleasePasswordStatusId($id, true);
        $this->set_status(true);
      }else{
        $this->setReleasePasswordStatusId($id, false);
      }
    }else{
      $this->setReleasePasswordStatusId($id, false);
    }
    return $this;
  }

  /*
   * バックアップ全件取得
   * @return object array mixed $this
   */
  public function getBk() {
    self::setSelect("na.*");
    self::setFrom("navigation_bk na");
    self::setWhere("na.delete_kbn IS NULL");
    self::setOrder("na.update_date DESC");
    if($site_id = self::getSiteId()){
      self::setWhere("na.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($id = self::getId()){
      self::setWhere("na.id = :id");
      self::setValue(":id", $id);
    }
    $q = self::getSelect().
          self::getFrom().
          self::getWhere().
          self::getGroupBy().
          self::getOrder().
          self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
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
  /*
   * 編集権限
   * @return object array mixed $this
   */
  public function getAccount() {
    self::setSelect("nta.*, ac.account, ac.name as account_name");
    self::setFrom("navigation_to_account_tbl nta LEFT JOIN account_tbl ac ON nta.account_id = ac.id");
    self::setWhere("nta.delete_kbn IS NULL AND ac.delete_kbn IS NULL");
    self::setOrder("nta.rank ASC");
    if($navigation_id = self::getNavigationId()){
      self::setWhere("nta.navigation_id = :navigation_id");
      self::setValue(":navigation_id", $navigation_id);
    }
    if($account_id = self::getAccountId()){
      self::setWhere("nta.account_id = :account_id");
      self::setValue(":account_id", $account_id);
    }
    $q = self::getSelect().
          self::getFrom().
          self::getWhere().
          self::getGroupBy().
          self::getOrder().
          self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      $stmt->execute(self::getValue());
      $this->rowNumber = $stmt->rowCount();
      if($this->rowNumber > 0){
        $this->set_status(true);
      }
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $this->row[] = $d;
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
    if(!$push->data->site_id){
      $this->set_message('サイトを選択してください。');
    }
    if(!$ut->mbStrLenCheck($push->data->name, 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
    }
    if($push->data->release_start_date && !$ut->dateCheck($push->data->release_start_date)){
      $this->set_invalid('release_start_date', '公開開始日を確認して下さい。');
    }
    if($push->data->release_end_date && !$ut->dateCheck($push->data->release_end_date)){
      $this->set_invalid('release_end_date', '公開終了日を確認して下さい。');
    }
    if($push->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    
    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push->data, 'navigation_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push->data->id ? $push->data->id : $this->lastInsertId();
      if(!$push->data->id && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        //※ナビゲーションはツリー型のため「rank[0] == 'トップページ'」とする！
        $stmt = $this->prepare("UPDATE navigation_tbl SET rank = rank + 1 WHERE parent_id != 0 AND site_id = :site_id AND delete_kbn IS NULL");
        if(!$stmt->execute([':site_id'=> $push->data->site_id])){
          $this->rollBack();
        }
        //カスタムフィールドに項目：内容を追加
        $stmt = $this->prepare("INSERT page_content_field_tbl (site_id, navigation_id, rank, release_kbn, name, field_type) VALUES (:site_id, :navigation_id, :rank, :release_kbn, :name, :field_type)");
        if(!$stmt->execute([':site_id'=> $push->data->site_id, ':navigation_id'=> $this->_lastId, ':rank'=> 0, ':release_kbn'=> 1, ':name'=> '内容', ':field_type'=> 'textarea_ckeditor'])){
          $this->rollBack();
        }
      }
      
      //アカウント操作
      if($push->accounts){
        $q_accounts = $this->createQueryNavigationToAccount();
        if($q_accounts['query']){
          foreach($q_accounts['query'] as $k => $query){
            $stmt = $this->prepare($query);
            if(!$stmt->execute($q_accounts['params'][$k])){
              $this->rollBack();
            }
          }
        }
      }

      //複製
      $stmt = $this->prepare("INSERT INTO navigation_bk ( id, parent_id, site_id, account_id, rank, release_kbn, release_start_date, release_end_date, release_password, format_type, format_id, name, catch, comment, meta_description, meta_keywords, directory_name, directory_path, template_name, url, page_limit, delete_kbn, update_date, created_date) ( SELECT * FROM navigation_tbl WHERE id = :id )");
      if(!$stmt->execute([
        ':id'=> $this->_lastId
      ])){
        $this->rollBack();
      }

      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    
    //サイトマップ、URLを生成
    $this->setSiteId($push->data->site_id);
    $this->directoryPathCreate();
    //キャッシュクリア
    $ut->smartyClearAllCache();
    return $this;
  }
  
  public function update(){
    $push = $this->getPost();
    $ut   = new utilityRepository;
    if(!$push->data->id){
      $this->set_message('IDが取得できません。');
    }
    if(!$push->data->site_id){
      $this->set_message('サイトを選択してください。');
    }
    if($push->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push->data, 'navigation_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push->data->id ? $push->data->id : $this->lastInsertId();
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    
    //サイトマップ、URLを生成
    $this->setSiteId($push->data->site_id);
    $this->directoryPathCreate();
    //キャッシュクリア
    $ut->smartyClearAllCache();    
    return $this;
  }
  
  public function updateAccountToNavigation(){
    $push = $this->getPost();
    $ut   = new utilityRepository;
    if(!$push->data->site_id){
      $this->set_message('サイトを選択してください。');
    }
    if(!array_filter($push->accounts->navigation_id) || !array_filter($push->accounts->account_id)){
      $this->set_message('IDが取得できません。');
    }
    if($push->token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    $this->connect();
    $this->beginTransaction();
    try {
      foreach($push->accounts->navigation_id as $k => $navigation_id){
        $query = 'INSERT INTO navigation_to_account_tbl ( 
          site_id, navigation_id, account_id, delete_kbn
        ) VALUES (
          :site_id, :navigation_id, :account_id, :delete_kbn
        ) ON DUPLICATE KEY UPDATE 
          delete_kbn = :delete_kbn';
        $params = [
          ':site_id' => $push->data->site_id,
          ':navigation_id' => $navigation_id,
          ':account_id'=> $push->accounts->account_id[$k],
          ':delete_kbn' => $push->accounts->delete_kbn[$k]
        ];
        $stmt = $this->prepare($query);
        if(!$stmt->execute($params)){
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

  public function createQueryNavigationToAccount(){
    $push = $this->getPost();
    $query = array();
    $params = array();
    if($push->accounts->account_id){
      $rank = 0;
      foreach($push->accounts->account_id as $k => $account_id){
        $query[] = 'INSERT INTO navigation_to_account_tbl ( 
          site_id, navigation_id, account_id, rank, delete_kbn
        ) VALUES (
          :site_id, :navigation_id, :account_id, :rank, :delete_kbn
        ) ON DUPLICATE KEY UPDATE 
          rank = :rank, delete_kbn = :delete_kbn';
        $params[] = [
          ':site_id' => $push->data->site_id,
          ':navigation_id' => $this->_lastId,
          ':account_id'=> $account_id,
          ':rank' => $rank,
          ':delete_kbn' => $push->accounts->delete_kbn[$k]
        ];
        $rank++;
      }
    }
    return array('query' => $query, 'params' => $params);
  }

  public function treeDecode($data){
    $tree = array();
    if($data){
      foreach($data as $d){
        $d->page_data = array();
        if($d->id && $d->url){
          $pages = new pageRepository;
          $pages->setNavigationId($d->id);
          $pages->setPageUrl($d->url);
          $d->page_data = $pages->get()->row;
        }
        
        $tree[] = $d;
        if(property_exists($d, "children") && $d->children){
          $tree = array_merge($tree, $this->treeDecode($d->children));
          unset($d->children);
        }
      }
    }
    return $tree;
  }

  public function sitemapCodeUrlCreate($url, $update_date){
    //(&)はエラーとなるため、一旦&に戻して再置換
    $url = preg_replace('/&/', '&amp;', preg_replace('/&amp;/', '&', $url));
    $update_date = date("c", strtotime($update_date));
    return 
      "<url>".
        "<loc>{$url}</loc>".
        "<lastmod>{$update_date}</lastmod>".
      "</url>";
  }
  
  public function sitemapCreate(){
    if(!$this->getSiteId()){
      return false;
    }
    $data = $this->get()->row;
    if(!$data[0]){
      return false;
    }
    if(!$data[0]->uri){
      return false;
    }

    $upload_dir = $data[0]->uri;
    $file_name = "sitemap.xml";
    $code = '<?xml version="1.0" encoding="UTF-8"?>'.
      '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '.
      'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
      'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 '.
      'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
    if($decode = $this->treeDecode($data)){
      foreach($decode as $d){
        //リンクフォーマットは除外
        if($d->format_type == "link"){
          continue;
        }
        $code.= $this->sitemapCodeUrlCreate($d->url, $d->update_date);
        if($d->page_data){
          foreach($d->page_data as $p){
            $code.= $this->sitemapCodeUrlCreate($p->page_url, $p->update_date);
          }
        }
      }
    }
    $code.= '</urlset>';
    if(file_put_contents($upload_dir.$file_name, $code)){
      return true;
    }
    return false;
  }

  /*
   * ここで全件のURLを生成する（※ここ以外では行わない）
   */
  public function directoryPathTree(&$list, $parent, $level = 0, $dirPath = array()){
    $tree = array();
    foreach ($parent as $key=>$value){
      if($level > 0){
        $dirPath[$level] = $value->directory_name;
      }
      if(isset($list[$value->id])){
        $value->children = $this->directoryPathTree($list, $list[$value->id], $level + 1, $dirPath);
      }
      if($level > 0 && $value->format_type != 'link'){
        $value->directory_path .= implode('/', $dirPath);
      }
      if(self::getId() && self::getId() == $value->id){
        $this->target_row = $value;
      }
      $tree[] = $value;
    }
    return $tree;
  }
  public function directoryPathCreate(){
    if(!$this->getSiteId()){
      return false;
    }

    $query = "SELECT na.*, si.directory as site_directory, si.domain  
              FROM navigation_tbl na LEFT JOIN site_tbl si ON si.id = na.site_id 
              WHERE na.delete_kbn IS NULL AND si.delete_kbn IS NULL AND na.site_id = :site_id 
              ORDER BY na.rank ASC
              ";
    try {
      self::connect();
      self::beginTransaction();
      $stmt = self::prepare($query);
      $stmt->bindParam(':site_id', $this->getSiteId(), \PDO::PARAM_INT);
      $stmt->execute();
      $array = array();
      $new = array();
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->directory_path = null;
        if(!$d->directory_name){
          $d->directory_name = $d->id;
        }
        $array[] = $d;
        $new[$d->parent_id][] = $d;
      }
      //URL生成
      $datas = $this->directoryPathTree($new, array($array[0]));
      $datas = $this->treeDecode($datas);

      foreach($datas as $data){
        $query = "UPDATE navigation_tbl SET directory_path = :directory_path 
                  WHERE id = :id AND site_id = :site_id";
        $stmt = self::prepare($query);
        $stmt->bindParam(':site_id', $data->site_id, \PDO::PARAM_INT);
        $stmt->bindParam(':id', $data->id, \PDO::PARAM_INT);
        $stmt->bindParam(':directory_path', $data->directory_path, \PDO::PARAM_STR);
        if(!$stmt->execute()){
          $this->rollBack();
        }
      }
      self::commit();

    } catch (\Exception $e) {
      $this->set_message($e->getMessage());
    }
    return true;
  }  

}
// ?>