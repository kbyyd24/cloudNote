<?php
require_once 'dataCenter.php';
require_once 'behaviour.php';
require_once 'methods.php';

header("Content-Type:text/xml;charset=UTF-8");
$xmldata = file_get_contents('php://input');
$data = (array)simplexml_load_string($xmldata);

class Download_Headers{
	private $filename;
	private $fileLength;
	private $userCode;
	private $signature;
	private $objectKey;
	private $time;
	private $contentLength;
	private $range;
	private $da;
	private $result;
	public $xml;
	
	function __construct($data){
		$meth = new Methods();
		$this->filename = $meth->init_value_quote('filename', $data);
		$this->fileLength = $meth->init_value('fileLength', $data);
		$this->userCode = $meth->init_value('code', $data);
	}
	
	private function check_value() {
		if ('' != $this->filename) {
			if (is_numeric($this->fileLength)) {
				if (is_numeric($this->userCode)) {
					return true;
				}
			}
		}
		$this->result = 'miss some parameters.';
		return false;
	}
	
	private function get_messages() {
		$sql = "select contentLength,objectKey from users_data where id=$this->userCode and filename=$this->filename";
		$mysqli = new mysqli(dataCenter::HOST, dataCenter::USER, dataCenter::PASSWORD, dataCenter::HOST_DB);
		if (!$mysqli){
			printf("can not connect to MySQL. error code: %sn", mysqli_connect_error());
			exit;
		}
		if ($sql_res = $mysqli->query($sql)) {
			$row = $sql_res->fetch_array();
			$this->contentLength = $row['contentLength'];
			$this->objectKey = $row['objectKey'];
		}
	}
	
	private function Authrization() {
		$this->da = behaviour::GET."\n\n\n".$this->time."\n/".dataCenter::BUCKET."/".$this->objectKey;
		$this->signature = "OSS ".dataCenter::ACCESSID.":".base64_encode(hash_hmac("sha1", $this->da, dataCenter::ACCESSKEY, true));
	}
	
	private function get_time(){
		$this->time = gmdate("D, d M Y H:i:s T", strtotime("+5 minutes"));
	}
	
	private function create_xml() {
		$this->xml = "<?xml version='1.0' encoding='UTF-8'?>";
		$this->xml .= "<root>";
		$this->xml .= "<authorization>$this->signature</authorization>";
		$this->xml .= "<bucket>".dataCenter::BUCKET."</bucket>";
		$this->xml .= "<contentLength>$this->contentLength</contentLength>";
		$this->xml .= "<date>$this->time</date>";
		$this->xml .= "<range>$this->range</range>";
		$this->xml .= "<id>$this->userCode</id>";
		$this->xml .= "<filename>$this->filename</filename>";
		$this->xml .= "<objectKey>$this->objectKey</objectKey>";
		$this->xml .= "</root>";
	}
	
	private function set_range() {
		if ($this->contentLength > $this->fileLength) {
			$this->range = $this->fileLength."-";
		}
		elseif ($this->contentLength == $this->fileLength){
			$this->range = 0;
		};
	}
	
	public function work() {
		if ($this->check_value()) {
			$this->get_messages();
			$this->get_time();
			$this->Authrization();
			$this->set_range();
			$this->create_xml();
		} else {
			$this->xml = "<?xml version='1.0' encoding='UTF-8'?><root>";
			$this->xml .= "<result>$this->result</result>";
			$this->xml .= "</root>";
		}
	}
}

function main($data) {
	$headers = new Download_Headers($data);
	$headers->work();
    echo $headers->xml;
}

main($data);
?>
