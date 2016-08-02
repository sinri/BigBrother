<?php
/**
Big Brother System - The Ministry of Love
================================================================
"War is peace", "Freedom is slavery" and "Ignorance is strength"

Telescreen, monitoring your clients
*/
date_default_timezone_set("Asia/Shanghai"); 

require_once('SinriPDO.php');

require_once('bb_love.php');
require_once('bb_peace.php');
require_once('bb_plenty.php');
require_once('bb_truth.php');

if(php_sapi_name()=='cli'){
	BigBrotherPeace::log("__________",'debug');
	$info=BigBrotherTruth::getCurrentClientStatus();
	BigBrotherPeace::sendToServer($info);
	BigBrotherPeace::log("``````````",'debug');
}else{
	BigBrotherPeace::log('From server: '.json_encode($_SERVER),'debug');
	BigBrotherPeace::log('Posted: '.json_encode($_POST),'debug');

	/*
	{
		"cpu_cores":0,
		"process_list":{
			"12368":{
				"user":"Sinri",
				"pid":"12368",
				"cpu":"1.3",
				"mem":"0.1",
				"vsz":"2480044",
				"rss":"8880",
				"tty":"s002",
				"stat":"S+",
				"start":"7:34PM",
				"time":"0:00.03",
				"command":"php telescreen.php"
			},
			...
		},
		"timestamp":"2016-08-02 19:34:09",
		"client_name":"sinrimac",
		"ver":"1.0"
	}
	*/

	$data=array();
	$data['client_ip']=$_SERVER['REMOTE_ADDR'];
	$data['client_name']=$_POST['client_name'];
	$data['cpu_cores']=$_POST['cpu_cores'];
	$data['process_list']=$_POST['process_list'];
	$data['timestamp']=$_POST['timestamp'];
	$data['ver']=$_POST['ver'];

	BigBrotherPlenty::processClientInfo($data);

	echo json_encode(array('result'=>'OK'));
	exit();
}
