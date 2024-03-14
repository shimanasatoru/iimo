<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\googleAnalyticsDataApiRepository;

class googleAnalyticsDataController{
  
  public $site;
  public function __construct (){
    $this->site = $_SESSION['site'];
  }

  public function getAction(){
    if(!$this->site || !$this->site->id){
      return $this;
    }

    $ut = new utilityRepository;
    $request = filter_input_array(INPUT_GET, [
      'dataType' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'start_day' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS
      ],
      'end_day' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS
      ],
    ]);
    if(!$ut->dateCheck(@$request['start_day'])){
      $request['start_day'] = date('Y-m-d', strtotime('-10 day'));
    }
    if(!$ut->dateCheck(@$request['end_day'])){
      $request['end_day'] = date('Y-m-d');
    }

    $g = new googleAnalyticsDataApiRepository;
    $g->setCredentials(__DIR__ .'/Quickstart-3007ae844e24.json');
    $g->setPropertyId('423203708');
    $g->setStartPeriod($request['start_day']);
    $g->setEndPeriod($request['end_day']);
    $result = $g->get();    
    if(@$request['dataType'] == 'json'){
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $result;
  }
}

// ?>