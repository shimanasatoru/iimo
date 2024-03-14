<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class pageSettingRepository extends dbRepository {
  
  use \host\cms\entity\sitesEntity;

  public function get() {
    self::setSelect("*");
    self::setFrom("page_setting_tbl p");
    self::setWhere("p.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("p.id = :id");
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
      $this->rowNumber = $stmt->rowCount();
      if($this->rowNumber > 0){
        $this->set_status(true);
      }
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->editor_css = json_decode($d->editor_css);
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
  
  public function push(array $post, $dataType = null){
    $ut  = new utilityRepository;
    $token = filter_var( @$post['token'], FILTER_SANITIZE_SPECIAL_CHARS);
    $directory = filter_var( @$post['directory'], FILTER_SANITIZE_SPECIAL_CHARS);
    $form_id = filter_var( @$post['form_id'], FILTER_SANITIZE_SPECIAL_CHARS);
    //セッティング設定の処理
    if($form_id == "settingForm"){
      $push  = filter_var_array( $post,[
        'id' => [
          'filter' => FILTER_VALIDATE_INT,
          'options'=> array('default'=>null)
        ],
        'editor_css' => [
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'options'=> array('default'=>null)
        ],
        'editor_color_palette' => [
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'options'=> array('default'=>null)
        ],
        'editor_style' => [
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'options'=> array('default'=>null)
        ],
        'editor_template' => [
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'options'=> array('default'=>null)
        ],
        'delete_kbn' => [
          'filter' => FILTER_VALIDATE_INT,
          'options'=> array('default'=>null)
        ]
      ]);
      if(!$push['id']){
        $this->set_message('サイトIDが取得できないため登録できません。');
      }
      if(!$token || $token != $ut->h($ut->generate_token())){
        $this->set_message('トークンが発行されないため、登録出来ません。');
      }
      if($this->_message || $this->_invalid){
        return $this;
      }
      $push['editor_css'] = json_encode(explode('&#13;&#10;', $push['editor_css']), JSON_UNESCAPED_UNICODE);
      
      $this->connect();
      $this->beginTransaction();
      try {
        $query = 'INSERT INTO page_setting_tbl
          (id, editor_css, editor_color_palette, editor_style, editor_template, delete_kbn)
          VALUES (:id, :editor_css, :editor_color_palette, :editor_style, :editor_template, :delete_kbn)
          ON DUPLICATE KEY UPDATE editor_css = :editor_css, editor_color_palette = :editor_color_palette, editor_style = :editor_style, editor_template = :editor_template, delete_kbn = :delete_kbn';
        $params = [
          ':id' => $push['id'],
          ':editor_css' => $push['editor_css'],
          ':editor_color_palette' => $push['editor_color_palette'],
          ':editor_style' => $push['editor_style'],
          ':editor_template' => $push['editor_template'],
          ':delete_kbn' => $push['delete_kbn']
        ];
        $stmt = $this->prepare($query);
        foreach($params as $key => $value){
          $stmt->bindValue($key, $value, (is_numeric($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
        }
        if(!$stmt->execute()){
          $this->rollBack();
        }
        $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
        $this->set_status(true);
      } catch (\PDOException $e){
        $this->rollBack();
        exit($e->getMessage());
      }
      $this->commit();
    }
    
    //google設定の処理
    if($form_id == "googleForm"){
      $ga4_credentials_file = @$_FILES['ga4_credentials'];
      $push = filter_var_array( $post,[
        'id' => [
          'filter' => FILTER_VALIDATE_INT,
          'options'=> array('default'=>null)
        ],
        'ga4_property_id' => [
          'filter' => FILTER_VALIDATE_INT,
          'options'=> array('default'=>null)
        ],
        'ga4_credentials_delete' => [
          'filter' => FILTER_VALIDATE_INT,
          'options'=> array('default'=>null)
        ]
      ]);
      if(!$push['id']){
        $this->set_message('サイトIDが取得できないため登録できません。');
      }
      if(!$directory){
        $this->set_message('サイトディレクトリを取得できないため登録できません。');
      }
      if(!$token || $token != $ut->h($ut->generate_token())){
        $this->set_message('トークンが発行されないため、登録出来ません。');
      }
      if($this->_message || $this->_invalid){
        return $this;
      }

      $this->connect();
      $this->beginTransaction();
      try {
        $query = 'INSERT INTO page_setting_tbl
          (id, ga4_property_id) VALUES (:id, :ga4_property_id)
          ON DUPLICATE KEY UPDATE ga4_property_id = :ga4_property_id';
        $params = [
          ':id' => $push['id'],
          ':ga4_property_id' => $push['ga4_property_id']
        ];
        $stmt = $this->prepare($query);
        foreach($params as $key => $value){
          $stmt->bindValue($key, $value, (is_numeric($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
        }
        if(!$stmt->execute()){
          $this->rollBack();
        }
        $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();

        $update = false;
        $ga4_credentials = null;
        $ga4_client_email = null;
        if($ga4_credentials_file && $ga4_credentials_file['name']){
          $file = $ga4_credentials_file;
          $dir = DIR_SITE.($directory.'/').'datas/ga4/';
          if(!$ut->createDir($dir)){
            $this->set_message('ディレクトリを生成できません。');
            return $this;
          }
          $upload_url = $ut->uploadedFile($file['name'], $file['tmp_name'], $file['error'],  $dir, "ga4_credentials");
          if(!$upload_url){
            $this->set_message('認証ファイル名を生成できません。');
            return $this;
          }
          $ga4_credentials = $dir.$upload_url;
          $context = stream_context_create([
            'ssl' => [
              'verify_peer'      => false,
              'verify_peer_name' => false
            ]
          ]);
          $json = json_decode(file_get_contents($ga4_credentials, false, $context), true); //SSL operation failed
          $ga4_client_email = $json['client_email'];
          $update = true;
        }
        if(@$push['ga4_credentials_delete']){
          $update = true;
        }
        if($update){
          $query = "UPDATE page_setting_tbl SET ga4_credentials = :ga4_credentials, ga4_client_email = :ga4_client_email WHERE id = :id LIMIT 1";
          $stmt = self::prepare($query);
          $stmt->bindParam(':id', $this->_lastId, \PDO::PARAM_INT);
          $stmt->bindParam(':ga4_credentials', $ga4_credentials, \PDO::PARAM_STR);
          $stmt->bindParam(':ga4_client_email', $ga4_client_email);
          if(!$stmt->execute()){
            $this->rollBack();
          }
        }
        $this->set_status(true);
      } catch (\PDOException $e){
        $this->rollBack();
        exit($e->getMessage());
      }
      $this->commit();
    }
    return $this;
  }
  
}
// ?>