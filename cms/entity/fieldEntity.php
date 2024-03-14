<?php 
namespace host\cms\entity;

trait fieldEntity{
  
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
      'rank' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>0)
      ],
      'release_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'variable' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'required' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'field_type' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'detail' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'column_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'column_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'column_type' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'column_detail' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'flags'  => FILTER_FORCE_ARRAY,
        'options'=> array('default'=>null)
      ],
      'attention' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'delete_kbn' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ]
    ]);
    
    //チェックボックス、ラジオ、セレクトは改行を配列へ変換
    if(in_array($data['field_type'], ['input_checkbox', 'input_radio', 'select'])){
      $data['detail'] = explode('&#13;&#10;', $data['detail']);
    }
    if(is_array($data['column_detail']) && in_array($data['field_type'], ['table'])){
      foreach($data['column_detail'] as $key => $value){
        if(in_array($data['column_type'][$key], ['input_checkbox', 'input_radio', 'select'])){
          $data['column_detail'][$key] = explode('&#13;&#10;', $value);
        }
      }
    }

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
  
  public function getFieldType(){
    $field_type = array(
      ['type' => "input_text", 'name' => "テキスト型：文字"],
      ['type' => "input_number", 'name' => "テキスト型：数字"],
      ['type' => "input_tel", 'name' => "テキスト型：電話番号"],
      ['type' => "input_email", 'name' => "テキスト型：メールアドレス型"],
      ['type' => "input_reply_email", 'name' => "テキスト型：メールアドレス型（返信先）"],
      ['type' => "input_date", 'name' => "テキスト型：日付型(年月日)"],
      ['type' => "input_time", 'name' => "テキスト型：日時型(時分)"],
      ['type' => "input_datetime", 'name' => "テキスト型：日付日時型(年月日時分)"],
      ['type' => "input_url", 'name' => "テキスト型：URL型"],
      ['type' => "textarea", 'name' => "テキストエリア型"],
      ['type' => "textarea_ckeditor", 'name' => "テキストエリア型：CKエディタ"],
      ['type' => "input_checkbox", 'name' => "チェックボックス型"],
      ['type' => "input_radio", 'name' => "ラジオボタン型"],
      ['type' => "select", 'name' => "セレクト型"],
      ['type' => "input_file", 'name' => "ファイル型"],
      ['type' => "table", 'name' => "テーブル", 'columns' => array(
        ['type' => "input_text", 'name' => "テキスト型：文字"],
        ['type' => "input_number", 'name' => "テキスト型：数字"],
        ['type' => "input_tel", 'name' => "テキスト型：電話番号"],
        ['type' => "input_email", 'name' => "テキスト型：メールアドレス型"],
        ['type' => "input_date", 'name' => "テキスト型：日付型(年月日)"],
        ['type' => "input_time", 'name' => "テキスト型：日時型(時分)"],
        ['type' => "input_datetime", 'name' => "テキスト型：日付日時型(年月日時分)"],
        ['type' => "input_url", 'name' => "テキスト型：URL型"],
        ['type' => "textarea", 'name' => "テキストエリア型"],
        ['type' => "input_checkbox", 'name' => "チェックボックス型"],
        ['type' => "input_radio", 'name' => "ラジオボタン型"],
        ['type' => "select", 'name' => "セレクト型"],
        ['type' => "input_file", 'name' => "ファイル型"],
      )],
    );
    return json_decode(json_encode($field_type, JSON_UNESCAPED_UNICODE));
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
  
}

// ?>