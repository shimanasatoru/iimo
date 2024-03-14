<?php 
namespace entity;

trait accountEntity{
  
  public $row;
  public $permissions;
  public function __construct () {
    $this->row = array();
    $this->permissions = (object) array(
      'administrator' => (object) array(
        'name' => "システム管理者",
        'badge' => "<span class=\"badge badge-info\">システム管理者</span>",
        'level' => 99
      ),
      'agent' => (object) array(
        'name' => "エージェント",
        'badge' => "<span class=\"badge badge-warning\">エージェント</span>",
        'level' => 3
      ),
      'manager' => (object) array(
        'name' => "サイト管理者",
        'badge' => "<span class=\"badge badge-primary\">サイト管理者</span>",
        'level' => 2
      ),
      'staff' => (object) array(
        'name' => "スタッフ",
        'badge' => "<span class=\"badge badge-secondary\">スタッフ</span>",
        'level' => 1
      ),
    );
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
      'parent_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'permissions' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'account' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'password' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
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
  public function setId(int $id) :void{
    $this->id = $id;
  }
  public function getId(){
    return $this->id;
  }
  
  public $parent_id;
  public function setParentId(int $parent_id) :void{
    $this->parent_id = $parent_id;
  }
  public function getParentId(){
    return $this->parent_id;
  }
  
  public $account;
  public function setAccount(string $account) :void{
    $this->account = $account;
  }
  public function getAccount(){
    return $this->account;
  }
  
  public $permissions_array;
  public function setPermissionsArray(string $permissions_array) :void{
    $this->permissions_array[] = $permissions_array;
  }
  public function getPermissionsArray(){
    return $this->permissions_array;
  }
  
  public $id_or_parent_id;
  public function setIdOrParentId(int $id_or_parent_id) :void{
    $this->id_or_parent_id = $id_or_parent_id;
  }
  public function getIdOrParentId(){
    return $this->id_or_parent_id;
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