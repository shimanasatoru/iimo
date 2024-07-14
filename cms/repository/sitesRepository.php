<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class sitesRepository extends dbRepository {
  
  use \host\cms\entity\sitesEntity;

  public function get() {
    self::setSelect("s.*, 
      pref.name AS prefecture_name,
      GROUP_CONCAT(COALESCE(sta.id, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS site_to_account_id,
      GROUP_CONCAT(COALESCE(sta.rank, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS account_rank,
      GROUP_CONCAT(COALESCE(sta.account_id, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS account_id,
      GROUP_CONCAT(COALESCE(a.account, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS account,
      GROUP_CONCAT(COALESCE(a.name, '') ORDER BY sta.rank ASC SEPARATOR '{tab}') AS account_name 
    ");
    self::setFrom("site_tbl s 
      LEFT JOIN m_prefectures_tbl pref ON pref.id = s.prefecture_id 
      LEFT JOIN site_to_account_tbl sta ON sta.site_id = s.id AND sta.delete_kbn IS NULL 
      LEFT JOIN account_tbl a ON sta.account_id = a.id AND a.delete_kbn IS NULL 
    ");
    self::setGroupBy("s.id");
    self::setExplodeColumns([
      "site_to_account_id", 
      "account_rank",
      "account_id",
      "account",
      "account_name",
    ]);
    self::setWhere("s.delete_kbn IS NULL");
    if($id = self::getId()){
      self::setWhere("s.id = :id");
      self::setValue(":id", $id);
    }
    if($agent_id = self::getAgentId()){
      self::setWhere("s.agent_id = :agent_id");
      self::setValue(":agent_id", $agent_id);
    }
    if($account_id = self::getAccountId()){
      self::setWhere("a.id = :account_id");
      self::setValue(":account_id", $account_id);
    }
    if($directory = self::getDirectory()){
      self::setWhere("directory = :directory");
      self::setValue(":directory", $directory);
    }
    if($keyword = self::getKeyword()){
      $where;
      foreach($keyword as $i => $word){
        $where.= ($where ? " OR " : "") . "(s.name LIKE :key{$i} OR s.directory LIKE :key{$i})";
        self::setValue(":key{$i}", "%{$word}%");
      }
      self::setWhere($where);
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
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $d->url = $d->domain.'/';
        $d->server_url = ADDRESS_SITE.$d->directory.'/';
        if(!$d->domain){
          $d->url = $d->server_url;
        }
        if($d->design_authority == "original"){
          $d->design_directory = DIR_SITE.$d->directory.'/design/';
          $d->design_address = $d->url.'design/';
        }else{
          $d->design_directory = DIR_CMS.'module/';
          $d->design_address = ADDRESS_CMS.'module/';
        }
        $this->row[] = $this->filterExplode($d);
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
  
  public function push(array $post, $dataType = null){
    $ut  = new utilityRepository;
    $token = filter_var( $post['token'], FILTER_SANITIZE_SPECIAL_CHARS);
    $push  = filter_var_array( $post,[
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'agent_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'domain' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9a-zA-Z\_\/\.]/")
      ],
      'directory' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9a-zA-Z\_]/")
      ],
      'design_authority' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],      
      'design_theme' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9a-zA-Z\_]/")
      ],      
      'release_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'release_start_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'release_end_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'company_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'postal_code' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'prefecture_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'municipality' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'address1' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'address2' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'phone_number1' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'phone_number2' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'fax_number' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'email_address' => [
        'filter' => FILTER_VALIDATE_EMAIL,
        'options'=> array('default'=>null)
      ],
      'header_code' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'edit_permission' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    
    if(!$ut->mbStrLenCheck($push['name'], 5, 100)){
      $this->set_invalid('name', 'サイト名は5文字～50文字となります。');
    }
    if(!$ut->mbStrLenCheck($push['directory'], 1, 30)){
      $this->set_invalid('directory', 'ディレクトリ名は1文字～30文字となります。');
    }
    if(!$ut->mbStrLenCheck($push['design_theme'], 1, 20)){
      $this->set_invalid('design_theme', 'デザインテーマは1文字～20文字となります。');
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
      $time = new \DateTime();
      $query = $this->queryCreate($push, 'site_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $push['id'] ? $push['id'] : $this->lastInsertId();
      
      //ディレクトリを生成・削除
      $dirPath = DIR_SITE."{$push['directory']}";
      if($push['delete_kbn'] == 1){
        //ディレクトリを削除
        if($ut->removeDir($dirPath) != true){
          $this->set_message('ディレクトリ削除が出来ません。ディレクトリ内のファイルを確認してください。');
          return $this;
        }
      }else{
        //ディレクトリを生成
        if($ut->createDir($dirPath) != true){
          $this->set_message("ディレクトリ({$dirPath})生成が出来ません。権限を確認してください。");
          return $this;
        }else{
          //デザインディレクトリを生成
          $designDirPath = $dirPath."/design";
          if($ut->createDir($designDirPath) != true){
            $this->set_message("ディレクトリ({$designDirPath})生成が出来ません。権限を確認してください。");
            return $this;
          }
          //.htaccess 生成
          if(!@file_get_contents("{$dirPath}/.htaccess")){
            $base_indexPath = "/".str_replace(DIR, "", DIR_SITE).$push['directory']."/";
            $rule_indexPath = '/index.php';
            $htaccessTxt = $this->getHtaccessTxt($base_indexPath, $rule_indexPath);
            if(file_put_contents("{$dirPath}/.htaccess", $htaccessTxt) === false){
              $this->set_message('htaccessファイルを生成できません、権限を確認してください。');
              return $this;
            }
          }
          //index.php 生成
          if(!@file_get_contents("{$dirPath}/index.php")){
            $indexTxt = $this->getIndexTxt();
            if(file_put_contents("{$dirPath}/index.php", $indexTxt) === false){
              $this->set_message('index.phpファイルを生成できません、権限を確認してください。');
              return $this;
            }
          }
        }
        
        //ロゴイメージ生成
        $update = false;
        $logoUrl = null;
        if(isset($_FILES['logo_image']) && $_FILES['logo_image']['name'] && $push['directory']){
          $file = $_FILES['logo_image'];
          $logoDir = DIR_SITE.($push['directory'].'/').'datas/logo/';
          $logoAddress = ADDRESS_SITE.($push['directory'].'/').'datas/logo/';
          if(!$ut->createDir($logoDir)){
            $this->set_message('ロゴディレクトを生成できません。');
            return $this;
          }
          $logoUrl = $ut->uploadedFile($file['name'], $file['tmp_name'], $file['error'],  $logoDir, "logo");
          if(!$logoUrl){
            $this->set_message('ロゴファイル名を生成できません。');
            return $this;
          }
          $logoUrl = $logoAddress.$logoUrl;
          $update = true;
        }
        if(isset($post['logo_image_delete']) && 
           $logo_image_delete = filter_var( $post['logo_image_delete'], FILTER_VALIDATE_INT)){
          $update = true;
        }
        if($update){
          $query = "UPDATE site_tbl SET logo_image = :logo_image WHERE id = :id LIMIT 1";
          $stmt = self::prepare($query);
          $stmt->bindParam(':id', $this->_lastId, \PDO::PARAM_INT);
          $stmt->bindParam(':logo_image', $logoUrl, \PDO::PARAM_STR);
          if(!$stmt->execute()){
            $this->rollBack();
          }
        }

        //アカウント操作
        if(isset($post['accounts']) && $post['accounts']){
          $q_accounts = $this->createQuerySiteToAccount($post['accounts']);
          if($q_accounts['query']){
            foreach($q_accounts['query'] as $k => $query){
              $stmt = $this->prepare($query);
              if(!$stmt->execute($q_accounts['params'][$k])){
                $this->rollBack();
              }
            }
          }
        }
      }
      $this->set_status(true);
    } catch (\PDOException $e){
      $this->rollBack();
      exit($e->getMessage());
    }
    $this->commit();
    return $this;
  }
  
  public function createQuerySiteToAccount(array $data){
    $accounts = filter_var_array($data, [
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'=> FILTER_REQUIRE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'account_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'=> FILTER_REQUIRE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'=> FILTER_REQUIRE_ARRAY,
        'options'=> array('default'=>null)
      ]
    ]);
    $query = array();
    $params = array();
    if($accounts['id']){
      $rank = 0;
      foreach($accounts['id'] as $k => $id){
        $query[] = 'INSERT INTO site_to_account_tbl ( 
          id, site_id, account_id, rank
        ) VALUES (
          :id, :site_id, :account_id, :rank
        ) ON DUPLICATE KEY UPDATE 
          site_id = :site_id, account_id = :account_id, rank = :rank, delete_kbn = :delete_kbn';
        $params[] = [
          ':id' => $id,
          ':site_id' => $this->_lastId,
          ':account_id'=> $accounts['account_id'][$k],
          ':rank' => $rank,
          ':delete_kbn' => $accounts['delete_kbn'][$k]
        ];
        $rank++;
      }
    }
    return array('query' => $query, 'params' => $params);
  }
  
}
// ?>