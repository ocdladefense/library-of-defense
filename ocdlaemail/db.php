<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

class db {
	private static $self;
	/**
	 * @var PDO $pdo
	 */
	private $pdo;
	protected function __construct(){
		global $dbhost, $dbuser, $dbpass, $dbname;
		try {
			$this->pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass,
				array(PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_LOCAL_INFILE => 1));
		} catch(PDOException $e){
			 die('Connection failed: ' . $e->getMessage());
		}
	}

	/**
	 * @return db
	 */
	public static function instance(){
		if(!isset(db::$self)){
			db::$self = new db();
		}
		return db::$self;
	}

	public function execute($statement, $parameters = array()){
		if(CONFIG_SHOWSQL){
			?><?php print($statement); ?><br/><pre><?php
			print_r($parameters); ?></pre><?php
		}
		$q = $this->pdo->prepare($statement);
		$q->setFetchMode(PDO::FETCH_ASSOC);
		foreach($parameters as $key => $value){
			if(is_int($value)) {
				$param = PDO::PARAM_INT;
			} elseif(is_bool($value)) {
                $param = PDO::PARAM_BOOL;
			} elseif(is_null($value)) {
                $param = PDO::PARAM_INT;
			} elseif(is_string($value)) {
                $param = PDO::PARAM_STR;
			} else {
                $param = FALSE;
			}

            if($param){
            	$q->bindValue($key,$value,$param);
        	}
		}
		$result = $q->execute();

		$errorInfo = $q->errorInfo();
		if(is_array($errorInfo) && $errorInfo[0] != "00000") {
			throw new Exception($errorInfo[2]);
		}

		if($result){
			$value = $q->fetchAll();
			return $value;
		} else {

		}
	}

	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}
}
?>