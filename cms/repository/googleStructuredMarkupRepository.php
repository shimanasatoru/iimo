<?php 
/*
 * google 構造化マークアップを管理
*/
namespace host\cms\repository;

use host\cms\repository\dbRepository;
use host\cms\repository\utilityRepository;

class googleStructuredMarkupRepository extends dbRepository {
  
  public function __construct () {
    $this->fields = array();
  }
  
  private $fields;
  private function getFields($case = null){
    $result = array();
    switch($case){
      default :
        
        
        
        
        
        break;
    }
    return $result;
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
      $this->_message[] = "認証ファイルまたは、プロパティを登録してください。";
      return $this;
    }
    if (!file_exists($credentials)){
      $this->_message[] = "認証ファイルを登録してください。";
      return $this;
    }

    // Create the client object and set the authorization configuration
    // from the client_secretes.json you downloaded from the developer console.
    $client = new Google_Client();
    // 以下の４行を追記
    $http = new \GuzzleHttp\Client([
      'verify' => __DIR__.'/google_client_key_for_php7/google_local_cacert.pem'
    ]);
    $client->setHttpClient($http);

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