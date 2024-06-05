<?php 
ini_set( 'display_errors', 1 );
date_default_timezone_set('Asia/Tokyo');
require_once __DIR__."/config/config.php";
require_once __DIR__."/core/bootstrap.php";

$autoload = new autoload;
$autoload->setSystemRoot(DIR_HOST);
$autoload->dispatch();
// ?>