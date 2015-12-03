<?php

require_once 'dataCenter.php';
require_once 'methods.php';

$xmldata = file_get_contents('php://input');
$data = (array)simplexml_load_string($xmldata);
header("Content-Type:text/xml;charset=UTF-8");

class Delete{
	private $code;
	private $filename;
	private $result;
	public $xml;
	
	function __construct($data) {
		$meth = new Methods();
		$this->code = $meth->init_value('code', $data);
		$this->filename = $meth->init_value_quote('filename', $data);
	}
	
	private function check_value() {
		if ('' != $this->code) {
			if ('' != $this->filename) {
				return true;
			}
		}
		$this->result = "Miss some parameters.";
		return false;
	}
	
	private function delete_data() {
		$conn = mysql_connect(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD) or die(mysql_error());
		mysql_select_db(dataCenter::HOST_DB, $conn);
		mysql_query("set names utf8");
		$sql = "DELETE from users_data where id=$this->code AND filename=$this->filename";
		mysql_query($sql);
		mysql_close($conn);
	}
	
	private function judge() {
		$conn = mysql_connect(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD);
		mysql_select_db(dataCenter::HOST_DB, $conn);
		mysql_query("set names utf8");
		$sql = "SELECT filename FROM users_data WHERE id=$this->code";
		$res = mysql_query($sql);
		mysql_close($conn);
		while($row = mysql_fetch_array($sql)) {
			$data = $row['filename'];
		}
		if ($data) {
			$this->result = "Faild!";//delete is faild
		} else {
			$this->result = "Success!";//delete is successed
		}
	}
	
	private function create_xml() {
		$this->xml = "<?xml version='1.0' encoding='UTF-8'?><root>";
		$this->xml .= "<result>$this->result</result>";
		$this->xml .= "</root>";
	}
	
	public function work() {
		if ($this->check_value()) {
			$this->delete_data();
			$this->judge();
		}
		$this->create_xml();
	}
}

function main($data) {
	$delete = new Delete($data);
	$delete->work();
	echo $delete->xml;
}

main($data);