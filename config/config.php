<?php 
/*
 * 定数
 * ※cronからの通信では、server['HTTP_HOST']が起動しないため php_uname を使用
 * ※テスト環境と本番環境では、address と htaccess と kcfineder/conf/config.php を変更する
 */
if(php_uname('n') == 'www5244.sakura.ne.jp'){
  //本番環境
  error_reporting(E_ALL ^ E_NOTICE);
  define('ENVIRONMENT', 'prod');
  define('SERVICE_NAME', 'iimo');
  define('SERVICE_MAIL', 'admin@iimo.jp');
  define('HOST_NAME', 'イイモ3');
  define('HOST_MAIL', 'admin@iimo.jp');
  define('DIR', '/home/iimo2/www/');
  define('DIR_HOST', DIR);
  define('DIR_LIBRARY', DIR_HOST.'library/');
  define('DIR_CMS', DIR_HOST.'cms/'); //autoloadのルート設定
  define('DIR_SITE', DIR_HOST.'site/'); //autoloadのルート設定
  define('DIR_MODULE', DIR_CMS.'module/');
  define('ADDRESS',  'https://iimo2.sakura.ne.jp/');
  define('ADDRESS_HOST',  ADDRESS);
  define('ADDRESS_CMS',  ADDRESS_HOST.'cms/');
  define('ADDRESS_SITE',  ADDRESS_HOST.'site/');
  define('FIRST_ROOT_CMS', DIR_CMS);  //autoloadの初回ルート設定
  define('FIRST_ROOT_SITE', DIR_SITE);  //autoloadの初回ルート設定
  define('FIRST_CONTROLLER', 'index'); //autoloadの初回コントローラー設定
  //DB
  define('DB_HOST', 'mysql626.db.sakura.ne.jp');
  define('DB_NAME', 'iimo2_db');
  define('DB_USER', 'iimo2');
  define('DB_PASSWORD', 'x99p5SAMTdAi-_');
  define('DB_CHARSET', 'utf8mb4');
  //googleシークレットキー
  define('G_RECAPTCHA_V3_SITEKEY', '6LePXvsnAAAAADxZRjnfHsl9eojVGiXem_eNRf_h');
  define('G_RECAPTCHA_V3_SECRETKEY', '6LePXvsnAAAAAAebhZJyudO60NqPLzxK57-Uc-J2');
  //SMTP
  define('SWIFT_DIR', DIR.'vendor/autoload.php');
  define('SMTP_SERVER_NAME', 'smtp.entry.conoha.io');
  define('SMTP_SERVER_PORT', 587);
  define('SMTP_SERVER_SECURE', 'tls');
  define('SMTP_USER_NAME', 'office@jma29.com');
  define('SMTP_USER_PASSWORD', 'i69-Up2n#');
  define('SMTP_OPTIONS', array('ssl' => array('allow_self_signed' => true, 'verify_peer_name' => false, 'verify_peer' => false)));
}else{
  //開発環境
  error_reporting(E_ALL ^ E_NOTICE);
  define('ENVIRONMENT', 'dev');
  define('SERVICE_NAME', 'iimo');
  define('SERVICE_MAIL', 'admin@iimo.jp');
  define('HOST_NAME', 'イイモ3');
  define('HOST_MAIL', 'admin@iimo.jp');
  define('DIR', 'C:/MAMP/htdocs/iimo/');
  define('DIR_HOST', DIR);
  define('DIR_LIBRARY', DIR_HOST.'library/');
  define('DIR_CMS', DIR_HOST.'cms/'); //autoloadのルート設定
  define('DIR_SITE', DIR_HOST.'site/'); //autoloadのルート設定
  define('DIR_MODULE', DIR_CMS.'module/');
  define('ADDRESS',  'https://localhost/iimo/');
  define('ADDRESS_HOST',  ADDRESS);
  define('ADDRESS_CMS',  ADDRESS_HOST.'cms/');
  define('ADDRESS_SITE',  ADDRESS_HOST.'site/');
  define('FIRST_ROOT_CMS', DIR_CMS);  //autoloadの初回ルート設定
  define('FIRST_ROOT_SITE', DIR_SITE);  //autoloadの初回ルート設定
  define('FIRST_CONTROLLER', 'index'); //autoloadの初回コントローラー設定
  //DB
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'iimo2_db');
  define('DB_USER', 'root');
  define('DB_PASSWORD', 'root');
  define('DB_CHARSET', 'utf8mb4');
  //googleシークレットキー
  define('G_RECAPTCHA_V3_SITEKEY', '');
  define('G_RECAPTCHA_V3_SECRETKEY', '');
  //SMTP
  define('SWIFT_DIR', DIR.'vendor/autoload.php');
  define('SMTP_SERVER_NAME', 'smtp.entry.conoha.io');
  define('SMTP_SERVER_PORT', 587);
  define('SMTP_SERVER_SECURE', 'tls');
  define('SMTP_USER_NAME', 'office@jma29.com');
  define('SMTP_USER_PASSWORD', 'i69-Up2n#');
  define('SMTP_OPTIONS', array('ssl' => array('allow_self_signed' => true, 'verify_peer_name' => false, 'verify_peer' => false)));
}
require_once DIR_LIBRARY."smarty-3.1.30/libs/Smarty.class.php";
//require_once DIR_LIBRARY."smarty-3.1.30/libs/SmartyBC.class.php";//カスタムの場合
require_once DIR.'vendor/autoload.php';
// ?>