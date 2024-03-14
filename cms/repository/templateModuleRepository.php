<?php 
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class templateModuleRepository extends dbRepository {
  
  use \entity\templateModuleEntity;
  
  public function tree($directory, $outputType = null, $levelStop = null, $tree = array(), $level = 0){
    if($level === $levelStop){
      return $tree;
    }
    //ファイル一覧取得
    if($outputType){
      $list = glob($directory.'/*.*');
    }else{
      $list = glob($directory.'/*', GLOB_ONLYDIR);
    }
    //運営側のテンプレート保存先
    $omit_dirname = 'module/'.self::getDesignTheme().'/files';
    foreach ($list as $key => $d){
      $path = pathinfo($d);
      $dirname = str_replace(DIR_CMS, "", $path['dirname']);
      $relative_path = str_replace($omit_dirname, "", $dirname);
      $relative_path = ($relative_path ? ltrim($relative_path, "/")."/" : "") . $path['basename'];
      $value = (object) array(
        'basename' => $path['basename'],
        'filename' => $path['filename'],
        'dirname'  => $dirname,
        'relative_path' => $relative_path,
        'filesize' => filesize($d),
        'filedate' => date("Y/m/d H:i:s", filemtime($d)),
        'level' => $level
      );
      $tree[] = $value;
      if(is_array(glob($d.'/*', GLOB_ONLYDIR))){
        $tree = $this->tree($d, $outputType, $levelStop, $tree, $level+1);
      }
    }
    return $tree;
  }
  
  /*
   * 全件取得
   * @return object array mixed $this
   */
  public function get() {
    $directory = DIR_CMS.'module';
    if($design_theme = self::getDesignTheme()){
      $directory.= '/'.$design_theme.'/files';
    }
    if($design_directory = self::getDesignDirectory()){
      $directory.= '/'.$design_directory;
    }
    if($design_file = self::getDesignFile()){
      $directory.= '/'.$design_file;
      $this->row[0] = (object) array(
        'theme' => $design_theme,
        'directory' => $design_directory,
        'fileName' => $design_file,
        'contents' => htmlspecialchars(file_get_contents($directory))
      );
      if($this->row[0]->contents){
        $this->totalNumber = $this->rowNumber = 1;
        $this->set_status(true);
      }
      return $this; //ファイル出力
    }
    $this->row = $this->tree($directory, self::getOutputType(), self::getLevelStop());
    $this->totalNumber = $this->rowNumber = count($this->row);
    if($this->rowNumber > 0){
      $this->set_status(true);
    }
    return $this;
  }
  
  public function push(){
    $push = $this->getPost();
    $ut = new utilityRepository;
    if(!$push['theme'] || !$push['directory']){
      $this->set_message('ディレクトリを選択してください。');
    }
    if(!$ut->mbStrLenCheck($push['name'], 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
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
      $query = $this->queryCreate($push, 'template_log_tbl');
      $stmt = $this->prepare($query['query']);
      if(!$stmt->execute($query['params'])){
        $this->rollBack();
      }
      $this->_lastId = $this->lastInsertId();
      $dir = DIR_CMS."module/{$push['theme']}/files/{$push['directory']}/{$push['name']}";
      if(!$push['delete_kbn']){
        if(htmlspecialchars_decode(file_put_contents($dir, $push['contents']))){
          $this->set_status(true);
        }else{
          $this->set_message('保存できません。'.$dir);
        }
      }else{
        if(unlink($dir)){
          $this->set_status(true);
        }else{
          $this->set_message('削除できません。'.$dir);
        }
      }
    } catch (\PDOException $e){
      $this->rollBack();
      $this->set_message($e->getMessage());
      return $this;
    }
    $this->commit();
    return $this;
  }
  
  public function themePush(){
    $push = $this->getPost();
    $ut = new utilityRepository;
    if(!$ut->mbStrLenCheck($push['name'], 1, 120)){
      $this->set_invalid('name', '必須または120文字以内となります。');
    }
    if($push['token'] != $ut->h($ut->generate_token())){
      $this->set_message('トークンが発行されないため、登録出来ません。');
    }
    if($this->_message || $this->_invalid){
      return $this;
    }
    unset($push['token']);
    try {
      $newDir = DIR_CMS."module/{$push['name']}";
      $oldDir = DIR_CMS."module/{$push['theme']}";
      if(!$push['delete_kbn']){
        if($push['theme']){
          if(rename($oldDir, $newDir)){
            $this->set_status(true);
          }else{
            $this->set_message('変更できません。'.$newDir);
          }
        }else{
          if($ut->createDir($newDir)){
            $this->set_status(true);
          }else{
            $this->set_message('保存できません。'.$newDir);
          }
        }
      }else{
        $dirArray = array(
          $newDir,
          $newDir.'/.htaccess',
          $newDir.'/.thumbs/files',
          $newDir.'/.thumbs',
          $newDir.'/files'
        );
        foreach($dirArray as $subDir){
          $ut->removeDir($subDir);
        }
        if($ut->removeDir($newDir)){
          $this->set_status(true);
        }else{
          $this->set_message('削除できません。(中のファイルを削除してください。)'.$newDir);
        }
      }
    } catch (\Exception $e){
      $this->set_message($e->getMessage());
      return $this;
    }
    return $this;
  }
}
// ?>