<?php

require_once 'dataCenter.php';
require_once 'methods.php';

$xmldata = file_get_contents('php://input');
$data = (array)simplexml_load_string($xmldata);
header("Content-Type:text/xml;charset=UTF-8");

class Cloud{
	private $code;
	private $page;
	private $number;
	private $DBdata;
	private $sql;
	public $xml;
	
	function __construct($data){
		$meth = new Methods();
		$this->code = $meth->init_value_quote('code', $data);
		$this->page = $meth->init_value('page', $data);
		$this->number = $meth->init_value('number', $data);
		if ('' == $this->number) {
			$this->number = 10;
		}
	}
	
	private function check_value() {
		if ('' != $this->code) {
			if ('' != $this->page) {
				return true;
			}
		}
		return false;
	}
	
	private function get_data() {
		$lowest = ($this->page-1)*$this->number;
		$highest = $this->page*$this->number;
		$sql = "SELECT filename,contentLength,remark FROM users_data WHERE `id`=$this->code LIMIT $lowest,$highest";
		$mysqli = new mysqli(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD, dataCenter::HOST_DB);
		if (!$mysqli){
			printf("can not connect to MySQL. error code: %sn", mysqli_connect_error());
			exit;
		}
		$this->DBdata = $mysqli->query($sql);
		$mysqli->close();
	}
	
	private function create_xml() {
		$i = 1;
		$this->xml = "<?xml version='1.0' encoding='UTF-8'?><root>";
		while($row = $this->DBdata->fetch_array()){
			$this->xml .= "<data num=\"".$i."\">";
			$this->xml .= "<filename>".$row['filename']."</filename>";
			$this->xml .= "<length>".$row['contentLength']."</length>";
			$this->xml .= "<remark>".$row['remark']."</remark>";
			$this->xml .= "</data>";
			$i++;
		}
		$this->xml .= "</root>";
	}

	public function work() {
		if ($this->check_value()) {
			$this->get_data();
		}
		$this->create_xml();
	}
}

function main($data) {
	$cloud = new Cloud($data);
	$cloud->work();
	echo $cloud->xml;
}

main($data);
