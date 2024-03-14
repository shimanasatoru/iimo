<?php 
namespace entity;

trait templateEntity{
  
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
    $data = filter_var_array($post, [
      'token' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'site_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'site_directory' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'theme' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'directory' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'contents' => [
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
  
  public $directory;
  public function getDirectory(){
    return $this->directory;
  }
  public function setDirectory(string $directory) :void{
    $this->directory = $directory;
  }

  public $design_theme;
  public function getDesignTheme(){
    return $this->design_theme;
  }
  public function setDesignTheme($design_theme) :void{
    $this->design_theme = $design_theme;
  }
  
  public $design_directory;
  public function getDesignDirectory(){
    return $this->design_directory;
  }
  public function setDesignDirectory(string $design_directory) :void{
    $this->design_directory = $design_directory;
  }
  
  public $design_file;
  public function getDesignFile(){
    return $this->design_file;
  }
  public function setDesignFile(string $design_file) :void{
    $this->design_file = $design_file;
  }
  
  public $output_type;
  public function getOutputType(){
    return $this->output_type;
  }
  public function setOutputType(string $output_type) :void{
    $this->output_type = $output_type;
  }
  
  public $level_stop;
  public function getLevelStop(){
    return $this->level_stop;
  }
  public function setLevelStop(int $level_stop) :void{
    $this->level_stop = $level_stop;
  }
  
}

// ?>