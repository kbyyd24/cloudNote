<?php

require_once 'dataCenter.php';
require 'methods.php';

$xmldata = file_get_contents('php://input');
$data = (array)simplexml_load_string($xmldata);
header("Content-Type:text/xml;charset=UTF-8");

class Signin{
	private $name;
	private $password;
	private $id;
	private $result;
	public $xml;
	
	function __construct($data) {
		$meth = new Methods();
		$this->name = $meth->init_value_quote('name', $data);
		$this->password = $meth->init_value_quote('password', $data);
	}
	
	private function check_value() {
		if ($this->name == '') {
			$this->result = "please input name";
			$this->id = 0;
			return false;
		} else if ($this->password == '') {
			$this->result = "please input password";
			$this->id = 0;
			return false;
		} else {
			return true;
		}
	}
	
	private function search_user() {
		$sql = "SELECT name FROM users_infomation WHERE `name`=$this->name";
		$mysqli = new mysqli(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD, dataCenter::HOST_DB);
		if (!$mysqli){
			printf("can not connect to MySQL. error code: %sn", mysqli_connect_error());
			exit;
		}
		if ($sql_res = $mysqli->query($sql)) {
			$row = $sql_res->fetch_row();
			if (null == $row) {
				return 1;
			} else {
				$this->result = "This account is existed!";
				$this->id = 0;
				$mysqli->close();
				return 0;
			}
		}
	}
	
	private function save_user() {
		$sql = "INSERT INTO users_infomation (name, password) values($this->name, $this->password)";
		$mysqli = new mysqli(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD, dataCenter::HOST_DB);
		if (!$mysqli){
			printf("can not connect to MySQL. error code: %sn", mysqli_connect_error());
			exit;
		}
		if ($sql_res = $mysqli->query($sql)) {
			$sql = "select id from users_infomation where name=$this->name";
			if ($sql_res = $mysqli->query($sql)) {
				$row = $sql_res->fetch_array();
				$this->id = $row['id'];
				$this->result = "Success!";
			}
		}
		$mysqli->close();
	}
	
	private function create_xml() {
		$this->xml = "<?xml version='1.0' encoding='UTF-8'?><root>";
		$this->xml .= "<result>$this->result</result>";
		$this->xml .= "<id>$this->id</id>";
		$this->xml .= "</root>";
	}
	
	public function work() {
		if ($this->check_value()) {
			$isexist = $this->search_user();
			if ($isexist == 1) {
				$this->save_user();
			}
		}
		$this->create_xml();
	}
}

function main($data) {
	$sign = new Signin($data);
	$sign->work();
	echo $sign->xml;
}
main($data);
