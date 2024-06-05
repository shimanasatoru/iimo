<?php 
namespace host\cms\entity;

trait navigationEntity{
  
  public $row;
  public function __construct () {
    $this->row = array();
  }
  
  public $post;
  public function getPost(){
    return $this->post;
  }
  // @params array $post, $type null:全カラム or diff:差分
  public function setPost(array $post, $type = null) :void{
    if(!isset($post['rank']) && (!isset($post['id']) || !$post['id'])){
      $post['rank'] = 0;//新規登録時限定::順位を設置する
    }
    $token = filter_var(@$post['token'], FILTER_SANITIZE_SPECIAL_CHARS, [
      'options'=> array('default'=>null)
    ]);
    
    $data = filter_var_array($post, [
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'parent_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'site_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'account_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'rank' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>0)
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'catch' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'comment' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'meta_description' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'meta_keywords' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'directory_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'release_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'release_start_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'release_end_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/]/")
      ],
      'release_password' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'format_type' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'format_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'template_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'page_limit' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'url' => [
        'filter' => FILTER_VALIDATE_URL,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    $accounts = @filter_var_array($post['accounts'], [
      'navigation_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'=> FILTER_REQUIRE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'account_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'=> FILTER_REQUIRE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'=> FILTER_REQUIRE_ARRAY,
        'options'=> array('default'=>null)
      ]
    ]);
    switch($type){
      case 'diff'://引数があるものだけとする
        $diff_data = array();
        $diff_accounts = array();
        if($data){
          foreach($data as $key => $value){
            if(isset($post[$key])){
              $diff_data[$key] = $value;
            }
          }
        }
        if($accounts){
          foreach($accounts as $key => $value){
            if(isset($post['accounts'][$key])){
              $diff_accounts[$key] = $value;
            }
          }
        }
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $diff_data,
          'accounts' => (object) $diff_accounts
        );
        break;
      default :
        $this->post = (object) array(
          'token' => $token,
          'data' => (object) $data,
          'accounts' => (object) $accounts
        );
        break;
    }
  }

  public $id;
  public function getId(){
    return $this->id;
  }
  public function setId(int $id) :void{
    $this->id = $id;
  }
  
  public $navigation_id;
  public function getNavigationId(){
    return $this->navigation_id;
  }
  public function setNavigationId(int $navigation_id) :void{
    $this->navigation_id = $navigation_id;
  }

  public $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }

  public $domain;
  public function getDomain(){
    return $this->domain;
  }
  public function setDomain(string $domain) :void{
    $this->domain = $domain;
  }
  
  public $release_kbn;
  public function getReleaseKbn(){
    return $this->release_kbn;
  }
  public function setReleaseKbn(int $release_kbn) :void{
    $this->release_kbn = $release_kbn;
  }
  
  public $release_id;
  public function getReleaseId(){
    return $this->release_id;
  }
  public function setReleaseId(int $release_id) :void{
    $this->release_id = $release_id;
  }
  
  public $release_password;
  public function getReleasePassword(){
    return $this->release_password;
  }
  public function setReleasePassword(string $release_password) :void{
    $this->release_password = $release_password;
  }
  
  public $release_password_status_id;
  public function getReleasePasswordStatusIds(){
    $return = null;
    if(isset($_SESSION["release_password_status_ids"]) && $_SESSION["release_password_status_ids"]){
      $return = $_SESSION["release_password_status_ids"];
    }
  }
  public function setReleasePasswordStatusId(int $release_password_status_id, bool $status = false) :void{
    if($status){
      $_SESSION["release_password_status_ids"][$release_password_status_id] = true;
    }else{
      unset($_SESSION["release_password_status_ids"][$release_password_status_id]);
    }
  }
  
  /*
   * navigation_to_account_tbl
   */
  public $account_id;
  public function getAccountId(){
    return $this->account_id;
  }
  public function setAccountId(int $account_id) :void{
    $this->account_id = $account_id;
  }
}

// ?>