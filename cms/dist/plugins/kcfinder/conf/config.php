<?php
/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 3.12
  *    @author Pavel Tzonkov <sunhater@sunhater.com>
  * @copyright 2010-2014 KCFinder Project
  *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
  *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
  *      @link http://kcfinder.sunhater.com
  */

/* IMPORTANT!!! Do not comment or remove uncommented settings in this file
   even if you are using session configuration.
   See http://kcfinder.sunhater.com/install for setting descriptions */
/*
 * エラー記録 20191121
 * kcfinderを全てアップロードした際に、表示されなくなった！
 * 原因：uploader.php 250行目 .htaccess の所有者が addasとなっていたため
 * 対処：本番では、.htaccess の所有者を apache とする
 */
ini_set('display_errors', 1);
require_once dirname(__DIR__, 5)."/config/config.php";

$disabled = true;
$uploadDir = '';
$uploadURL = '';
if($_SESSION['user'] && $_SESSION['site']->directory){
  //サイト作成
  $disabled = false;
  $design_theme = 'default';
  if($_SESSION['site']->design_theme){
    $design_theme = $_SESSION['site']->design_theme;
  }
  $uploadDir = DIR_SITE.$_SESSION['site']->directory.'/design/'.$design_theme;
  $uploadURL = ADDRESS_SITE.$_SESSION['site']->directory.'/design/'.$design_theme;
}
if($_SESSION['user'] && $_SESSION['cms']->design_theme){
  //モジュール作成
  $disabled = false;
  $uploadDir = DIR_CMS.'module/'.$_SESSION['cms']->design_theme;
  $uploadURL = ADDRESS_CMS.'module/'.$_SESSION['cms']->design_theme;
}
if($_SESSION['user']->permissions == 'administrator' && !$_SESSION['site']->id){
  //管理者の場合、お知らせ投稿
  $disabled = false;
  $uploadDir = DIR_CMS.'templates/finder/';
  $uploadURL = ADDRESS_CMS.'templates/finder/';  
}
return array(

  // GENERAL SETTINGS
  'disabled' => $disabled,
  'uploadURL' => $uploadURL,
  'uploadDir' => $uploadDir,
  'theme' => "dark",
  'types' => array(
    // (F)CKEditor types
    'files'   =>  "",
    'flash'   =>  "swf",
    'images'  =>  "*img",

    // TinyMCE types
    'file'    =>  "",
    'media'   =>  "swf flv avi mpg mpeg qt mov wmv asf rm",
    'image'   =>  "*img",
  ),


  // IMAGE SETTINGS
  'imageDriversPriority' => "imagick gmagick gd",
  'jpegQuality' => 90,
  'thumbsDir' => ".thumbs",

  'maxImageWidth' => 0,
  'maxImageHeight' => 0,

  'thumbWidth' => 100,
  'thumbHeight' => 100,

  'watermark' => "",

  // DISABLE / ENABLE SETTINGS
  'denyZipDownload' => false,
  'denyUpdateCheck' => false,
  'denyExtensionRename' => false,


  // PERMISSION SETTINGS
  'dirPerms' => 0775,
  'filePerms' => 0664,
  'access' => array(
    'files' => array(
      'upload' => true,
      'delete' => true,
      'copy'   => true,
      'move'   => true,
      'rename' => true
    ),
    'dirs' => array(
      'create' => true,
      'delete' => true,
      'rename' => true
    )
  ),

  'deniedExts' => "exe com msi bat cgi pl php phps phtml php3 php4 php5 php6 py pyc pyo pcgi pcgi3 pcgi4 pcgi5 pchi6",


  // MISC SETTINGS

  'filenameChangeChars' => array(/*
    ' ' => "_",
    ':' => "."
  */),

  'dirnameChangeChars' => array(/*
    ' ' => "_",
    ':' => "."
  */),

  'mime_magic' => "",

  'cookieDomain' => "",
  'cookiePath' => "",
  'cookiePrefix' => 'KCFINDER_',

  // THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION SETTINGS
  '_sessionVar' => "KCFINDER",
  '_check4htaccess' => true,
  '_normalizeFilenames' => false,
  '_dropUploadMaxFilesize' => 104857600,
  //'_tinyMCEPath' => "/tiny_mce",
  //'_cssMinCmd' => "java -jar /path/to/yuicompressor.jar --type css {file}",
  //'_jsMinCmd' => "java -jar /path/to/yuicompressor.jar --type js {file}",
);
