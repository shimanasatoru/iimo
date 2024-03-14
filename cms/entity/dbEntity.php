<?php 
namespace host\cms\entity;

trait dbEntity{
  /*
   * @param array $_status
   */
  public $_status = false;
  public function set_status(bool $_status) :void{
    $this->_status = $_status;
  }
  public function get_status() {
    return $this->_status;
  }

  /*
   * @param array $_message
   */
  public $_message;
  public function set_message(string $value) :void{
    $this->_message[] = $value;
  }
  public function get_message() :array {
    return $this->_message;
  }
  /*
   * @param array $_valid
   */
  public $_valid;
  public function set_valid(string $key , string $value){
    if(!$this->_valid) $this->_valid = new \stdClass;
    $this->_valid->{$key} = $value;
  }
  public function get_valid(){
    return $this->_valid;
  }
  /*
   * @param array $_invalid
   */
  public $_invalid;
  public function set_invalid(string $key , string $value){
    if(!$this->_invalid) $this->_invalid = new \stdClass;
    $this->_invalid->{$key} = $value;
  }
  public function get_invalid(){
    return $this->_invalid;
  }
  /*
   * @param array $_lastId
   */
  public $_lastId;
  public function set_lastid(int $_lastId) :void{
    $this->_lastId = $_lastId;
  }  
  public function get_lastid(){
    return $this->_lastId;
  }  
  
  private $select;
  public function setSelect(string $select) :void {
    $this->select = $select;
  }
  public function getSelect(){
    $result = null;
    if($this->select){
      $result = "SELECT SQL_CALC_FOUND_ROWS ".$this->select." ";
    }
    return $result;
  }
  
  private $from;
  public function setFrom(string $from) :void {
    $this->from = $from;
  }
  public function getFrom(){
    $result = null;
    if($this->from){
      $result = "FROM ".$this->from." ";
    }
    return $result;
  }

  private $where;
  public function setWhere($e, $tableName=0) :void {
    $this->where[$tableName][] = $e;
  }
  public function getWhere($key = 'AND'){
    $val = null;
    if($this->where && count($this->where) > 1){
      foreach($this->where as $key => $value){
        $val[$key] = ' WHERE (' . implode( ' '.$key.' ', $value ) . ')';
      }
    }elseif($this->where){
      $val = ' WHERE (' . implode( ' '.$key.' ', current($this->where) ) . ')';
    }
    return $val;
  }
  
  private $value;
  public function setValue($e, $val){
    $this->value[$e] = $val;
  }
  public function getValue(){
    return $this->value;
  }
  
  public $page=0;
  public function setPage(int $page){
    $this->page = $page;
  }
  public function getPage(){
    return $this->page;
  }
  
  public $limit;
  public function setLimit(int $limit){
    $this->limit = $limit;
  }
  public function getLimit(){
    return $this->limit;
  }
  
  public function getPageLimit(){
    if($this->limit == null)
      return null;
    return ' LIMIT '. $this->page * $this->limit .','. $this->limit;
  }
  
  private $order;
  public function setOrder(string $order){
    $this->order = $order;
  }
  public function getOrder(){
    if($this->order == null)
      return null;
    return ' ORDER BY '. $this->order;
  }
  
  private $group_by;
  public function setGroupBy(string $group_by){
    $this->group_by = $group_by;
  }
  public function getGroupBy(){
    if($this->group_by == null)
      return null;
    return ' GROUP BY '. $this->group_by;
  }
  
  private $explode_columns;
  private $explode_separator;
  public function setExplodeColumns(array $array, string $separator = '{tab}'){
    $this->explode_columns = $array;
    $this->explode_separator = $separator;
  }
  public function getExplodeColumns(){
    return (object) [
      'columns' => $this->explode_columns, 
      'separator' => $this->explode_separator
    ];
  }
  public function filterExplode(object $data){
    $ex_columns = $this->getExplodeColumns();
    if($ex_columns->columns){
      foreach($ex_columns->columns as $column){
        if($data->$column){
          $data->$column = explode($ex_columns->separator, $data->$column);
        }
      }
    }
    return $data;
  }
  
  private $json_decode_columns;
  public function setJsonDecodeColumns(array $array){
    $this->json_decode_columns = $array;
  }
  public function getJsonDecodeColumns(){
    return $this->json_decode_columns;
  }
  public function filterJsonDecode(object $data){
    $columns = $this->getJsonDecodeColumns();
    if($columns){
      foreach($columns as $column){
        if(is_array($data->{$column})){
          foreach($data->{$column} as $key => $val){
            $json = json_decode($data->{$column}[$key]);
            if($json){
              $data->{$column}[$key] = $json;
            }
          }
          continue;
        }
        $data->{$column} = json_decode($data->{$column});
      }
    }
    return $data;
  }

  public $rowNumber = 0;
  public $totalNumber = 0;//合計数
  public $pageNumber = 0;//ページ数
  public $pageRange;// ページ番号格納する
  public function getAll(){
    if($this->getSelect() && $this->getFrom()){
      $query = $this->getSelect().$this->getFrom().$this->getWhere().$this->getGroupBy().$this->getOrder().$this->getPageLimit();
      try {
        $this->connect();
        $stmt = $this->prepare($query);
        $stmt->execute($this->getValue());
        while($d = $stmt->fetch(\PDO::FETCH_OBJ)){
          $d = $this->filterExplode($d);
          $d = $this->filterJsonDecode($d);
          $this->row[] = $d;
        }
        $this->rowNumber = $stmt->rowCount();
        if($this->rowNumber > 0){
          $this->set_status(true);
        }
        $stmt_allNumber = $this->prepare("SELECT FOUND_ROWS() as `allNumber`");
        $stmt_allNumber->execute();
        while($d = $stmt_allNumber->fetch(\PDO::FETCH_OBJ)){
          $this->totalNumber = $d->allNumber;
        }
        $this->pageRange = $this->getPageRange();
      } catch (\Exception $e) {
        $this->set_message($e->getMessage());
      }
    }
    return $this;
  }

  /*
   * @params $pageRangeNumber => 表示させるページネーション数
   */
	public function getPageRange(int $pageRangeNumber = 2){
    $result = array();
    if($this->limit && $this->row){
      $this->pageNumber = ceil($this->totalNumber / $this->limit);
      $start = max($this->page - $pageRangeNumber, 0);
      $end = min($this->page + $pageRangeNumber, $this->pageNumber - 1);
      if($this->pageNumber == 0){ // 位置：１ページ目の場合
        $end = $pageRangeNumber * 2; // 終点再計算
      }
      for($i = $start; $i <= $end; $i++){
        $result[] = $i;
      }
    }else{
      $result = array(0);
    }
    return $result;
	}
  
  /*
   * クエリを生成
   */
  public function queryCreate($data, $tbl){
    $data = (object) $data;
    if(isset($data->id) && !$data->id){
      unset($data->id);
    }
    $insert_holders = array();
    $update_holders = array();
    $values = array();
    $params = array();
    foreach($data as $key=>$value){
      $insert_holders[] = $key;
      $update_holders[] = $key.'= :'.$key;
      $values[] = ':'.$key;
      $params[':'.$key] = $value;
    }
    
    $query = null;
    if (isset($data->id) && $data->id) {
      $query = "UPDATE {$tbl} SET ". implode(', ', $update_holders) ." WHERE id = '{$data->id}' LIMIT 1 ";
    } else {
      $query = "INSERT INTO {$tbl}". " (". implode(', ', $insert_holders) .") VALUES (". implode(', ', $values) .")";
    }
    return array(
      "query" => $query,
      "params" => $params
    );
  }
  
  /*
   * カラムをコピー
   * @param mixed $array
   * @param int $array['key_column_name'] //キーとするカラム名
   * @param string $array['copy_id'] //コピーするID
   * @param string $array['drop_column_name'] //一時テーブルから消すカラム
   * @param string $array['add_id'] //一時テーブル追加の際、付与する共通のID
   * @param string $array['table_name'] //テーブル名
   * @return void
   */
  public function table_columns_copy($array){
    $array = filter_var_array($array, [
      'key_column_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'copy_id' => [
        'filter' => FILTER_VALIDATE_INT,
        'options'=> array('default'=>null)
      ],
      'drop_column_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'add_id' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ],
      'table_name' => [
        'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
        'options'=> array('default'=>null)
      ]
    ]);
    if(!$array['key_column_name'] || !$array['copy_id'] || !$array['drop_column_name'] || !strlen($array['add_id']) || !$array['table_name']){
      return false;
    }
    
    $last_id = false;
    $this->connect();
    $this->beginTransaction();
    try {
      $query = "CREATE TEMPORARY TABLE tmp SELECT * from {$array['table_name']} where {$array['key_column_name']} = :copy_id AND delete_kbn is null";
      $stmt1 = $this->prepare($query);
      if(!$stmt1->execute([':copy_id'=> $array['copy_id']])){
        $this->rollBack();
      }
      $query = "ALTER TABLE tmp drop {$array['drop_column_name']}";
      $stmt2 = $this->prepare($query);
      if(!$stmt2->execute()){
        $this->rollBack();
      }
      $query = "INSERT INTO {$array['table_name']} SELECT {$array['add_id']},tmp.* FROM tmp";
      $stmt3 = $this->prepare($query);
      if(!$stmt3->execute()){
        $this->rollBack();
      }
      $last_id = $this->lastInsertId();
      $query = "DROP TABLE tmp";
      $stmt4 = $this->prepare($query);
      if(!$stmt4->execute()){
        $this->rollBack();
      }
    } catch (\PDOException $e){
      $this->rollBack();
      exit($e->getMessage());
    }
    $this->commit();
    return $last_id;
  }
}

// ?>