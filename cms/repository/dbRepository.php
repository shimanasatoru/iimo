<?php 
namespace host\cms\repository;
/*
 * PHP Version 7.3.9
 * 参考: https://gist.github.com/landock/2579011
 */
class dbRepository{
  
  use \host\cms\entity\dbEntity;
  
  static private $instance;
  /*
   * @param string $dsn
   * @param string $user
   * @param string $password
   * @param array $options
   * @return PDO
   */    
  public function __construct(){
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET.";";
    $options = [
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ];
    try {
      $this->pdo = new \PDO($dsn, DB_USER, DB_PASSWORD, $options);
    } catch (\PDOException $e) {
      throw new \Exception("データベース接続失敗". $e->getMessage());
    }
  }
  
  /*
   * @return string
   */    
  final public static function connect(){
    if(!(self::$instance instanceof self)){
      self::$instance = new self();
    }
    return self::$instance;
  }

  /*
   * @return string
   */
  final public function __clone(){
    throw new \Exception("このインスタンスはシングルトンクラスです");
  }
    
  /*
   * @return bool
   */
	public function beginTransaction(){
		return self::$instance->pdo->beginTransaction();
	}

  /*
   * @return bool
   */
	public function commit(){
		return self::$instance->pdo->commit();
	}

  /*
   * @return string
   */
  public function errorCode(){
    return self::$instance->pdo->errorCode();
  }

  /*
   * @return array
   */
  public function errorInfo(){
    return self::$instance->pdo->errorInfo();
  }

  /*
   * @param string $statement
   */
  public function exec($statement){
    return self::$instance->pdo->exec($statement);
  }

  /*
   * @param int $attribute
   * @return mixed
   */
  public function getAttribute($attribute){
    return self::$instance->pdo->getAttribute($attribute);
  }

  /*
   * @return array
   */
  public function getAvailableDrivers(){
    return self::$instance->pdo->getAvailableDrivers();
  }
    
  /*
   * @param string $name ※IDが返されるシーケンスオブジェクトの名前
   * @return string
   */
	public function lastInsertId($name =null){
		return self::$instance->pdo->lastInsertId($name);
	}

  /*
   * @param string $statement
   * @param array $options ※PDOステートメントobjの属性値を設定するための1つ以上の key=>valueヘアの配列
   * @return PDO Statement
   */
  public function prepare($statement, $options=false){
    if(!$options) $options=array();
    return self::$instance->pdo->prepare($statement, $options);
  }
    
  /*
   * @param string $statement
   * @return PDO Statement
   */
  public function query($statement){
    return self::$instance->pdo->query($statement);
  }

  /*
   * @param string $statement
   * @return array
   */    
  public function queryFetchAllAssoc($statement){
    return self::$instance->pdo->query($statement)->fetchAll(PDO::FETCH_ASSOC);
  }

  /*
   * @param string $statement
   * @return array
   */
  public function queryFetchRowAssoc($statement){
    return self::$instance->pdo->query($statement)->fetch(PDO::FETCH_ASSOC);    	
  }

  /*
   * @param string $statement
   * @return mixed
   */
  public function queryFetchColAssoc($statement){
    return self::$instance->pdo->query($statement)->fetchColumn();    	
  }

  /*
   * @param string $input
   * @param int $parameter_type
   * @return string
   */
  public function quote ($input, $parameter_type=0){
    return self::$instance->pdo->quote($input, $parameter_type);
  }

  /*
   * @return bool
   */
  public function rollBack(){
    return self::$instance->pdo->rollBack();
  }

  /*
   * @param int $attribute
   * @param mixed $value
   * @return bool
   */
  public function setAttribute($attribute, $value){
    return self::$instance->pdo->setAttribute($attribute, $value);
  }
}

// ?>