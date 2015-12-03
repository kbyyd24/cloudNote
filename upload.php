<?php
/**
 * @return request headers in xml
 */

require_once 'dataCenter.php';
require_once 'behaviour.php';
require_once 'methods.php';

$xmldata = file_get_contents('php://input');
$data=(array)simplexml_load_string($xmldata);
header("Content-Type:text/xml;charset=UTF-8");


class Upload_Headers{
	private $signature;
	private  $contentType;
	private $objectKey;
	private $time;
	private $result;
	public $xml;
	
	function __construct($data){
		$meth = new Methods();
		$this->contentType = $meth->init_value_quote('contentType', $data);
		$this->objectKey = $meth->init_value_quote('object', $data);
	}
	
	private function check_value() {
		if ('' != $this->contentType) {
			if ('' != $this->objectKey) {
				return true;
			}
		}
		$this->result = 'miss some parameters.';
		return false;
	}
	
	private function Authrization(){
		$data = behaviour::PUT."\n\n".$this->contentType."\n".$this->time."\n/".dataCenter::BUCKET."/".$this->objectKey;
		$this->signature = "OSS ".dataCenter::ACCESSID.":".base64_encode(hash_hmac("sha1", $data, dataCenter::ACCESSKEY, true));
	}
	
	private function get_time(){
		$this->time = gmdate("D, d M Y H:i:s T", time());
	}

	private function create_xml(){
		$this->xml = "<?xml version='1.0' encoding='UTF-8'?>";
		$this->xml .= "<root>";
		$this->xml .= "<authorization>".$this->signature."</authorization>";
		$this->xml .= "<contentType>".$this->contentType."</contentType>";
		$this->xml .= "<bucket>".dataCenter::BUCKET."</bucket>";
		$this->xml .= "<date>".$this->time."</date>";
		$this->xml .= "</root>";
	}

	public function work(){
		if ($this->check_value()) {
			$this->get_time();
			$this->Authrization();
			$this->create_xml();
		} else {
			$this->xml = "<?xml version='1.0' encoding='UTF-8'?><root>";
			$this->xml .= "<result>$this->result</result>";
			$this->xml .= "</root>";
		}
	}
}

function main($data){
	$headers = new Upload_Headers($data);
	$headers->work();
	echo $headers->xml;
}

main($data);
?>
