<?php 
ini_set( 'display_errors', 1 );
date_default_timezone_set('Asia/Tokyo');
require_once dirname(__DIR__, 1)."/config/config.php";
require_once dirname(__DIR__, 1)."/core/bootstrap.php";

//サイト側 php-fpm使用率が高いので cms側のみとする
ini_set('session.gc_maxlifetime', 216000);
ini_set('session.cookie_lifetime', 216000);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
@session_start();

$autoload = new autoload;
$autoload->setSystemRoot(DIR_CMS);
$autoload->dispatch();
// ?>