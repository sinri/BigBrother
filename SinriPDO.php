<?php
// defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SinriPDO 
 * For CI Copyright 2016 Sinri Edogawa
 * Under MIT License
 **/
class SinriPDO {

	// protected $CI;

	private $pdo=null;

    // We'll use a constructor, as you can't directly call a function
    // from a property definition.
    public function __construct($params)
    {
        // Assign the CodeIgniter super-object
        // $this->CI =& get_instance();

        // echo json_encode($params);

		$host=$params['host'];
		$port=$params['port'];
		$username=$params['username'];
		$password=$params['password'];
		$database=$params['database'];

		try {
			$this->pdo = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$database.';charset=utf8',$username,$password,
				array(PDO::ATTR_EMULATE_PREPARES => false)
			);
			$this->pdo->query("set names utf8");
			// var_dump($this->pdo->query("SELECT 1")->fetchAll(PDO::FETCH_ASSOC));
		} catch (PDOException $e) {
			// throw new Exception("Error Processing Request", 1);
			
			exit('データベース'.' [mysql:host='.$host.';port='.$port.'] 接続失敗。'.$e->getMessage());
		}
	}

	public function getAll($sql){
		$stmt=$this->pdo->query($sql);
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	public function getCol($sql){
		$stmt=$this->pdo->query($sql);
		$rows=$stmt->fetchAll(PDO::FETCH_BOTH);
		$col=array();
		if($rows){
			foreach ($rows as $row) {
				$col[]=$row[0];
			}
		}
		return $col;
	}

	public function getRow($sql){
		$stmt=$this->pdo->query($sql);
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
		if($rows)
			return $rows[0];
		else return false;
	}

	public function getOne($sql){
		//FETCH_BOTH
		$stmt=$this->pdo->query($sql);
		$rows=$stmt->fetchAll(PDO::FETCH_BOTH);
		if($rows){
			$row = $rows[0];
			if($row){
				return $row[0];
			}else{
				return false;
			}
		}
		else return false;
	}

	public function exec($sql){
		$rows=$this->pdo->exec($sql);
		return $rows;
	}

	public function insert($sql){
		$rows=$this->pdo->exec($sql);
		if($rows){
			return $this->pdo->lastInsertId();
		}else{
			return false;
		}
	}

	public function beginTransaction(){
		return $this->pdo->beginTransaction();
	}
	public function commit(){
		return $this->pdo->commit();
	}
	public function rollBack(){
		return $this->pdo->rollBack();
	}
	public function inTransaction(){
		return $this->pdo->inTransaction();
	}

	public function errorCode(){
		return $this->pdo->errorCode();
	}
	public function errorInfo(){
		return $this->pdo->errorInfo();
	}

	public function quote($string,$parameter_type = PDO::PARAM_STR ){
		return $this->pdo->quote($string,$parameter_type);
	}
}
?>
