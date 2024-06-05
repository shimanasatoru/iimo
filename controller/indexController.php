<?php 
namespace controller;

//use Twig;
use Smarty;
use host\cms\repository\utilityRepository;
use host\cms\repository\sitesRepository;
use host\cms\repository\navigationRepository;
use host\cms\repository\pageRepository;
use host\cms\repository\pageStructureRepository;
use host\cms\repository\pageModuleRepository;
use host\cms\repository\mailFormRepository;
use host\cms\repository\productRepository;
use controller\objectController;

class indexController{
  
  private $uri;
  public function getUri(){
    return $this->uri;
  }
  public function setUri(string $uri) :void{
    $this->uri = $uri;
  }
  
  private $preview_flg;
  public function getPreviewFlg(){
    return $this->preview_flg;
  }
  public function setPreviewFlg(int $preview_flg) :void{
    $this->preview_flg = $preview_flg;
  }
  
  public $preview_page_post; //途中プレビューページPOST値を保持
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

  private $structure_id;
  public function getStructureId(){
    return $this->structure_id;
  }
  public function setStructureId(int $structure_id) :void{
    $this->structure_id = $structure_id;
  }

  private $module_id;
  public function getModuleId(){
    return $this->module_id;
  }
  public function setModuleId(int $module_id) :void{
    $this->module_id = $module_id;
  }
  
  private $design_authority; //デザイン権限（標準:default）
  public function getDesignAuthority(){
    return $this->design_authority;
  }
  public function setDesignAuthority(string $design_authority) :void{
    $this->design_authority = $design_authority;
  }

  
  /**
   * サイト存在をチェック
   * return array $data
   */
  public function siteExists($param = null){
    $au = new \autoload;
    $siteDirectory = $au->siteDirectory($param);
    if($siteDirectory){
      $si = new sitesRepository;
      $si->setDirectory($siteDirectory);
      $si->setLimit(1);
      $get_site = $si->get();
      if($get_site->rowNumber == 1){
        return $get_site->row[0];
      }
    }
    /*
    //見つからない場合は本部を参照
    $si = new sitesRepository;
    $si->setTopDirectory(1);
    $si->setLimit(1);
    $get_site = $si->get();
    if($get_site->rowNumber == 1){
      return $get_site->row[0];
    }
    */
    return array();
  }
  
  /**
   * ページ存在をチェック
   * @params int $site_id, array $keys = [level=>key], string $columnSearch = 'id'
   * @return array $data
   */
  public function pageExists(int $site_id, array $keys, string $columnSearch = 'id'){
    
    //ログイン認証ページ
    $navigation_id = filter_input(INPUT_POST, 'navigation_id', FILTER_VALIDATE_INT);
    $release_password = filter_input(INPUT_POST, 'release_password', FILTER_SANITIZE_SPECIAL_CHARS);
    if($navigation_id && $release_password){
      $n = new navigationRepository;
      $n->setSiteId($site_id);
      $n->setReleaseId($navigation_id);
      $n->setReleasePassword($release_password);
      $n->releaseOauth();
    }
    
    //ナビゲーション取得
    $n = new navigationRepository;
    $n->setSiteId($site_id);
    if(!$this->getPreviewFlg()){
      $n->setReleaseKbn(1);
    }
    $navigation = $n->get();
    if(!$navigation->row){
      return false;
    }
    $i = 0;
    $data = $navigation->row[$i];
    foreach($keys as $level => $key){
      if($data->children === null || $key == null){
        continue;
      }
      $i = array_search( $key, array_column( $data->children, $columnSearch));
      if($i === false){
        return false;
      }
      $data = $data->children[$i];
    }
    
    //ページ構成を取得する
    $ps = new pageStructureRepository;
    $ps->setSiteId($site_id);
    $ps->setNavigationId($data->id);
    if(!$this->getPreviewFlg()){
      $ps->setReleaseKbn(1);
    }
    if($preview_page_post = $this->getPreviewPagePost()){
      $ps->setPreviewPagePost($preview_page_post);//途中プレビューページポスト値を取得
    }
    if($preview_structure_post = $this->getPreviewStructurePost()){
      $ps->setPreviewStructurePost($preview_structure_post);//途中プレビューストラクチャポスト値を取得
    }
    if($structure_id = $this->getStructureId()){
      $ps->setId($structure_id);
    }
    $data->structures = $ps->get();

    //モジュールプレビューの場合
    if($module_id = $this->getModuleId()){
      $pm = new pageModuleRepository;
      if($this->getDesignAuthority() != "default"){
        $pm->setSiteId($site_id); //デザイン権限オリジナルならsite_idが必要
      }
      $pm->setId($module_id);
      $data->structures = $pm->get();
    }
    
    //公開パスワード判定
    if($data->release_password_status === false){
      $data->elements = new \StdClass;
      return $data;
    }
    
    //お問合せデータ取得
    if($data->format_type == 'formFormat'){
      $mf = new mailFormRepository;
      $mf->setSiteId($site_id);
      if($data->format_id){ //プレビュー時、フォームIDがないとエラーとなるため回避
        $mf->setId($data->format_id);
      }
      $mf->setLimit(1);
      $mail_form = $mf->get();
      if($_POST){
        $mail_form = $mf->pushPost($_POST);
        if($mail_form->_lastId && $mail_form->_status){
          $address = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ) ? "https://" : "http://").$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          if(parse_url($address, PHP_URL_QUERY)){
            $address.= "&thanks";
          }else{
            $address.= "?thanks";
          }
          header("Location: {$address}", true , 301);
          return false;
        }
      }
      $data->elements = $mail_form;
      return $data;
    }
    
    //リスト・固定ページデータ取得
    if(in_array($data->format_type, ['fixedFormat', 'listFormat'])){
      $request = filter_input_array(INPUT_GET, [
        'id' => [
          'filter' => FILTER_VALIDATE_INT,
          'options'=> array('default'=>null)
        ],
        'fid' => [ //fild_id
          'filter' => FILTER_VALIDATE_INT,
          'options'=> array('default'=>null)
        ],
        'ct' => [ //content_type
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'options'=> array('default'=>null)
        ],
        'cv' => [ //content_value
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'options'=> array('default'=>null)
        ],
        'cvs' => [ //content_values
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'flags'  => FILTER_FORCE_ARRAY,
          'options'=> array('default'=>null)
        ],
        'keyword' => [
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'options'=> array('default'=>null)
        ],
        'p' => [
          'filter' => FILTER_VALIDATE_INT,
          'options'=> array('default'=>null)
        ],
      ]);
      
      //全文検索
      if(@$request['keyword']){
        $p = new pageRepository;
        $p->setSiteId($site_id);
        if(!$this->getPreviewFlg()){
          $p->setReleaseKbn(1);
        }
        $p->setKeyword($request['keyword']);
        $data->elements = $p->getSearchResults();
        return $data;
      }

      $p = new pageRepository;
      $p->setSiteId($site_id);
      $p->setNavigationId($data->id);
      $p->setPageUrl($data->url);
      if(!$this->getPreviewFlg()){
        $p->setReleaseKbn(1);
      }
      if($preview_page_post = $this->getPreviewPagePost()){
        $p->setPreviewPost($preview_page_post);//途中プレビューページポスト値を取得
      }
      if($data->format_type == 'listFormat'){
        if(@$request['id']){
          $p->setId($request['id']);
        }else{
          //fid + cvs = カテゴリ検索的
          if(@$request['fid']){
            $p->setFieldId($request['fid']);
          }
          if(@$request['ct']){
            $p->setContentType($request['ct']);
          }
          if(@$request['cv']){
            $p->setContentValue($request['cv']);
          }
          if(@$request['cvs']){
            $p->setContentValues($request['cvs']);
          }
          if(@$request['p']){
            $p->setPage($request['p']);
          }
        }
        $data->page_limit = $data->page_limit ? $data->page_limit : 10;
        $p->setLimit($data->page_limit);
      }
      $data->elements = $p->get();
    }

    //商品データ取得
    if($data->format_type == 'shoppingFormat'){
      $request = filter_input_array(INPUT_GET, [
        'id' => [
          'filter' => FILTER_VALIDATE_INT,
          'options'=> array('default'=>null)
        ],
        'keyword' => [
          'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
          'options'=> array('default'=>null)
        ],
      ]);
      $p = new productRepository;
      $p->setSiteId($site_id);
      if(!$this->getPreviewFlg()){
        $p->setReleaseKbn(1);
      }
      $p->setLimit(10);
      if(@$request['id']){
        $p->setId($request['id']);
      }
      $data->elements = $p->get();
      return $data;
    }
    return $data;
  }

  public function indexAction(){
    $uri = $this->getUri();//preview用uri
    $siteData = $this->siteExists($uri);
    if(!$siteData){
      print "サイトが見つかりません。";
      return false;
    }

    //現在のディレクトリを取得する
    $au = new \autoload;
    $thisDirectory = $au->uriExplode($uri, isset($siteData->top_directory) && $siteData->top_directory ? true : false);
    $params = filter_input_array(INPUT_GET);
    $preview = false;//プレビュー判定
    if($this->getPreviewFlg() || $this->getPreviewPagePost() || $this->getPreviewStructurePost()){
      $preview = true;
    }
    
    $tpl = new Smarty;
    $ut = new utilityRepository;
    
    //キャッシュ設定
    $cache_caching = Smarty::CACHING_LIFETIME_CURRENT;
    $cache_compile_check = false; //差分チェックなし（オーバーヘッド負荷）
    $cache_life_time = 86400; //1日
    $cache_file_dir = DIR_SITE . $siteData->directory ."/cache/";
    $compile_dir = DIR_SITE . $siteData->directory ."/templates_c/";
    $cache_file_name = implode("_", $thisDirectory);
    $cache_file_name = "index" . ($cache_file_name ? "_" . $cache_file_name : "") . ".txt";
    
    //パラメータがあったらキャッシュ使用しない
    if(!$params && !$preview){
      //キャッシュファイル読込、判定
      $cache_file = $ut->smartyLoadCacheFile($cache_file_dir . $cache_file_name);
      if($cache_file->template && $cache_file->site_id && $cache_file->page_id){
        $tpl->setCaching($cache_caching);
        $tpl->setCacheDir($cache_file_dir);
        $tpl->setCompileDir($compile_dir);
        $tpl->setCompileCheck($cache_compile_check);
        $tpl->setCacheLifetime($cache_life_time);
        if($tpl->isCached($cache_file->template, $cache_file->site_id, $cache_file->page_id)){
          $tpl->display($cache_file->template, $cache_file->site_id, $cache_file->page_id);
          exit;
        }
      }
    }
    
    //キャッシュなし、データ取得処理
    //現在のディレクトリからページデータを取得する
    $pageData = $this->pageExists($siteData->id, $thisDirectory, 'directory_name');

    //テンプレート読込
    $template = 'default.tpl';
    if($siteData->design_authority == "default"){
      $template_dir = "{$siteData->design_directory}{$siteData->design_theme}/files/templates/";
    }else{
      $template_dir = DIR_SITE.($siteData->directory ? $siteData->directory.'/' : null)."design/{$siteData->design_theme}/files/templates/";
    }
    if($pageData->template_name){
      $template = $pageData->template_name;
    }
    $template_dir.= $template;
    
    //キャッシュ生成（固定ページ＋パラメータなし）
    if(!$params && !$preview && $pageData->format_type == "fixedFormat"){
      $tpl->setCaching($cache_caching);
      $tpl->setCacheDir($cache_file_dir);
      $tpl->setCompileDir($compile_dir);
      $tpl->setCompileCheck($cache_compile_check);
      $tpl->setCacheLifetime($cache_life_time);
      $cache_file_text = "{$template_dir},{$siteData->id},{$pageData->id}";
      $ut->smartyCreateCacheFile($cache_file_dir . $cache_file_name, $cache_file_text);
    }

    //出力処理
    $o = new objectController;
    $o->setSiteId($siteData->id);
    $tpl->registerObject('o', $o);//オブジェクトをsmarty登録
    $tpl->assign([
      'token' => $ut->h($ut->generate_token()),
      'siteData' => $siteData,
      'pageData' => $pageData,
      'thisDirectory' => $thisDirectory,
      'previewFlg' => $this->getPreviewFlg()
    ]);
    $tpl->display($template_dir, $siteData->id, $pageData->id);
    exit;

  }
}

// ?>