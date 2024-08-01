<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;
use host\cms\repository\sitesRepository;

class pageSettingRepository extends dbRepository {
  
  public function __construct () {
  }
  
  public $id;
  public function setId(int $id) :void{
    $this->id = $id;
  }
  public function getId(){
    return $this->id;
  }
  
  public $result;
  public $client_editor;
  public $system_editor;
  public function get() {

    //初期化
    $this->set_status(false);
    $this->result = (object) array(
      "editor_color_palette" => "",
      "editor_css" => "[]",
      "editor_style" => "[]",
      "editor_template" => "[]",
      "ga4_credentials" => "",
      "ga4_client_email" => "",
      "ga4_property_id" => ""
    );

    //設定データ取得
    if($id = self::getId()){
      $si = new sitesRepository;
      $si->setId($id);
      $si->setLimit(1);
      $site = @$si->get()->row[0];
      if(!$site){
        $this->set_message("サイトがありません");
        return $this;
      }

      $this->client_editor = new \StdClass;
      $this->system_editor = new \StdClass;
      if($site->design_authority == "default"){
        $system_theme_files = array(
          (object) array(
            'name' => 'editor_css',
            'url' => $site->design_directory.$site->design_theme."/files/editor/css.json"
          ),
          (object) array(
            'name' => 'editor_color_palette',
            'url' => $site->design_directory.$site->design_theme."/files/editor/color_palette.json"
          ),
          (object) array(
            'name' => 'editor_style',
            'url' => $site->design_directory.$site->design_theme."/files/editor/style.json"
          ),
          (object) array(
            'name' => 'editor_template',
            'url' => $site->design_directory.$site->design_theme."/files/editor/template.json"
          ),
        );
        foreach($system_theme_files as $files){
          $file = null;
          if (isset($files->url) && file_exists($files->url)) {
            $file = file_get_contents($files->url, true);
          }
          if(isset($files->name)){
            $this->system_editor->{$files->name} = $file;
          }
        }
      }
      
      //設定ファイル取得
      try {
        self::connect();
        $stmt = self::prepare("SELECT * FROM page_setting_tbl p WHERE p.id = :id AND p.delete_kbn IS NULL");
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        $stmt->execute();
        while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
          foreach($d as $name => $value) {
            $this->client_editor->{$name} = html_entity_decode($value, ENT_QUOTES);
            if(in_array($name, ["ga4_credentials", "ga4_client_email", "ga4_property_id"])){
              $this->result->{$name} = $value;
            }
          }
        }
      } catch (\Exception $e) {
        $this->set_message($e->getMessage());
      }
      
      //editor_css
      $editor_css = $this->system_editor->editor_css = json_decode(@$this->system_editor->editor_css);
      $this->client_editor->editor_css = json_decode($this->client_editor->editor_css);
      if($this->client_editor->editor_css){
        foreach($this->client_editor->editor_css as $css){
          $editor_css[] = $css;
        }
      }
      $this->result->editor_css = json_encode($editor_css);
      
      //editor_color_palette
      $editor_color_palette = $this->client_editor->editor_color_palette;
      $this->system_editor->editor_color_palette = json_decode(@$this->system_editor->editor_color_palette);
      if($this->system_editor->editor_color_palette){
        foreach($this->system_editor->editor_color_palette as $color){
          $editor_color_palette.= substr($editor_color_palette, -1) != "," ? "," . $color : $color;
        }
      }
      $this->result->editor_color_palette = $editor_color_palette;
      
      //editor_style
      $editor_style = json_decode("[". $this->client_editor->editor_style ."]");
      $this->system_editor->editor_style = json_decode(@$this->system_editor->editor_style);
      if($this->system_editor->editor_style){
        foreach($this->system_editor->editor_style as $style){
          $editor_style[] = $style;
        }
      }
      $this->result->editor_style = json_encode($editor_style, JSON_UNESCAPED_UNICODE);
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