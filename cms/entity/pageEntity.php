<?php 
namespace host\cms\entity;

trait pageEntity{
  
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
    $data = filter_var_array($post, [
      'token' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'site_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'navigation_id' => [
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
      'release_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'release_start_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/\s\:]/")
      ],
      'release_end_date' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'options'=> array('default'=>null, "regexp"=> "/[0-9\-\/\s\:]/")
      ],
      'content' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'delete_images' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    switch($type){
      case 'diff'://引数があるものだけとする
        $diff = array();
        foreach($data as $key => $value){
          if(isset($post[$key])){
            $diff[$key] = $value;
          }
        }
        $this->post = $diff;
        break;
      default :
        $this->post = $data;
        break;
    }
  }

  public $bk_content_id;
  public function getBkContentId(){
    return $this->bk_content_id;
  }
  public function setBkContentId(int $bk_content_id) :void{
    $this->bk_content_id = $bk_content_id;
  }

  public $id;
  public function getId(){
    return $this->id;
  }
  public function setId(int $id) :void{
    $this->id = $id;
  }
  
  public $site_id;
  public function getSiteId(){
    return $this->site_id;
  }
  public function setSiteId(int $site_id) :void{
    $this->site_id = $site_id;
  }
  
  public $navigation_id;
  public function getNavigationId(){
    return $this->navigation_id;
  }
  public function setNavigationId(int $navigation_id) :void{
    $this->navigation_id = $navigation_id;
  }
  
  public $navigation_ids;
  public function getNavigationIds(){
    return $this->navigation_ids;
  }
  public function setNavigationIds(array $navigation_ids) :void{
    $this->navigation_ids = $navigation_ids;
  }
  
  public $format_type;
  public function getFormatType(){
    return $this->format_type;
  }
  public function setFormatType(string $format_type) :void{
    $this->format_type = $format_type;
  }
  
  public $page_id;
  public function getPageId(){
    return $this->page_id;
  }
  public function setPageId(int $page_id) :void{
    $this->page_id = $page_id;
  }
  
  public $release_kbn;
  public function getReleaseKbn(){
    return $this->release_kbn;
  }
  public function setReleaseKbn(int $release_kbn) :void{
    $this->release_kbn = $release_kbn;
  }
  
  public $content_id;
  public function getContentId(){
    return $this->content_id;
  }
  public function setContentId(int $content_id) :void{
    $this->content_id = $content_id;
  }
  
  public $field_id;
  public function getFieldId(){
    return $this->field_id;
  }
  public function setFieldId(int $field_id) :void{
    $this->field_id = $field_id;
  }
  
  public $field_value;
  public function getFieldValue(){
    return $this->field_value;
  }
  public function setFieldValue(string $field_value) :void{
    $this->field_value = $field_value;
  }

  public $module_id;
  public function getModuleId(){
    return $this->module_id;
  }
  public function setModuleId(int $module_id) :void{
    $this->module_id = $module_id;
  }

  public $content_type;
  public function getContentType(){
    return $this->content_type;
  }
  public function setContentType(string $content_type) :void{
    $this->content_type = $content_type;
  }
  
  public $content_value;
  public function getContentValue(){
    return $this->content_value;
  }
  public function setContentValue(string $content_value) :void{
    $this->content_value = $content_value;
  }

  public $content_values;
  public function getContentValues(){
    return $this->content_values;
  }
  public function setContentValues(array $content_values) :void{
    $this->content_values = $content_values;
  }
  
  public $restore_datetime;
  public function getRestoreDatetime(){
    return $this->restore_datetime;
  }
  public function setRestoreDatetime(string $restore_datetime) :void{
    $value = filter_var($restore_datetime, FILTER_VALIDATE_REGEXP, ['options'=> array('default'=>null, "regexp"=> "/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/")]);
    if($value !== false){
      $datetime = strtotime($value);
      if($datetime == false){//有効日時を判定
        $value = null;
      }
    }
    $this->restore_datetime = $value;
  }
  
  public $keyword;
  public function getKeyword(){
    return $this->keyword;
  }
  public function setKeyword(string $keyword) :void{
    $this->keyword = $keyword;
  }
  
  public $befor_name; //リンク先の差替前の名称
  public function getBeforName(){
    return $this->befor_name;
  }
  public function setBeforName(string $befor_name) :void{
    $this->befor_name = $befor_name;
  }
  
  public $after_name; //リンク先の差替後の名称
  public function getAfterName(){
    return $this->after_name;
  }
  public function setAfterName(string $after_name) :void{
    $this->after_name = $after_name;
  }
  
  public $preview_post; //プレビューPOST値を保持
  public function getPreviewPost(){
    return $this->preview_post;
  }
  public function setPreviewPost(array $preview_post = array()) :void{
    $this->preview_post = $preview_post;
  }
  
  public $page_url; //ページURLをデータに渡す
  public function getPageUrl(){
    return $this->page_url;
  }
  public function setPageUrl(string $page_url) :void{
    $this->page_url = $page_url;
  }
}

// ?>