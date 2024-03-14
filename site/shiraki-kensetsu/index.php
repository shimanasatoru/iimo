<?php 
if(isset($_POST) 
   && isset($_POST['reserve']['venueName']) 
   && isset($_POST['reserve']['scheduledDate']) 
   && isset($_POST['reserve']['scheduledTime'])){
  
  //必須項目の判定
  $required_flag = 1;
  if(is_array($_POST['required'])){
    foreach($_POST['required'] as $id => $val){
      if(!isset($_POST['post'][$id]) || !$_POST['post'][$id]){
        $required_flag = 0;
      }
    }
  }

  $place_name = $_POST['reserve']['venueName'];
  $visit_date = $_POST['reserve']['scheduledDate'];
  $visit_time = $_POST['reserve']['scheduledTime'];
  $default_limit_quantity = 1;
  $push_flag = true;
  
  if($required_flag && $place_name && $visit_date && $visit_time){
    $upload_dir = __DIR__ . "/design/default/files/reserve/";
    $file_name = "{$place_name}.json";
    $file_data = array();
    if(file_exists($upload_dir.$file_name)){
      $file_data = file_get_contents($upload_dir.$file_name);
      $file_data = json_decode(mb_convert_encoding($file_data, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN'), true);
    }
    if(!isset($file_data['limit_quantity'])){
      $file_data['limit_quantity'] = $default_limit_quantity;
    }
    if(!isset($file_data['data'])){
      $file_data['data'] = array();
    }
    if($file_data['data']){
      foreach($file_data['data'] as $key => $column){
        if($column['date'] == $visit_date && $column['time'] == $visit_time){
          $file_data['data'][$key]['quantity'] += 1;
          $push_flag = false;
        }
      }
    }
    if($push_flag){
      $file_data['data'][] = array(
        "date" => $visit_date,
        "time" => $visit_time,
        "quantity" => 1
      );
    }
    $file_data = json_encode($file_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    file_put_contents($upload_dir.$file_name, $file_data);
  }
}
require_once( dirname(__DIR__, 2) . '/index.php' );
// ?>