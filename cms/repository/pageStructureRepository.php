<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\fieldRepository;
use host\cms\repository\pageRepository;

class pageStructureRepository extends dbRepository {
  
  use \host\cms\entity\pageStructureEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("ps.*, a.name as account_name, m.module_theme, m.module_type, m.template");
    if(self::getBackupTableFlg()){
      //バックアップデータ参照
      self::setFrom("page_structure_bk ps 
                    LEFT JOIN m_page_module_tbl m ON ps.module_id = m.id 
                    LEFT JOIN account_tbl a ON ps.account_id = a.id");
      self::setOrder("ps.update_date DESC");
      if($bk_id = self::getBkId()){
        self::setWhere("ps.bk_id = :bk_id");
        self::setValue(":bk_id", $bk_id);
      }
    }else{
      //通常参照
      self::setFrom("page_structure_tbl ps 
                    LEFT JOIN m_page_module_tbl m ON ps.module_id = m.id 
                    LEFT JOIN account_tbl a ON ps.account_id = a.id");
      self::setWhere("ps.delete_kbn IS NULL");
      self::setOrder("ps.rank ASC");
    }
    if($id = self::getId()){
      self::setWhere("ps.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("ps.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($navigation_id = self::getNavigationId()){
      self::setWhere("ps.navigation_id = :navigation_id");
      self::setValue(":navigation_id", $navigation_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("ps.release_kbn = :release_kbn AND (ps.release_start_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') >= DATE_FORMAT(ps.release_start_date, '%Y-%m-%d')) AND (ps.release_end_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') <= DATE_FORMAT(ps.release_end_date, '%Y-%m-%d'))");
      self::setValue(":release_kbn", $release_kbn);
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
      $fieldColumns = self::getExplodeColumns();//fieldカラムを取得
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->htmlparts = json_decode($d->htmlparts, true);//array
        $d->data = new \StdClass();
        if($d->o_navigation_id){
          $page = new pageRepository;
          $page->setSiteId($d->site_id);
          $page->setNavigationId($d->o_navigation_id);
          if($preview_page_post = self::getPreviewPagePost()){
            $page->setPreviewPost($preview_page_post);//途中プレビューページポスト値を取得
          }else{
            $page->setReleaseKbn(1);
          }
          $d->data = $page->get();
        }
        $this->row[] = $d;
      }
      if($preview_structure_post = self::getPreviewStructurePost()){
        //途中プレビューストラクチャポスト値に差し替える
        $this->setPost($preview_structure_post, 'diff');
        $post = $this->getPost();
        if(!$site_id || !$post['navigation_id']){
          return false;
        }
        $object = (object) $post;
        if(!$post['id']){
          array_unshift($this->row, $object);//新規投稿なら最初の配列へ
        }else{
          foreach($this->row as $key => $row){
            if($row->id == $post['id']){
              $this->row[$key] = $object;
            }
          }
        }
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
    if(!$push['navigation_id']){
      $this->set_message('ナビゲーションを選択してください。');
    }
    if(!$ut->mbStrLenCheck($push['name'], 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
    }
    if($push['release_start_date'] && !$ut->dateCheck($push['release_start_date'])){
      $this->set_invalid('release_start_date', '公開開始日を確認して下さい。');
    }
    if($push['release_end_date'] && !$ut->dateCheck($push['release_end_date'])){
      $this->set_invalid('release_end_date', '公開終了日を確認して下さい。');
    }
    if($push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    unset($push['token']);
    
    if(isset($push['htmlparts'])){
      $push['htmlparts'] = json_encode( $push['htmlparts'], JSON_UNESCAPED_UNICODE);
    }

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'page_structure_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE page_structure_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND navigation_id = :navigation_id AND delete_kbn IS NULL");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $push['site_id'], 
          ':navigation_id'=> $push['navigation_id']
        ])){
          $this->rollBack();
        }
      }
      
      //複製
      $stmt = $this->prepare("INSERT INTO page_structure_bk ( id, site_id, navigation_id, module_id, account_id, rank, release_kbn, release_start_date, release_end_date, name, html, htmlparts, o_navigation_id, meta_description, meta_keywords, delete_kbn, update_date, created_date ) ( SELECT * FROM page_structure_tbl WHERE id = :id )");
      if(!$stmt->execute([
        ':id'=> $this->_lastId
      ])){
        $this->rollBack();
      }
      
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    //キャッシュクリア
    $ut->smartyClearAllCache();
    $this->set_status(true);
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
      $query = $this->queryCreate($push, 'page_structure_tbl');
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
    //キャッシュクリア
    $ut->smartyClearAllCache();
    return $this;
  }
  
  public function preview(){
    $site = $_SESSION['site'];
    $siteUri = $this->getSiteUri();
    if(!$site || !$siteUri){
      print "サイト情報がありません";
      return false;
    }
    $siteController = glob(DIR_HOST.'controller/*.php');
    foreach($siteController as $file){
      require_once($file);
    }
    $index = new \controller\indexController;
    if($id = $this->getId()){
      $index->setStructureId($id);
    }
    if($module_id = $this->getModuleId()){
      $index->setModuleId($module_id);
    }
    $index->setUri($siteUri);
    $index->setPreviewFlg(1);
    if($preview_structure_post = self::getPreviewStructurePost()){
      $index->setPreviewStructurePost($preview_structure_post);//途中プレビューストラクチャポスト値
    }
    $index->setDesignAuthority($site->design_authority);//デザイン権限が必要
    return $index->indexAction();
  }
}
// ?>