<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\repeatRepository;

class repeatItemRepository extends dbRepository {
  
  use \host\cms\entity\repeatItemEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("rp.*, s.domain, s.directory");
    self::setFrom("repeat_product_item_tbl rp LEFT JOIN site_tbl s ON rp.site_id = s.id ");
    self::setWhere("rp.delete_kbn IS NULL AND s.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("rp.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("rp.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($repeat_product_id = self::getRepeatProductId()){
      self::setWhere("rp.repeat_product_id = :repeat_product_id");
      self::setValue(":repeat_product_id", $repeat_product_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("rp.release_kbn = :release_kbn AND (rp.release_start_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') >= DATE_FORMAT(rp.release_start_date, '%Y-%m-%d')) AND (rp.release_end_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') <= DATE_FORMAT(rp.release_end_date, '%Y-%m-%d'))");
      self::setValue(":release_kbn", $release_kbn);
    }
    if($sales_kbn = self::getSalesKbn()){
      $date = date("Y-m-d");
      if($sales_date = self::getSalesDate()){
        $date = $sales_date;
      }
      self::setWhere("rp.sales_kbn = :sales_kbn AND (rp.sales_start_date IS NULL || DATE_FORMAT(:sales_date, '%Y-%m-%d') >= DATE_FORMAT(rp.sales_start_date, '%Y-%m-%d')) AND (rp.sales_end_date IS NULL || DATE_FORMAT(:sales_date, '%Y-%m-%d') <= DATE_FORMAT(rp.sales_end_date, '%Y-%m-%d'))");
      self::setValue(":sales_kbn", $sales_kbn);
      self::setValue(":sales_date", $date);
    }
    self::setOrder("rp.rank ASC");
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

      $today = date("Y-m-d");
      $table = array(
        '&#13;&#10;'=> "\n",
        '&#10;'=> "\n"
      );
      $search = array_keys($table);
      $replace = array_values($table);
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        
        $d->sales = 0;
        if($d->sales_kbn == 1 
           && (!$d->sales_start_date || strtotime($today) >= strtotime($d->sales_start_date)) 
           && (!$d->sales_end_date || strtotime($today) <= strtotime($d->sales_end_date))){
          $d->sales = 1;
        }elseif($d->sales_kbn == 2){
          $d->sales = 2;
        }
        
        $d->explanatory_text1 = str_replace($search,$replace,$d->explanatory_text1);

        $files_stmt = self::prepare('
          SELECT * FROM repeat_product_files_tbl 
          WHERE repeat_product_item_id = :id AND delete_kbn IS NULL ORDER BY rank ASC
        ');
        $files_stmt->execute([':id' => $d->id]);
        $d->files = array();
        while($f = $files_stmt->fetch(\PDO::FETCH_OBJ)){
          $f->url = null;
          if($d->directory && $f->name){
            $f->url = ($d->domain ? $d->domain : ADDRESS_SITE."/".$d->directory)."/datas/repeat/item/{$d->id}/{$f->name}";
          }
          $d->files[] = $f;
        }
        $this->row[] = $d;
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

  public function push(){
    $push = $this->getPost();
    $token = $push['token'];
    $data = $push['data'];
    $files = $push['files'];
    
    $ut = new utilityRepository;
    if(!$data['site_id']){
      $this->set_message('サイトを選択してください。');
    }
    if(!$data['repeat_product_id']){
      $this->set_message('リピート番号がありません。');
    }
    if(!$ut->mbStrLenCheck($data['name'], 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
    }
    if($token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($data, 'repeat_product_item_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $data['id'] ? $data['id'] : $this->lastInsertId();
      if(!$data['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("
          UPDATE repeat_product_item_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL
        ");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $data['site_id']
        ])){
          $this->rollBack();
        }
      }

      //ディレクトリ生成・QRコード生成
      $dirPath = DIR_SITE.$_SESSION['site']->directory.'/datas/repeat/item/'.$this->_lastId;
      if($ut->createDir($dirPath) != true){
        $this->set_message("ディレクトリ({$dirPath})生成が出来ません。権限を確認してください。");
        return $this;
      }

      //データ保存処理 並び替え含む
      if(isset($files['files_sort_id']) && $files['files_sort_id'] !== "false"){
        $files_sort_id = explode(',', $files['files_sort_id']);
        foreach($files_sort_id as $rank=>$id){ //IDがあったら更新、無ければ新規
          if($id && !ctype_digit($id)){
            $this->set_message($id.': ファイル生成時にエラーが発生しました。');
            continue;
          }

          //画像ファイル生成
          $file = array('name'=> null, 'tmp_name'=> null, 'size'=> null, 'mime'=> null, 'error'=> null);
          if(!$id && isset($_FILES['images']['name'][$rank])){
            $file = array(
              'name'=> $_FILES['images']['name'][$rank],
              'tmp_name'=> $_FILES['images']['tmp_name'][$rank],
              'size'=> $_FILES['images']['size'][$rank],
              'mime'=> $_FILES['images']['type'][$rank],
              'error'=> $_FILES['images']['error'][$rank]
            );
            $newName = pathinfo($file['name'], PATHINFO_FILENAME);
            $file['name'] = $ut->uploadedFile($file['name'], $file['tmp_name'], $file['error'],  $dirPath, $newName);
            if(!$file['name']){
              $this->set_message('ファイル名を生成できません。');
              return $this;
            }
          }
          $query = 'INSERT INTO repeat_product_files_tbl ( 
                      id, repeat_product_item_id, rank, use_name, name, size, mime
                    ) VALUES (
                      :id, :repeat_product_item_id, :rank, :use_name, :name, :size, :mime
                    ) ON DUPLICATE KEY UPDATE rank = :rank, delete_kbn = :delete_kbn';
          $params = [
            ':id'         => $id ? $id : null,
            ':repeat_product_item_id' => $this->_lastId,
            ':rank'       => $rank,
            ':use_name'   => 'image',
            ':name'       => $file['name'],
            ':size'       => $file['size'],
            ':mime'       => $file['mime'],
            ':delete_kbn' => null
          ];
          $stmt = $this->prepare($query);
          if(!$stmt->execute($params)){
            $this->rollBack();
          }
        }
      }
      //データ削除処理
      if(@$files['delete_images']){
        foreach ($files['delete_images'] as $key => $id) {
          $delete = [
            'id' => $id,
            'delete_kbn' => 1
          ];
          $query = $this->queryCreate($delete, 'repeat_product_files_tbl');
          $stmt = $this->prepare($query['query']);
          if(!$stmt->execute($query['params'])){
            $this->rollBack();
          }
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
    $token = $push['token'];
    $push = $push['data'];
    $ut   = new utilityRepository;
    if(!$push['id']){
      $this->set_message('IDが取得できません。');
    }
    if(!$push['site_id']){
      $this->set_message('サイトを選択してください。');
    }
    if($token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($push, 'repeat_product_item_tbl');
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