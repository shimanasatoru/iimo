<?php 
ini_set( 'display_errors', 1 );
require_once dirname(__DIR__, 1)."/config/config.php";
require_once dirname(__DIR__, 1).'/cms/repository/utilityRepository.php';

use \host\cms\repository\utilityRepository;

//バックアップ対象ディレクトリ（例:/home/sakuraName/www）
$sourceDir = rtrim(DIR, "/");

//バックアップ先ディレクトリ（バックアップ除外）（例：/home/sakuraName/www/bk）
$backupFixDir = $sourceDir . '/bk';

//バックアップ先
$backupDir = $backupFixDir .'/'. date('Ymd');

// バックアップ名
$backupName = $backupDir .'/backup_' . date('YmdHis') . '.zip';

// バックアップファイル名
$backupFileName = $backupDir .'/app_' . date('YmdHis') . '.zip';

// バックアップファイル名
$backupSqlName = $backupDir .'/db_' . date('YmdHis') . '.sql';

//パスワード
$backupPassword = 'YourPassword';

//DB接続情報
$host = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$database = DB_NAME;

//排他的処理
$lockfile = $backupFixDir . '/lockfile.txt';
$handle = fopen($lockfile, 'w');
if (flock($handle, LOCK_EX)) {
  // ここに排他的な処理を記述
  
  $ut = new utilityRepository;

  //バックアップディレクトリ生成
  if(is_dir($backupDir)){
    echo "本日分は既に生成済み";
    return false;
  }
  if(!$ut->createDir($backupDir)){
    echo "ディレクトリ生成失敗";
    return false;
  }
  
  //過去のバックアップを除去
  $date = new \DateTime('now');
  $date->modify('-3 day');
  $historyDir = glob($backupFixDir . '/*', GLOB_ONLYDIR);
  foreach($historyDir as $path){
    $parts = explode('/', $path);
    $lastPart = end($parts);
    //日付部分を取得
    $pathDate = substr($lastPart, -8);
    if(is_int($pathDate) && $date->format('Ymd') >= $pathDate){
      $ut->removeDir($path);
    }
  }

  // mysqldumpコマンドを実行
  $dumpCommand = "mysqldump --host=$host --user=$username --password=$password $database > $backupSqlName";
  exec($dumpCommand, $output, $exitCode);
  if ($exitCode === 0) {
    echo "データベースのバックアップが成功しました。";
  } else {
    echo "データベースのバックアップ中にエラーが発生しました。";
  }

  // バックアップコマンドを実行
  $backupCommand = "zip -r $backupFileName $sourceDir -x \"$backupFixDir/*\"";
  exec($backupCommand, $output, $exitCode);
  if ($exitCode === 0) {
    echo "ファイルのバックアップが成功しました。";
  } else {
    echo "ファイルのバックアップ中にエラーが発生しました。";
  }

  // 併せてパスワード暗号化ZIPを実行
  $backupCommand = "zip -r --encrypt -P $backupPassword $backupName $backupDir";
  exec($backupCommand, $output, $exitCode);
  if ($exitCode === 0) {
    echo "パスワードバックアップが成功しました。";
  } else {
    echo "パスワードバックアップ中にエラーが発生しました。";
  }

  if (file_exists($backupFileName)) {
    unlink($backupFileName);
  }
  if (file_exists($backupSqlName)) {
    unlink($backupSqlName);
  }

  flock($handle, LOCK_UN); // ロック解除
} else {
  // ロックに失敗した場合の処理
  exit;
}
fclose($handle);

return true;
// ?>