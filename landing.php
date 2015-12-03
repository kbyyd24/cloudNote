<?php

require_once 'dataCenter.php';
require 'methods.php';

$xmldata = file_get_contents('php://input');
$data = (array)simplexml_load_string($xmldata);
header("Content-Type:text/xml;charset=UTF-8");

class Login{
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
		if ('' != $this->name) {
			if ('' != $this->password) {
				return true;
			}
		}
		$this->result = 'miss some parameters.';
		return false;
	}
	
	private function search_user() {
		$sql = "SELECT `id` FROM `users_infomation` WHERE `NAME` = $this->name AND `PASSWORD` = $this->password";
		$mysqli = new mysqli(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD, dataCenter::HOST_DB);
		if (!$mysqli){
			printf("can not connect to MySQL. error code: %sn", mysqli_connect_error());
			exit;
		}
		$sql_res = $mysqli->query($sql);
		$mysqli->close();
		if ($sql_res) {
			$row = $sql_res->fetch_row();
			$this->id = $row[0];
			$this->result = "Success!";
		} else {
			$this->result = "user_name or password wrong!";
			$this->id = 0;
		}
	}
	
	private function create_xml() {
		$this->xml = "<?xml version='1.0' encoding='UTF-8'?><root>";
		$this->xml .= "<result>$this->result</result>";
		$this->xml .= "<id>$this->id</id>";
		$this->xml .= "</root>";
	}
	
	public function work() {
		if ($this->check_value()) {
			$this->search_user();
		}
		$this->create_xml();
	}
}

function main($data) {
	$login = new Login($data);
	$login->work();
	echo $login->xml;
}

main($data);
