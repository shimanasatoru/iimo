<?php 
/*
google analytics API をMAMP に設置するまで
1.前提
ライブラリ：google-api-php-client は終了（GA, UA）
ライブラリ：Google Analytics Data API（GA4）となった
2.Google Analytics Data API 導入手順
https://twinkangaroos.com/how-to-run-google-analytics-data-api-ga4-with-php.html
3.クイックスタート
https://developers.google.com/analytics/devguides/reporting/data/v1/quickstart-client-libraries?hl=ja#php
4.MAMPにSSLを設置、両方をみながら設置できた。が関係無いようだ。
https://monotone.jp/archives/22904
https://qiita.com/Stchan/items/824ef4553a9eb21af27e
5.MAMPエラー Uncaught GuzzleHttp\Exception\RequestException: cURL error 60
6.(3対策) google 認証必要が必要
 6-1. ここから cacert.pem をダウンロードして解凍します (クリーンなファイル形式/データ)
 ダメ）https://gist.github.com/VersatilityWerks/5719158/download
 https://qiita.com/f_uto/items/f54ca60230efc7bb00e9
 6-2. C:\MAMP\conf\apache\keys\pgoogle-local-cacert.pem に置きます。
 6-3. php.ini に次の行を追加します curl.cainfo = "C:\MAMP\conf\apache\keys\pgoogle-local-cacert.pem"
 6-4.ウェブサーバー/Apacheを再起動します。
7.API のディメンションと指標
https://developers.google.com/analytics/devguides/reporting/data/v1/api-schema?hl=ja
*/
namespace host\cms\repository;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;

class googleAnalyticsDataApiRepository {
  
  public function __construct () {
    $this->credentials = null;
    $this->property_id = null;
    $this->rows = array();
  }
  
  private $credentials;
  public function setCredentials(string $credentials) :void{
    $this->credentials = $credentials;
  }
  private function getCredentials(){
    return $this->credentials;
  }
  
  private $property_id;
  public function setPropertyId(int $property_id) :void{
    $this->property_id = $property_id;
  }
  private function getPropertyId(){
    return $this->property_id;
  }
  
  public $start_period;
  public function getStartPeriod(){
    return $this->start_period;
  }
  public function setStartPeriod(string $start_period) :void{
    $options = array('options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/"));
    $this->start_period = filter_var($start_period, FILTER_VALIDATE_REGEXP, $options);
  }
  
  public $end_period;
  public function getEndPeriod(){
    return $this->end_period;
  }
  public function setEndPeriod(string $end_period) :void{
    $options = array('options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/"));
    $this->end_period = filter_var($end_period, FILTER_VALIDATE_REGEXP, $options);
  }
  
  public $report_type;
  public function getReportType(){
    return $this->report_type;
  }
  public function setReportType(int $report_type) :void{
    $this->report_type = $report_type;
  }

  /*
   * ログイン認証
   * @return object array mixed $this
   */
  public function get(){
    
    //$credentials = "./xxx.json";
    //$property_id = 'YOUR-GA4-PROPERTY-ID';
    $credentials = $this->credentials;
    $property_id = $this->property_id;
    $report_type = $this->report_type;
    
    if(!$credentials || !$property_id){
      $this->set_message("認証ファイルまたは、プロパティを登録してください。");
      return $this;
    }
    if (!file_exists($credentials)){
      $this->set_message("認証ファイルを登録してください。");
      return $this;
    }

    // Using a default constructor instructs the client to use the credentials
    // specified in GOOGLE_APPLICATION_CREDENTIALS environment variable.
    $client = new BetaAnalyticsDataClient([
      'credentials' => $credentials
    ]);
    
    switch($report_type){
      default:
        $report_method = [
          'property' => 'properties/' . $property_id,
          'dateRanges' => [
            new DateRange([
              //'start_date' => '30daysAgo',
              //'end_date' => 'yesterday',
              'start_date' => $this->start_period,
              'end_date' => $this->end_period,
            ]),
          ],
          'dimensions' => [
            //new Dimension(['name' => 'pagePath']),
            //new Dimension(['name' => 'city']),
            new Dimension(['name' => 'date'])
          ],
          'metrics' => [
            //new Metric(["name" => "activeUsers"]),
            //new Metric(["name" => "active28DayUsers"]),
            //new Metric(["name" => "sessions"]),
            new Metric(["name" => "screenPageViews"]),
            //new Metric(["name" => "averageSessionDuration"]),
            //new Metric(["name" => "newUsers"]),
            new Metric(["name" => "totalUsers"])
          ],
          /*
          'dimensionFilter' => new FilterExpression([
            'filter' => new Filter([
              'field_name' => 'pagePath',
              'string_filter' => new StringFilter([
                'match_type' => MatchType::BEGINS_WITH,
                //'value' => '/conversion',
              ])
            ]),
          ]),
          */
        ];
        try {
          $response = $client->runReport($report_method);
          foreach ($response->getRows() as $key => $row) {
            $this->row[$key]['date'] = $row->getDimensionValues()[0]->getValue();
            $this->row[$key]['pageViews'] = $row->getMetricValues()[0]->getValue();
            $this->row[$key]['user'] = $row->getMetricValues()[1]->getValue();  
          }
        } catch (\Exception $e){
          $this->_message[] = 'googleAnalyticsDataAPI認証に失敗しました。';
        }
        break;
    }
    return $this;
  }
  
}
// ?>