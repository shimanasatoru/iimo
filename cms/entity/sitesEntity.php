<?php 
namespace host\cms\entity;

trait sitesEntity{

  public $row;
  public $design_authority;
  //Design authority
  public function __construct () {
    $this->row = array();
    $this->design_authority = (object) array(
      'default' => (object) array(
        'name' => "デフォルト",
      ),
      'original' => (object) array(
        'name' => "オリジナル",
      ),
    );
  }
  
  public function getHtaccessTxt(string $base_directory, string $rule_directory){
$text = "MultiviewsMatch Any
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {$rule_directory}
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . {$rule_directory} [L]
</IfModule>
";
    return $text;
  }
  
  public function getIndexTxt(){
$text = "<?php 
require_once( dirname(__DIR__, 2) . '/index.php' );
// ?>";
    return $text;
  }

  public $id;
  public function setId(int $id) :void{
    $this->id = $id;
  }
  public function getId(){
    return $this->id;
  }
  
  public $agent_id;
  public function setAgentId(int $agent_id) :void{
    $this->agent_id = $agent_id;
  }
  public function getAgentId(){
    return $this->agent_id;
  }
  
  public $account_id;
  public function setAccountId(int $account_id) :void{
    $this->account_id = $account_id;
  }
  public function getAccountId(){
    return $this->account_id;
  }
  
  public $site_directory;
  public function setDirectory(string $site_directory) :void{
    $this->site_directory = $site_directory;
  }
  public function getDirectory(){
    return $this->site_directory;
  }

  public $account;
  public function setAccount(string $account) :void{
    $this->account = $account;
  }
  public function getAccount(){
    return $this->account;
  }
  
  public $keyword = array();
  public function setKeyword(string $keyword) :void{
    $keyword = mb_convert_kana($keyword, 's');
    $ary_keyword = preg_split('/[\s]+/', $keyword, -1, PREG_SPLIT_NO_EMPTY);
    $this->keyword = $ary_keyword;
  }
  public function getKeyword() : array{
    return $this->keyword;
  }
  
}

// ?>