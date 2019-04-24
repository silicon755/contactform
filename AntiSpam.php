<?php

$data = array(
	0 => array("How many hours has one day?",24),
	1 => array("How many days has one week?",7),
	2 => array("How many seconds has one minute?",60),
	3 => array("What is 5 multiplied by 3?",15),
	4 => array("What is 9 minus 2?",7),
	5 => array("What is 12 plus 1?",13),
	6 => array("What is 50 divided by 10?",5)
);

class AntiSpam{

	public static function getAnswerById($id){
		global $data;
		
		return $data[$id][1];
	}	
	
	public static function getRandomQuestion(){
		global $data;
		
		$rand = rand(0,count($data)-1);
		return array($rand,$data[$rand][0]);
	}
	
}

?>