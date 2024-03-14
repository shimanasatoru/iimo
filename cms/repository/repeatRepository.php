<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\repeatItemRepository;
use host\cms\repository\productIncludedRepository;
use host\cms\repository\campaignRepository;

class repeatRepository extends dbRepository {
  
  use \host\cms\entity\repeatEntity;
  
  /*
   * @return object array mixed $this
   */
  public function get() {
    self::setSelect("
      p.*, 
      s.domain, 
      s.directory, 
      u.name as unit_name,
      d.name as delivery_name,
      t.code as temperature_zone_code,
      t.name as temperature_zone_name,
      t.badge as temperature_zone_badge
    ");
    self::setFrom("
      repeat_product_tbl p 
      LEFT JOIN site_tbl s ON p.site_id = s.id 
      LEFT JOIN m_unit_tbl u ON p.unit_id = u.id 
      LEFT JOIN m_temperature_zone_tbl t ON p.temperature_zone = t.id
      LEFT JOIN m_delivery_tbl d ON p.delivery_id = d.id 
    ");
    self::setWhere("p.delete_kbn IS NULL AND s.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("p.id = :id");
      self::setValue(":id", $id);
    }
    if($site_id = self::getSiteId()){
      self::setWhere("p.site_id = :site_id");
      self::setValue(":site_id", $site_id);
    }
    if($member_id = self::getMemberId()){
      self::setWhere("p.member_id = :member_id");
      self::setValue(":member_id", $member_id);
    }
    if($release_kbn = self::getReleaseKbn()){
      self::setWhere("p.release_kbn = :release_kbn AND (p.release_start_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') >= DATE_FORMAT(p.release_start_date, '%Y-%m-%d')) AND (p.release_end_date IS NULL || DATE_FORMAT(now(), '%Y-%m-%d') <= DATE_FORMAT(p.release_end_date, '%Y-%m-%d'))");
      self::setValue(":release_kbn", $release_kbn);
    }
    self::setOrder("p.rank ASC");
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
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){

        $d->release = 0;
        if($d->release_kbn == 1 
           && (!$d->release_start_date || strtotime($today) >= strtotime($d->release_start_date)) 
           && (!$d->release_end_date || strtotime($today) <= strtotime($d->release_end_date))){
          $d->release = 1;
        }elseif($d->release_kbn == 2){
          $d->release = 2;
        }
        
        $d->member_value = $this->member_value[($d->member_id ? 1 : 0)];
        
        //商品
        $item = new repeatItemRepository;
        $item->setSiteId($d->site_id);
        $item->setRepeatProductId($d->id);
        if($item_id = $this->getItemId){
          $item->setId($item_id);
        }
        $item->setSalesKbn(1);
        $item->setLimit(1);
        $d->item = $item->get()->row[0];
        
        //付属品
        $d->option_include_id = json_decode($d->option_include_id, true);
        $d->option_include = array();
        if($d->option_include_id){
          $pi = new productIncludedRepository;
          $pi->setSiteId($d->site_id);
          $pi->setIds($d->option_include_id);
          $pi->setReleaseKbn(1);
          $d->option_include = $pi->get()->row;
        }

        $d->campaign_id = json_decode($d->campaign, true);
        $d->campaign = array();
        if($d->campaign_id){
          
          $calc = new productRepository;
          $ca = new campaignRepository;
          $ca->setSiteId($d->site_id);
          $ca->setIds($d->campaign_id);
          $ca->setUsageKbn(1);
          $campaign = $ca->get()->row;
          if($campaign){
            foreach($campaign as $c){
              $discount = $calc->calcDiscountPrice(
                $d->tax_class, 
                $d->tax_rate, 
                $c->discount_type, 
                $c->discount_number, 
                $d->unit_price
              );
              $c->discount_unit_price = $discount['price'];
              $c->discount_unit_tax_price = $discount['tax_price'];
              $c->discount_unit_notax_price = $discount['notax_price'];
              $c->discount_unit_tax = $discount['tax'];
              $d->campaign[] = $c;
            }
          }
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
    if(!$ut->mbStrLenCheck($data['name'], 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
    }
    if(!is_int($data['unit_price'])){
      $this->set_invalid('unit_price', '必須となります。');
    }
    if(!is_int($data['unit_id'])){
      $this->set_invalid('unit_id', '必須となります。');
    }
    if($token != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }

    $data['campaign'] = json_encode($data['campaign'], JSON_UNESCAPED_UNICODE);
    $data['option_include_id'] = json_encode($data['option_include_id'], JSON_UNESCAPED_UNICODE);

    $this->connect();
    $this->beginTransaction();
    try {
      $query = $this->queryCreate($data, 'repeat_product_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $data['id'] ? $data['id'] : $this->lastInsertId();
      if(!$data['id'] && $this->_lastId){
        //新規登録の場合は、順位を一つずつ移動
        $stmt = $this->prepare("
          UPDATE repeat_product_tbl SET rank = rank + 1 WHERE id != :id AND site_id = :site_id AND delete_kbn IS NULL
        ");
        if(!$stmt->execute([
          ':id'=> $this->_lastId, 
          ':site_id'=> $data['site_id']
        ])){
          $this->rollBack();
        }
      }

      //ディレクトリ生成・QRコード生成
      $dirPath = DIR_SITE.$_SESSION['site']->directory.'/datas/product/'.$this->_lastId;
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
                      id, repeat_product_id, rank, use_name, name, size, mime
                    ) VALUES (
                      :id, :repeat_product_id, :rank, :use_name, :name, :size, :mime
                    ) ON DUPLICATE KEY UPDATE rank = :rank, delete_kbn = :delete_kbn';
          $params = [
            ':id'         => $id ? $id : null,
            ':repeat_product_id' => $this->_lastId,
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
    $push = $push['data'];
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
      $query = $this->queryCreate($push, 'repeat_product_tbl');
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