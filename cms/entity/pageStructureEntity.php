<?php 
namespace host\cms\entity;

trait pageStructureEntity{
  
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
      'module_id' => [
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
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'html' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'o_navigation_id' => [
        'filter' => FILTER_VALIDATE_INT,
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

  public $backup_table_flg;
  public function getBackupTableFlg(){
    return $this->backup_table_flg;
  }
  public function setBackupTableFlg(int $backup_table_flg) :void{
    $this->backup_table_flg = $backup_table_flg;
  }
  
  public $bk_id;
  public function getBkId(){
    return $this->bk_id;
  }
  public function setBkId(int $bk_id) :void{
    $this->bk_id = $bk_id;
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
  
  public $module_id;
  public function getModuleId(){
    return $this->module_id;
  }
  public function setModuleId(int $module_id) :void{
    $this->module_id = $module_id;
  }

  public $release_kbn;
  public function getReleaseKbn(){
    return $this->release_kbn;
  }
  public function setReleaseKbn(int $release_kbn) :void{
    $this->release_kbn = $release_kbn;
  }
  
  public $site_uri;
  public function getSiteUri(){
    return $this->site_uri;
  }
  public function setSiteUri(string $site_uri) :void{
    $this->site_uri = $site_uri;
  }
  
  public $preview_page_post; //途中プレビューPOST値を保持
  public function getPreviewPagePost(){
    return $this->preview_page_post;
  }
  public function setPreviewPagePost(array $preview_page_post = array()) :void{
    $this->preview_page_post = $preview_page_post;
  }
  
  public $preview_structure_post; //途中プレビューストラクチャPOST値を保持
  public function getPreviewStructurePost(){
    return $this->preview_structure_post;
  }
  public function setPreviewStructurePost(array $preview_structure_post = array()) :void{
    $this->preview_structure_post = $preview_structure_post;
  }
}

// ?>