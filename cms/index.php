<?php 
ini_set( 'display_errors', 1 );
date_default_timezone_set('Asia/Tokyo');
require_once dirname(__DIR__, 1)."/config/config.php";
require_once dirname(__DIR__, 1)."/core/bootstrap.php";

$autoload = new autoload;
$autoload->setSystemRoot(DIR_CMS);
$autoload->dispatch();
// ?>