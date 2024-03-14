<?php 
namespace controller;

use host\cms\repository\utilityRepository;
use host\cms\repository\logRepository;
use host\cms\repository\googleAnalyticsDataApiRepository;

class logController{
  
  public $user;
  public $site;
  public $setting;
  public function __construct (){
    $this->user = $_SESSION['user'];
    $this->site = $_SESSION['site'];
    $this->setting = $_SESSION['page_setting'];
  }

  public function getSummaryAction(){
    $user = $this->user;
    $site = $this->site;
    $setting = $this->setting;
    if(!$user || !$site){
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
    
    $l = new logRepository;
    if($site->id){
      $l->setSiteId($site->id);
    }else{
      $l->setSiteIds($user->site_id);
    }
    $l->setStartPeriod($request['start_day']);
    $l->setEndPeriod($request['end_day']);
    $updatesData = $l->getDailyActiveBk();
    
    $googleData = new \StdClass;
    if($setting->ga4_credentials && $setting->ga4_property_id){
      $g = new googleAnalyticsDataApiRepository;
      //$g->setCredentials(__DIR__ .'/Quickstart-3007ae844e24.json');
      //$g->setPropertyId('423203708');
      $g->setCredentials($setting->ga4_credentials);
      $g->setPropertyId($setting->ga4_property_id);
      $g->setStartPeriod($request['start_day']);
      $g->setEndPeriod($request['end_day']);
      $googleData = $g->get();
    }

    $summaryData = array();
    foreach($updatesData->row as $key => $u){
      $date = date("Ymd", strtotime($u->day));
      $summaryData[$key]['date'] = $u->day;
      $summaryData[$key]['updatesData'] = $u->counter;
      if(isset($googleData->row)){
        $i = array_search($date, array_column($googleData->row, 'date'), true);
        $summaryData[$key]['pageViewsData'] = $googleData->row[$i]['pageViews'];
        $summaryData[$key]['userViewsData'] = $googleData->row[$i]['user'];
      }
    }
    if(@$request['dataType'] == 'json'){
      echo json_encode($summaryData, JSON_UNESCAPED_UNICODE);
      return true;
    }
    return $summaryData;
  }
}

// ?>