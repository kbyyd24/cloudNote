<?php

require_once 'dataCenter.php';
require_once 'methods.php';

$xmldata = file_get_contents('php://input');
$data = (array)simplexml_load_string($xmldata);
header("Content-Type:text/xml;charset=UTF-8");

class Logging{
	private $objectKey;
	private $contentLength;
	private $remark;
	private $contentType;
	private $fileName;
	private $userCode;
	private $audioTime;
	private $Time;
	private $result;
	private $res;
	public $xml;
	
	function __construct($data) {
		$meth = new Methods();
		$this->fileName = $meth->init_value_quote('filename', $data);
		$this->userCode = $meth->init_value('code', $data);
		$this->contentLength = $meth->init_value('contentLength', $data);
		$this->contentType = $meth->init_value_quote('contentType', $data);
		$this->objectKey = $meth->init_value_quote('objectKey', $data);
		$this->Time = $meth->init_value_quote('time', $data);
		$this->audioTime = $meth->init_value_quote('audioTime', $data);
		$this->remark = $meth->init_value_quote('remark', $data);
	}
	
	private function check_value() {
		if ('' != $this->fileName && is_numeric($this->userCode) && is_numeric($this->contentLength) && '' != $this->contentType && '' != $this->objectKey && '' != $this->Time) {
			return true;
		}
		$this->result = 'miss some parameters.';
		return false;
	}

	/**
	 * judge where the file probably exist, improve search efficiency
	 */
// 	private function
	
	private function search_base() {
//		$conn = mysql_connect(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD);
//		if(!$conn){
//			die(mysql_error());
//		}
//		mysql_select_db(dataCenter::HOST_DB, $conn);
//		mysql_query("set names utf8");
//		$this->sqlsearch = "SELECT contentLength FROM users_data WHERE id=$this->userCode AND objectKey=$this->objectKey";
//		$data = mysql_query($this->sqlsearch);
//		mysql_close($conn);
//		while($row = mysql_fetch_array($data)) {
//			$this->res = $row['contentLength'];
//		}
//		if ($this->res) {
//			return 1;//the same object has saved
//		} else {
//			return 0;//no the same object has saved
//		}

		$sql = "select contentLength from users_data where id=$this->userCode and objectKey-$this->objectKey";
		$mysqli = new mysqli(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD, dataCenter::HOST_DB);
		$sql_res = $mysqli->query($sql);
		$mysqli->close();
		if ($sql_res) {
			$row = $sql_res->fetch_array();
			$this->res = $row['contentLength'];
			return 1;
		} else {
			return 0;
		}
	}
	
	private function save_into_base() {
		$sql = "insert into users_data (id, objectKey, contentLength, audioTime, remark, contentType, filename, time) values ($this->userCode, $this->objectKey, $this->contentLength, $this->audioTime, $this->remark, $this->contentType, $this->fileName, $this->Time)";
		$mysqli = new mysqli(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD, dataCenter::HOST_DB);
		if (!$mysqli){
			$this->result = "can not connect to MySQL. error code: ".mysqli_connect_error();
			exit;
		}
		$sql_res = $mysqli->query($sql);
		$mysqli->close();
		return $sql_res;
	}

	private function update_base() {
		$conn = mysql_connect(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD);
		if(!$conn) {
			die(mysql_error());
		}
		mysql_select_db(dataCenter::HOST_DB, $conn);
		mysql_query("set names utf8");
		$this->sqlupdate = "UPDATE users_data SET objectKey=$this->objectKey, filename=$this->fileName, contentType=$this->contentType, contentLength=$this->contentLength, remark=$this->remark, time=$this->Time";
		mysql_query($this->sqlupdate);
		mysql_close($conn);
		return true;
	}
	
	private function create_xml() {
		$this->xml = "<?xml version='1.0' encoding='UTF-8'?><root>";
		$this->xml .= "<result>$this->result</result>";
		$this->xml .= "<res>$this->res</res>";
		$this->xml .= "</root>";
	}
	
	public function work() {
		if ($this->check_value()) {
			$issave = $this->search_base();
			if ($issave == 1) {
				if ($this->update_base()) {
					$this->result = "Update Success!";
				} else {
					$this->result = "Update Faild!";
				}
			} elseif ($issave == 0) {
				if ($this->save_into_base()) {
					$this->result = "Save Success!";
				} else {
					$this->result = "Save Faild!";
				}
			}
			$this->create_xml();
		} else {
			$this->xml = "<?xml version='1.0' encoding='UTF-8'?><root>";
			$this->xml .= "<result>$this->result</result>";
			$this->xml .= "</root>";
		}
	}
}

function main($data) {
	$log = new Logging($data);
	$log->work();
	echo $log->xml;
}

main($data);
