<?php

require_once 'modules/ppython/php_python.php';
require_once 'methods.php';

$xmldata = file_get_contents('php://input');
$data = (array)simplexml_load_string($xmldata);
header("Content-Type:text/xml;charset=UTF-8");

class Topic {
	const FILENAME =  "modules/ppython/topic.text";
	private $topic_in;
	private $topic_out = [];
	public $xml;
	
	function __construct($data) {
		$meth = new Methods();
		$this->topic_in = $meth->init_value_quote('str', $data);
	}
	
	private function output() {
		$topic_file = fopen(self::FILENAME, 'w') or die("<?xml version='1.0' encoding='UTF-8'?><root>fault to open file by write!</root>");
		fwrite($topic_file, $this->topic_in);
		fclose($topic_file);
	}
	
	private function cut_word() {
		return ppython("topic::topic");
	}
	
	private function get_result() {
		$topic_file = fopen(self::FILENAME, 'r') or die("<?xml version='1.0' encoding='UTF-8'?><root>fault to open file by read!</root>");
		while (!feof($topic_file)) {
			$this->topic_out[] = fgets($topic_file);
		}
		fclose($topic_file);
	}
	
	private function create_xml() {
		$this->xml = "<?xml version='1.0' encoding='UTF-8'?><root>";
		foreach ($this->topic_out as $value) {
			$this->xml .= "<part>$value</part>";			
		}
		$this->xml .= "</root>";
	}
	
	public function work() {
		self::output();
		if (self::cut_word()) {
			self::get_result();
		} else {
			echo "wrong!";
		}
		$this->create_xml();
	}
}

function main($data){
	$wordCuter = new Topic($data);
	$wordCuter->work();
	echo $wordCuter->xml;
}

main($data);