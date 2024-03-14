<?php 
/*
 * samesite 対策
 * 2020/10/10 ※他ブラウザではバグがあるため chrome の場合のみcookieをsamesite=none とする
 */
if(!empty($_SERVER['HTTPS'])){
  //※サイトがhttpsではない場合は、sessionが毎回書き換わるため使用できない
  $ua = getenv('HTTP_USER_AGENT');
  if (strstr($ua, 'Edge') || strstr($ua, 'Edg')) { //Microsoft Edge
  } elseif (strstr($ua, 'Trident') || strstr($ua, 'MSIE')) { //Microsoft Internet Explorer
  } elseif (strstr($ua, 'OPR') || strstr($ua, 'Opera')) { //Opera
  } elseif (strstr($ua, 'Chrome')) { //Google Chrome
    $_cp = session_get_cookie_params();
    session_set_cookie_params($_cp['lifetime'], $_cp['path'] . '; SameSite=None', $_cp['domain'], true, true);
  } elseif (strstr($ua, 'Firefox')) { //Firefox
  } elseif (strstr($ua, 'Safari')) { //Safari
  }
}

ini_set('session.gc_maxlifetime', 216000);
ini_set('session.cookie_lifetime', 216000);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
@session_start();

// php version check
if (!preg_match('/^(\d+\.\d+)/', PHP_VERSION, $ver) || ($ver[1] < 5.3))
  die("あなたのPHP " . PHP_VERSION . " 、当サービスに必要なバージョンは5.3以上となります。");

// safe mode check
if (ini_get("safe_mode"))
  die("php.ini 設定 -> safe_mode がオンになっています、セーフモードでは実行出来ません。");

require_once __DIR__."/autoload.php";
// ?>