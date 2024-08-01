<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\sitesRepository;
use host\cms\repository\fieldRepository;

use host\cms\repository\navigationRepository;

class pageRepository extends dbRepository {
  
  use \host\cms\entity\pageEntity;
  
  public function currentUpperDir($data, $current_id, $array = array()){
    if(!$data[$current_id] || !$data[$current_id]->parent_id || $current_id === 0){
      return $array;
    }
    
    $path = ($data[$current_id]->directory_name ? $data[$current_id]->directory_name : $data[$current_id]->id);
    if($data[$current_id]->page_id){
      $path = $path."/?id={$data[$current_id]->page_id}";
    }
    $array['name'] = $data[$current_id]->name . "/".$array['name'];
    $array['path'] = $path . ($array['path'] ? "/".$array['path'] : null);
    if($data[$current_id]->parent_id){
      return $this->currentUpperDir($data, $data[$current_id]->parent_id, $array);
    }
    return $array;
  }
  
  /*
   * 検索結果処理
   */
  public function getSearchResults() {
    $page_uri = self::getPageUrl();
    $keyword = self::getKeyword();
    $file_uri = $page_uri."sitesearch.json";
    if(!file_exists($file_uri) || !$keyword){
      return $this;
    }
    
    $keyword = preg_split("/( |　)+/", $keyword);
    $json = json_decode( file_get_contents($file_uri) );
    $this->row = array_filter($json, function($item) use ($keyword) {
      $item->directory_path_name_array = (array) $item->directory_path_name_array;
      foreach ($keyword as $value) {
        if (strpos($item->name, $value) !== false) {
          return true;
        }
        if (strpos($item->content, $value) !== false) {
          return true;
        }
      }
      return false;
    });
    $this->rowNumber = count($this->row);
    return $this;
  }
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("p.*, n.directory_name, n.directory_path, n.parent_id");
    self::setFrom("page_tbl p 
                  LEFT JOIN page_content_tbl c ON p.site_id = c.site_id AND p.id = c.page_id 
                  LEFT JOIN navigation_tbl n ON p.site_id = n.site_id AND p.navigation_id = n.id");
    self::setWhere("p.delete_kbn IS NULL AND c.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("p.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("p.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($navigation_id = self::getNavigationId()){
      self::setWhere("p.navigation_id = :navigation_id");
      self::setValue(":navigation_id", $navigation_id);
    }
    if($navigation_ids = self::getNavigationIds()){
      $value = null;
      foreach($navigation_ids as $i => $val){
        $value.= ($value ? ' OR ' : '') . "p.navigation_id = :navigation_ids{$i}";
        self::setValue(":navigation_ids{$i}", $val);
      }
      self::setWhere("({$value})");
    }
    if($format_type = self::getFormatType()){
      self::setWhere("n.format_type = :format_type");
      self::setValue(":format_type", $format_type);
    }
    if($content_id = self::getContentId()){
      self::setWhere("c.id = :content_id");
      self::setValue(":content_id", $content_id);
    }
    if($field_id = self::getFieldId()){
      self::setWhere("c.field_id = :field_id");
      self::setValue(":field_id", $field_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("p.release_kbn = :release_kbn AND (p.release_start_date IS NULL || now() >= p.release_start_date) AND (p.release_end_date IS NULL || now() <= p.release_end_date)");
      self::setValue(":release_kbn", $release_kbn);
    }
    
    $field_id = self::getFieldId();
    $content_value = self::getContentValue();
    if($field_id && $content_value){
      self::setWhere("c.field_id = :field_id AND c.value = :value");
      self::setValue(":field_id", $field_id);
      self::setValue(":value", $content_value);
    }
    $content_values = self::getContentValues();
    if($field_id && $content_values){
      $value = null;
      switch(self::getContentType()){
        case 'and':
          foreach($content_values as $i => $val){
            $value.= ($value ? ' AND ' : '') . "c.value LIKE :value{$i}";
            self::setValue(":value{$i}", "%\"{$val}\"%");
          }
          break;
        default:
          foreach($content_values as $i => $val){
            $value.= ($value ? ' OR ' : '') . "c.value LIKE :value{$i}";
            self::setValue(":value{$i}", "%\"{$val}\"%");
          }
          break;
      }
      self::setWhere("c.field_id = :field_id AND ({$value})");
      self::setValue(":field_id", $field_id);
    }
    self::setOrder("p.release_start_date DESC");
    self::setGroupBy("p.id");
    $q = self::getSelect()
        .self::getFrom()
        .self::getWhere()
        .self::getGroupBy()
        .self::getOrder()
        .self::getPageLimit();
    try {
      self::connect();
      $stmt = self::prepare($q);
      if(is_array(self::getValue())){
        foreach(self::getValue() as $name => $value){
          if(is_numeric($value)){
            $stmt->bindValue($name, $value, \PDO::PARAM_INT);
          }else{
            $stmt->bindValue($name, $value, \PDO::PARAM_STR);
          }
        }
      }
      $stmt->execute();
      
      $stmt_allNumber = $this->prepare("SELECT FOUND_ROWS() as `allNumber`");
      $stmt_allNumber->execute();
      while($d = $stmt_allNumber->fetch(\PDO::FETCH_OBJ)){
        $this->totalNumber = $d->allNumber;
      }
      $fieldColumns = self::getExplodeColumns();//fieldカラムを取得
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        //公開開始日キーとするため、なければ投稿日
        if(!$d->release_start_date){
          $d->release_start_date = $d->created_date;
        }
        //ページURL
        $d->url = $d->directory_path."/?id={$d->id}";
        $d->page_url = null;
        if($page_url = self::getPageUrl()){
          $d->page_url = $page_url."/?id={$d->id}";
        }
        $f = new pageRepository;
        $f->setSiteId($d->site_id);
        $f->setPageId($d->id);
        $f->setReleaseKbn(1);
        $field_get = $f->fieldContentGet();
        $d->fields = $field_get->row;
        $this->row[] = $d;
      }
      if($preview_post = self::getPreviewPost()){
        //途中プレビューポスト値に差し替える
        $this->setPost($preview_post, 'diff');
        $post = $this->getPost();
        if(!$site_id || !$post['navigation_id']){
          return false;
        }
        //フィールド取得
        $f = new fieldRepository;
        $f->setSiteId($site_id);
        $f->setNavigationId($post['navigation_id']);
        $f->setOrder("rank ASC");
        $field = $f->get();
        $object = (object) $post;
        foreach($field->row as $f){
          $f->value = $post['content'][$f->id];
          $object->fields[ $f->variable ? $f->variable : $f->id ] = $f;
        }
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
      $this->pageRange = $this->getPageRange();
    } catch (\Exception $e) {
      $this->set_message($e->getMessage());
    }
    return $this;
  }
  
  /*
   * @return object array mixed $this
   */
  public function getRestore() {
    $restore_datetime = self::getRestoreDatetime();
    if($restore_datetime){
      //履歴日時からバックアップデータを取得
      self::setSelect("p.*, n.directory_name, n.parent_id, mc.*");
      self::setFrom("
        ( 
          SELECT max(bk_id) as bk_id 
          FROM page_bk WHERE update_date <= :restore_datetime GROUP BY id 
        ) as mp 
        LEFT JOIN page_bk as p ON mp.bk_id = p.bk_id 
        LEFT JOIN (
          SELECT max(bk_id) as bk_content_id, page_id, field_id 
          FROM page_content_bk WHERE update_date <= :restore_datetime GROUP BY id
        ) as mc ON p.id = mc.page_id 
        LEFT JOIN navigation_tbl n ON p.navigation_id = n.id
      ");
      self::setValue(":restore_datetime", $restore_datetime);
    }else{
      //バックアップから履歴日時を取得
      self::setSelect("p.num, p.id, p.site_id, p.update_date");
      self::setFrom("
      ( SELECT 1 as num, id, site_id, update_date FROM page_bk 
      UNION ALL SELECT 2 as num, page_id as id, site_id, update_date FROM page_content_bk ) as p
      ");
      self::setGroupBy("update_date");
      self::setOrder("p.update_date DESC");
    }
    if($id = self::getId()){
      self::setWhere("p.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("p.site_id = :site_id");
      self::setValue(":site_id", $site_id);
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
      $stmt_allNumber = $this->prepare("SELECT FOUND_ROWS() as `allNumber`");
      $stmt_allNumber->execute();
      while($d = $stmt_allNumber->fetch(\PDO::FETCH_OBJ)){
        $this->totalNumber = $d->allNumber;
      }
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        if(!$restore_datetime){//履歴一覧を出力
          $this->row[] = $d;
          continue;
        }

        //履歴詳細を出力（1件のみ）
        //公開開始日キーとするため、なければ投稿日
        if(!$d->release_start_date){
          $d->release_start_date = $d->created_date;
        }
        $d->url = $d->directory_name."/?id={$d->id}";
        
        $d->fields = array();
        if($d->bk_content_id){
          $f = new pageRepository;
          $f->setSiteId($d->site_id);
          $f->setPageId($d->id);
          $f->setBkContentId($d->bk_content_id);
          $f->setReleaseKbn(1);
          $d->fields = $f->fieldContentGet()->row;
        }
        if(!$this->row[0]){
          $this->row[0] = $d;
        }else{
          foreach($d->fields as $key => $field){
            $this->row[0]->fields[$key] = $field;
          }
        }
      }
      $this->rowNumber = $stmt->rowCount();
      if($this->rowNumber > 0){
        $this->set_status(true);
      }
      $this->pageRange = $this->getPageRange();
    } catch (\Exception $e) {
      $this->set_message($e->getMessage());
    }
    return $this;
  }

  /*
   * @return object array mixed $this
   */
  public function fieldContentGet() {
    self::setSelect("
      fi.id as field_id,
      fi.name,
      fi.variable,
      fi.required,
      fi.field_type,
      fi.detail,
      fi.attention,
      fi.rank,
      co.id as content_id,
      co.page_id,
      co.value,
      co.content_type,
      co.content_size,
      co.content_mime
    ");
    if($bk_content_id = self::getBkContentId()){
      //バックアップIDから取得
      self::setFrom("page_content_field_tbl fi 
        LEFT JOIN page_content_bk co ON co.field_id = fi.id
      ");
      self::setWhere("co.bk_id = :bk_content_id");
      self::setValue(":bk_content_id", $bk_content_id);
    }else{
      //通常の取得
      self::setFrom("page_content_field_tbl fi 
        LEFT JOIN page_content_tbl co ON co.field_id = fi.id
      ");
    }
    self::setWhere("fi.delete_kbn IS NULL AND co.delete_kbn IS NULL");
    self::setOrder("fi.rank ASC");
    if($site_id = self::getSiteId()){
      self::setWhere("fi.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($navigation_id = self::getNavigationId()){
      self::setWhere("fi.navigation_id = :navigation_id");
      self::setValue(":navigation_id", $navigation_id);
    }
    if($page_id = self::getPageId()){
      self::setWhere("co.page_id = :page_id");
      self::setValue(":page_id", $page_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("fi.release_kbn = :release_kbn");
      self::setValue(":release_kbn", $release_kbn);
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
        if($d->content_type == 'input_file' && $d->value){
          $d->value = "/datas/page/".$d->page_id."/".$d->value;
        }
        if(in_array( $d->field_type, ['select', 'input_radio', 'input_checkbox', 'table'], true)){
          $d->detail = json_decode($d->detail, JSON_UNESCAPED_UNICODE);
        }
        if(in_array( $d->field_type, ['input_checkbox', 'table'], true)){
          $d->value = json_decode($d->value, JSON_UNESCAPED_UNICODE);
        }
        $var = $d->field_id;
        if($d->variable){
          $var = $d->variable;
        }
        $this->row[$var] = $d;
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
    $pageAddress = ADDRESS_SITE.($_SESSION['site']->directory ? $_SESSION['site']->directory.'/' : null).'datas/page/';
    $pageDir = DIR_SITE.($_SESSION['site']->directory ? $_SESSION['site']->directory.'/' : null).'datas/page/';
    if((!isset($_SESSION['site']->top_directory) || !$_SESSION['site']->top_directory) && !$_SESSION['site']->directory){
      $this->set_message('サイトディレクトリを選択してください。');
    }
    if(!$push['navigation_id']){
      $this->set_message('ナビゲーションを選択してください。');
    }
    if(!$push['content']){
      $this->set_message('内容がありません。');
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
    $f = new fieldRepository;
    $f->setSiteId($push['site_id']);
    $f->setNavigationId($push['navigation_id']);
    $f->setOrder("rank ASC");
    $field = $f->get();
    if($field->rowNumber == 0){
      $this->set_message('入力項目を作成してください。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    $content = @$push['content'];
    $delete_images = @$push['delete_images'];
    unset($push['content'], $push['delete_images'], $push['token']);
    
    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'page_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      if(!$push['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("UPDATE page_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND navigation_id = :navigation_id AND delete_kbn IS NULL");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $push['site_id'], 
          ':navigation_id'=> $push['navigation_id']
        ])){
          $this->rollBack();
        }
      }
      
      //複製
      $stmt = $this->prepare("INSERT INTO page_bk ( id, site_id, navigation_id, account_id, rank, release_kbn, release_start_date, release_end_date, name, meta_description, meta_keywords, delete_kbn, update_date, created_date) ( SELECT * FROM page_tbl WHERE id = :id )");
      if(!$stmt->execute([
        ':id'=> $this->_lastId
      ])){
        $this->rollBack();
      }
      
      //ディレクトを生成
      $dirPath = $pageDir.$this->_lastId;
      if(!$ut->createDir($dirPath)){
        $this->set_message('ディレクトを生成できません。');
        return $this;
      }

      foreach($content as $field_id => $field_value){
        //フィールドを取得
        $i = array_search( $field_id, array_column( $field->row, 'id'));
        $field_type = $field->row[$i]->field_type;
        $file = array('name'=> null, 'tmp_name'=> null, 'size'=> null, 'mime'=> null, 'error'=> null);
        if($field_type == "input_file"){
          //画像ファイル生成
          if(isset($_FILES['content']['name'][$field_id]) && $_FILES['content']['name'][$field_id]){
            $file = array(
              'name'=> $_FILES['content']['name'][$field_id],
              'tmp_name'=> $_FILES['content']['tmp_name'][$field_id],
              'size'=> $_FILES['content']['size'][$field_id],
              'mime'=> $_FILES['content']['type'][$field_id],
              'error'=> $_FILES['content']['error'][$field_id]
            );
            $field_value = $ut->uploadedFile($file['name'], $file['tmp_name'], $file['error'],  $dirPath, "page_{$field_id}");
            if(!$file['name']){
              $this->set_message('ファイル名を生成できません。');
              return $this;
            }
          }
          //新規ファイルなし、または削除でない場合
          if(!$file['name'] && (!$delete_images || !in_array($field_id, $delete_images))){
            continue;
          }
        }
        if($field_type == "table"){

          //テーブルの場合、配列を変換 => キー1:行、キー2:列とする
          $table = array();
          $column_field = $field->row[$i]->detail;
          foreach($field_value as $col_key => $col_value){
            //テーブルカラムのフィールドを取得
            $i_column = array_search( $col_key, array_column( $column_field, 'column_id'));
            $column_type = $column_field[$i_column]->column_type;
            foreach($col_value as $row_key => $row_value){
              if($column_type == "input_file"){
                //画像ファイル生成
                if(isset($_FILES['content']['name'][$field_id][$col_key][$row_key]) && $_FILES['content']['name'][$field_id][$col_key][$row_key]){
                  $file = array(
                    'name'=> $_FILES['content']['name'][$field_id][$col_key][$row_key],
                    'tmp_name'=> $_FILES['content']['tmp_name'][$field_id][$col_key][$row_key],
                    'size'=> $_FILES['content']['size'][$field_id][$col_key][$row_key],
                    'mime'=> $_FILES['content']['type'][$field_id][$col_key][$row_key],
                    'error'=> $_FILES['content']['error'][$field_id][$col_key][$row_key]
                  );
                  $path = pathinfo($file['name']);
                  $file_name = $ut->uploadedFile($file['name'], $file['tmp_name'], $file['error'],  $dirPath, $path['filename']);
                  if($file_name){
                    $row_value = $pageAddress . $this->_lastId . "/" . $file_name . "?" . date("YmdHis");
                  }else{
                    $this->set_message('ファイル名を生成できません。');
                    return $this;
                  }
                }
              }
              
              $table[$row_key][$col_key] = $row_value;
            }
          }
          $field_value = $table;
        }
        if(is_array($field_value)){
          $field_value = json_encode(array_filter($field_value), JSON_UNESCAPED_UNICODE);
        }

        $delete_kbn = null;
        if($delete_images && in_array($field_id, $delete_images)){
          $delete_kbn = 1;
        }
        $query = 'INSERT INTO page_content_tbl
          (site_id, page_id, field_id, value, content_type, content_size, content_mime)
          VALUES (:site_id, :page_id, :field_id, :value, :content_type, :content_size, :content_mime)
          ON DUPLICATE KEY UPDATE value = :value, content_type = :content_type, content_size = :content_size, content_mime = :content_mime, delete_kbn = :delete_kbn';
        $params = [
          ':site_id' => $push['site_id'],
          ':page_id' => $this->_lastId,
          ':field_id' => $field_id,
          ':value' => $field_value,
          ':content_type' => $field_type,
          ':content_size' => $file['size'],
          ':content_mime' => $file['mime'],
          ':delete_kbn' => $delete_kbn
        ];
        $stmt = $this->prepare($query);
        if(!$stmt->execute($params)){
          $this->rollBack();
        }

        //複製
        $stmt = $this->prepare("INSERT INTO page_content_bk ( id, site_id, page_id, field_id, value, content_type, content_size, content_mime, delete_kbn, update_date, created_date) ( SELECT * FROM page_content_tbl WHERE id = :id )");
        if(!$stmt->execute([
          ':id'=> $this->lastInsertId()
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
    //キャッシュクリア
    $ut->smartyClearAllCache();
    //サイトマップ更新フラグ付与
    $si = new sitesRepository;
    $si->changeSitemapFlag(['id'=> $push['site_id'], 'flag'=> true]);
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
      $query = $this->queryCreate($push, 'page_tbl');
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
    //サイトマップ更新フラグ付与
    $si = new sitesRepository;
    $si->changeSitemapFlag(['id'=> $push['site_id'], 'flag'=> true]);
    return $this;
  }
  
  public function fixPageLink(){
    $ut   = new utilityRepository;
    $push = $this->getPost();
    $site_id = $this->getSiteId();
    $befor_name = $this->getBeforName();
    $after_name = $this->getAfterName();
    if($push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if(!$site_id){
      $this->set_message('サイトを選択してください。');
    }
    if(!$befor_name){
      $this->set_message('変更前の名称がありません。');
    }
    if(!$after_name){
      $this->set_message('変更後の名称がありません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    $this->connect();
    $this->beginTransaction();
    try {
      $query = "UPDATE page_content_tbl 
                SET value = REPLACE(value, :befor_name, :after_name) WHERE site_id = :site_id";
      $bind = array(
        ":site_id" => $site_id,
        ":befor_name" => $befor_name,
        ":after_name" => $after_name
      );
      $stmt = $this->prepare($query);
      if(!$stmt->execute($bind)){
        $this->rollBack();
      }
      $query = "UPDATE page_structure_tbl 
                SET html = REPLACE(html, :befor_name, :after_name) WHERE site_id = :site_id";
      $bind = array(
        ":site_id" => $site_id,
        ":befor_name" => $befor_name,
        ":after_name" => $after_name
      );
      $stmt = $this->prepare($query);
      if(!$stmt->execute($bind)){
        $this->rollBack();
      }
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
}
// ?>