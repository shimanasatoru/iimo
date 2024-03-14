<?php 
/*
 * 便利な共用部品はここにまとめよう
 */
namespace host\cms\repository;
use host\cms\repository\dbRepository;

/*
 * yamlファイルを取得
 */
class yamlReader {
  public function parse($url_yaml){
    require_once DIR_LIBRARY."spyc-0.5/spyc.php";
    return json_decode(json_encode(spyc_load_file($url_yaml)));
  }
}

/*
 * インポートファイルの改行コードをCRLF形式に変換
 */
class CrlfFilter{
  public function filter($in, $out, &$consumed, $closing) {
    while ($bucket = stream_bucket_make_writeable($in)) {
      $bucket->data = preg_replace("/\n$/", '', $bucket->data);
      $bucket->data = preg_replace("/\r$/", '', $bucket->data);
      $bucket->data = $bucket->data . "\r\n";
      $consumed += $bucket->datalen;
      stream_bucket_append($out, $bucket);
    }
    return PSFS_PASS_ON;
  }
}
/*
 * ユーティリティ
 */
class utilityRepository{
  /*
   * ツリー出力 再帰
   *
   * @param array $list
   * @return array
   */
  public function tree(&$list, $parent){
    $tree = array();
    foreach ($parent as $key=>$value){
      if(isset($list[$value->id])){
        $value->children = $this->tree($list, $list[$value->id]);
      }
      $tree[] = $value;
    } 
    return $tree;
  }

  /*
   * jsonデータは、順序を保証しない（勝手に並び替えされる対策）
   *
   * @param array $data
   * @return array
   */
  public function json_array_encode($data) {
    $buf = array();
    foreach ($data as $key => $value) {
      $buf[] = json_encode($value);
    }
    return '[' . implode(',', $buf) . ']';    
  }
    
  /*
   * CSRFトークンの生成
   *
   * @return string トークン
   */
  public function generate_token(){
    // セッションIDからハッシュを生成
    return hash('sha256', session_id());
  }

  /*
   * CSRFトークンの検証
   *
   * @param string $token
   * @return bool 検証結果
   */
  public function validate_token($token = null){
    // 送信されてきた$tokenがこちらで生成したハッシュと一致するか検証
    if($token){
      return $token === $this->generate_token();
    }
    return false;
  }
    
  /*
   * htmlspecialcharsのラッパー関数
   *
   * @param string $str
   * @return string
   */
  public function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
    
  /*
   * PHP 5.3 以上で openssl 拡張が必要。
   */
  public function openSslRandom($n = 30){
    return strtr(substr(base64_encode(openssl_random_pseudo_bytes($n)),0,$n),'/+','_-');
  }
    
  /*
   * バリデーション系：メールチェック
   */
  public function mailCheck(string $mail){
    if(preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $mail)){
      return true;
    }
    return false;
  }
  
  /*
   * バリデーション系：文字数チェック
   * @return boolean
   */
  public function mbStrLenCheck($text, int $min_len, int $max_len){
    $text_len = mb_strlen($text);
    if($text_len < $min_len || $text_len > $max_len){
      return false;
    }
    return true;
  }
  
  /*
   * バリデーション系：日付チェック
   * 対応形式: yyyymmdd , yyyy-mm-dd, yyyy/mm/dd
   * @return boolean
   */
  public function dateCheck($date) :bool{
    if(!$date){
      return false;
    }
    $strtotime = strtotime($date);
    $y = date('Y', $strtotime);
    $m = date('m', $strtotime);
    $d = date('d', $strtotime);
    if(checkdate($m, $d, $y) == false){
      return false;
    }
    return true;
  }
  
  /*
   * フォーマット系：郵便番号
   * 対応形式: yyyymmdd , yyyy-mm-dd, yyyy/mm/dd
   * @return boolean
   */
  public function formatPostalCode($code){
    if(!$code){
      return null;
    }
    $code = str_replace("-", "", $code);
    $code = substr($code ,0,3) . "-" . substr($code ,3);
    return $code;
  }

  /*
   * ディレクトリ生成
   * @param string $dirPath, string $permission
   * @return boolean
   */
  public function createDir(string $dirPath, $permission = 0775){
    if(file_exists($dirPath) == true){
      return true;
    }
    try {
      if (@mkdir($dirPath, $permission, true) != true) {
        return false;
      }
      if (chmod($dirPath, $permission) != true) {
        return false;
      }
      
    } catch (\Exception $e){
      return $e->getMessage();
    }
    return true;
  }
  
  /*
   * ディレクトリ削除
   * @param string $dirPath
   * @return boolean
   */
  public function removeDir(string $dirPath){
    try {
      if(file_exists($dirPath) != true){
        return true;
      }
      if(is_file($dirPath)){
        return unlink($dirPath);
      }else{
        $files = array_diff(scandir($dirPath), array('.','..'));
        if (!empty($files)) {
          return false;
        }
        if (rmdir($dirPath) != true) {
          return false;
        }
      }
    } catch (\Exception $e){
      return $e->getMessage();
    }
    return true;
  }
  
  /*
   * ディレクトリをコピーする
   * @param  string $dir     コピー元ディレクトリ
   * @param  string $new_dir コピー先ディレクトリ
   * @return void
   */
  public function copyDir($this_dir, $new_dir, $permission = 0755){
    $dir     = rtrim($this_dir, '/').'/';
    $new_dir = rtrim($new_dir, '/').'/';
    // コピー元ディレクトリが存在すればコピーを行う
    if(is_dir($dir)){
      // コピー先ディレクトリが存在しなければ作成する
      if(!is_dir($new_dir)){
        mkdir($new_dir, $permission, true);
      }
      // ディレクトリを開く
      if ($handle = opendir($dir)) {
        // ディレクトリ内のファイルを取得する
        while (false !== ($file = readdir($handle))) {
          if ($file === '.' || $file === '..') {
            continue;
          }
          // 下の階層にディレクトリが存在する場合は再帰処理を行う
          if (is_dir($dir.$file)) {
            copy_dir($dir.$file, $new_dir.$file);
          } else {
            copy($dir.$file, $new_dir.$file);
          }
        }
        closedir($handle);
      }
    }
  }
  
  /*
   * ディレクトリ内のファイル削除
   * @param string $dirPath
   * @return boolean
   */
  public function removeFile(string $dirPath){
    try {
      if(file_exists($dirPath) != true){
        return true;
      }
      $files = glob($dirPath.'*');
      foreach($files as $file){
        if(is_file($file)){
          unlink($file);
        }
      }
    } catch (\Exception $e){
      return $e->getMessage();
    }
    return true;
  }
  
  /*
   * smarty キャッシュファイル生成
   * @param string $dirPath, string $texgt
   * @return boolean
   */
  public function smartyCreateCacheFile($dirpath, $text){
    if($dirpath && $text){
      @file_put_contents($dirpath, $text);
    }
    return true;
  }
  
  /*
   * smarty キャッシュファイル読み込み
   * @param string $dirPath
   * @return object string $template, int $site_id, int $page_id,
   */
  public function smartyLoadCacheFile($dirpath){
    $return = (object) array("template" => null, "site_id" => null, "page_id" => null);
    if(file_exists($dirpath)){
      $load_text = file($dirpath);
      if($load_text && $load_text[0]){
        list($return->template, $return->site_id, $return->page_id) = explode(",", $load_text[0]);
      }
    }
    return $return;
  }
  
  /*
   * smarty キャッシュクリア
   * @param string $dirPath
   * @return boolean
   */
  public function smartyClearAllCache(){
    if(DIR_SITE && $_SESSION['site'] && $_SESSION['site']->directory){
      $cache_dir = DIR_SITE . $_SESSION['site']->directory . "/cache/";
      $compile_dir = DIR_SITE . $_SESSION['site']->directory . "/templates_c/";
      $this->removeFile($cache_dir);
      $this->removeFile($compile_dir);
    }
    return true;
  }

  /*
   * 画像を保存
   * @param array $file, string $dirPath
   * @return string or boolean
   */
  public function uploadedFile($fileName, $fileTmpName, $fileError, string $dirPath, string $newName){
    if(!$fileName || !$fileTmpName || !$dirPath || !$newName){
      return false;
    }
    if(file_exists($dirPath) == false){
      return false;
    }
    try {
      if ($fileError == UPLOAD_ERR_OK && is_uploaded_file($fileTmpName)) {
        setlocale(LC_CTYPE, 'ja_JP.UTF-8'); //basename マルチバイト対策&ディレクトリトラバーサル対策
        //$fileName = basename($fileName);
        $filepath = pathinfo($fileName);
        $createName = "{$newName}.{$filepath['extension']}";
        if(!$this->imageOrientation($fileTmpName, $dirPath."/".$createName, 1000)){
          //exif_read_data 取得できない場合
          move_uploaded_file($fileTmpName, $dirPath."/".$createName);
        }
      }
    } catch (\Exception $e){
      return $e->getMessage();
    }
    return $createName;
  }
  
  /*
   * ディレクトリ権限の変更
   * @param string $dirPath
   * @return boolean
   */
  public function chOwn(string $dirPath, $userName){
    try {
      if(file_exists($dirPath) != true){
        return false;
      }
      if (chown($dirPath, $userName) != true) {
        return false;
      }
    } catch (\Exception $e){
      return $e->getMessage();
    }
    return true;
  }

  /*
   * xmlロード関数（youtube チャンネルとか）
   * ※jquery -> xml読込の場合 クロスドメインエラーが発生するためPHP側で取得する
   * 参考 https://developer.yukimonkey.com/article/20200227/
   * ※simplexml_load_stringでは、名前空間「:」は取得できないため、file_get_contentsする
   * @param string $xml
   * @return object
   */
  public function xmlLoad($xml){
    $dd = file_get_contents($xml);
    $dd = preg_replace( "/<([^>]+?):(.+?)>/", "<$1_$2>", $dd );
    $dd = preg_replace( "/_\/\//", "://", $dd );
    return simplexml_load_string( $dd, 'SimpleXMLElement', LIBXML_NOCDATA );
  }
  
  /*
   * Exif情報のOrientationによって画像を回転して保存
   * @param string $filename
   * @param string $output
   * @param int $maxsize
   * @return true or false
   */
  public function imageOrientation($filename, $output, $maxsize = null){
    /* 画像情報取得（参考例）
    [FileSize]=>27233
    [MimeType]=>image/jpeg
    */
    $exif = @exif_read_data($filename);
    if(empty($exif)) return false;
    if(!$output) return false;

    $type = $exif['MimeType'];
    $w = $exif['COMPUTED']['Width'];
    $h = $exif['COMPUTED']['Height'];

    //画像ロード
    $image = '';
    switch($type){
      case 'image/gif':
        $image = imagecreatefromgif($filename);
        break;
      case 'image/jpeg':
        $image = imagecreatefromjpeg($filename);
        break;
      case 'image/png':
        $image = imagecreatefrompng($filename);
        break;
      case 'image/bmp':
        $image = imagecreatefromwbmp($filename);
        break;
      case 'image/webp':
        $image = imagecreatefromwebp($filename);
        break;
      default:
        return false;
    }

    //画像縮小
    if($maxsize && $w && $h && ( $w > $maxsize || $h > $maxsize )){
      if($w > $h){
        $per = $maxsize/$w;
      }else{
        $per = $maxsize/$h;
      }

      $re_w = $w*$per;
      $re_h = $h*$per;

      // 新しく描画するキャンバスを作成
      $canvas = imagecreatetruecolor($re_w, $re_h);
      imagecopyresampled($canvas, $image, 0,0,0,0, $re_w, $re_h, $w, $h);
      $image = $canvas;
    }

    //Orientation
    //がある場合は回転処理
    if(!empty($exif['Orientation'])) {
      //回転角度
      $degrees = 0;
      switch($exif['Orientation']) {
        case 1:		//回転なし（↑）
          break;
        case 8:		//右に90度（→）
          $degrees = 90;
          break;
        case 3:		//180度回転（↓）
          $degrees = 180;
          break;
        case 6:		//右に270度回転（←）
          $degrees = 270;
          break;
        case 2:		//反転　（↑）
          $mode = IMG_FLIP_HORIZONTAL;
          break;
        case 7:		//反転して右90度（→）
          $degrees = 90;
          $mode = IMG_FLIP_HORIZONTAL;
          break;
        case 4:		//反転して180度なんだけど縦反転と同じ（↓）
          $mode = IMG_FLIP_VERTICAL;
          break;
        case 5:		//反転して270度（←）
          $degrees = 270;
          $mode = IMG_FLIP_HORIZONTAL;
          break;
      }
      //反転(2,7,4,5)
      if (isset($mode)) {
        $image = imageflip($image, $mode);
      }
      //回転(8,3,6,7,5)
      if ($degrees > 0) {
        $image = imagerotate($image, $degrees, 0);
      }
    }

    //保存
    //gettype($image) => PHP7.4 resource / PHP8 object
    $image_resource = false;
    if(PHP_VERSION_ID > 80000){
      if(isset($image) && is_object($image)){
        $image_resource = true;
      }
    }else{
      if(isset($image) && is_resource($image)){
        $image_resource = true;
      }
    }
    if($image_resource){
      switch($exif['MimeType']){
        case 'image/gif':
          imagegif($image, $output);
          break;
        case 'image/jpeg':
          imagejpeg($image, $output);
          break;
        case 'image/png':
          imagepng($image, $output);
          break;
        case 'image/bmp':
          imagewbmp($image, $output);
          break;
        case 'image/webp':
          imagewebp($image, $output);
          break;
      }
      //メモリ解放
      imagedestroy($image);
    }
    $canvas_resource = false;
    if(PHP_VERSION_ID > 80000){
      if(isset($canvas) && is_object($canvas)){
        $canvas_resource = true;
      }
    }else{
      if(isset($canvas) && is_resource($canvas)){
        $canvas_resource = true;
      }
    }
    if($canvas_resource){
      imagedestroy($canvas);
    }
    return true;
  }
    
  /*
   * マスタテーブル
   */
  public function masterLoader(string $name){
    $array = array();
    switch($name){
      case 'tax_rate' :
        $from = "m_tax_rate_tbl";
        $db = new dbRepository;
        $db->setSelect("*");
        $db->setFrom("m_tax_rate_tbl");
        $db->setWhere("delete_kbn is null");
        break;
      case 'prefectures' :
        $from = "m_prefectures_tbl";
        $db = new dbRepository;
        $db->setSelect("*");
        $db->setFrom("m_prefectures_tbl");
        $db->setWhere("delete_kbn is null");
        break;
      case 'temperature_zone' :
        $from = "m_temperature_zone_tbl";
        $db = new dbRepository;
        $db->setSelect("*");
        $db->setFrom("m_temperature_zone_tbl");
        $db->setWhere("delete_kbn is null");
        break;
      default :
        return $array;
        break;
    }
    $q = $db->getSelect().$db->getFrom().$db->getWhere().$db->getGroupBy().$db->getOrder().$db->getPageLimit();
    try {
      $db->connect();
      $stmt = $db->prepare($q);
      $stmt->execute($db->getValue());
      while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
        $array[] = $d;
      }
    } catch (\Exception $e) {
      $this->set_message($e->getMessage());
    }
    return $array;
  }
  /*
   * エクセルダウンロード処理
   * 生成するファイル名, データ型[0=>[a,b,c],1=>[a,b,c]]
   */
  public function downloadCsv($newFileName, $data){
    try {
      //CSV形式で情報をファイルに出力のための準備
      $csvFileName = '/tmp/' .$newFileName. '.csv';
      $fileName = $newFileName. '.csv';
      $res = fopen($csvFileName, 'w');
      if ($res === FALSE) {
        throw new \Exception('ファイルの書き込みに失敗しました。');
      }
      
      // 末尾改行コードをCR+LFにするフィルタクラスをストリームフィルタとして登録
      stream_filter_register('CrlfFilter', CrlfFilter::class);
      stream_filter_append($res, 'CrlfFilter');
      foreach($data as $dataInfo) {
        // 文字コード変換。エクセルで開けるようにする
        // $dataInfoの中身はこんな感じ ["id", "name", "email", "password"]
        // SJIS では環境依存文字が化けるため、SJIS-win となる
        mb_convert_variables('SJIS-win', 'UTF-8', $dataInfo);
        fputcsv($res, $dataInfo);
      }
      
      fclose($res);
      // ダウンロード開始
      // ファイルタイプ（csv）
      header('Content-Type: application/octet-stream');
      // ファイル名
      header('Content-Disposition: attachment; filename=' . $fileName); 
      // ファイルのサイズ　ダウンロードの進捗状況が表示
      header('Content-Length: ' . filesize($csvFileName)); 
      header('Content-Transfer-Encoding: binary');
      // ファイルを出力する
      readfile($csvFileName);
      unlink($csvFileName);
    } catch(Exception $e) {
      // 例外処理をここに書きます
      echo $e->getMessage();
    }
  }
  /*
   * エクセルアップロード処理
   * 生成するファイル名, データ型[0=>[a,b,c],1=>[a,b,c]]
   */
  public function importCsv($file, $filter, $encode = 'SJIS-win'){
    $result = array('_status'=> false, '_message'=> array(), 'row'=> array());
    $column_len = count($filter);
    if(!$file || !$file['import']){
      $result['_message'][] = "データが見つかりませんでした。";
      return $result;
    }
    
    try {
      
      $row = 0;
      // ファイルが存在しているかチェックする
      if (($handle = fopen($file['import']['tmp_name'], "r")) !== FALSE) {
        // 1行ずつfgetcsv()関数を使って読み込む
        while (($data = fgetcsv($handle))) {
          if($row == 0){
            $row++;
            continue;
          }
          /*
           * 【mac注意】macでexcelは、環境依存文字で文字化けするため、macのNumbersで編集し、UTF-8でOK
           * しかし、インポートの際にUTF-8の読み込みを行っていない。（UTF8の場合、エクセル側で文字化けする）
           */
          $enc = mb_detect_encoding($data[0], $encode, false);
          //$enc = mb_detect_encoding($data[0], "UTF-8, shift-jis, eucjp-win, sjis-win", false);
          if(!$enc){
            $result['_message'][] = "ファイル文字コードが取得できません。";
            return $result;
          }
          $data = mb_convert_encoding($data, 'UTF-8', $enc);
          if($column_len != count($data)){
            $result['_message'][] = "${row}行目：列数が合致しませんでした。(CSV:".count($data)."/Field:".$column_len.")";
            return $result;
          }
          
          $col = 0;
          foreach ($filter as $column=> $value) {
            $result['row'][$row][$column] = filter_var( $data[$col], FILTER_SANITIZE_SPECIAL_CHARS);
            if($value == 'int' && ($data[$column] && !is_numeric($data[$column]))){
              $result['_message'][] = "${row}行目${col}列目：値が正しくありません。";
              return $result;
            }
            $col++;
          }
          $row++;
        }
        fclose($handle);
      }
      
      $result['_status'] = true;
      return $result;
    } catch(Exception $e) {
      // 例外処理をここに書きます
      echo $e->getMessage();
    }
  }
  
  /*
   * 配列をソートする
   */
  public function sortByKey($key_name, $sort_order, $array) {
    foreach ($array as $key => $value) {
      $standard_key_array[$key] = $value[$key_name];
    }
    array_multisort($standard_key_array, $sort_order, $array);
    return $array;
  }
  
  
}

// ?>