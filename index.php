<?php
/**
Big Brother System - The Ministry of Love
================================================================
"War is peace", "Freedom is slavery" and "Ignorance is strength"

Index, make the history
*/
date_default_timezone_set("Asia/Shanghai"); 

require_once('SinriPDO.php');

require_once('bb_love.php');
require_once('bb_peace.php');
require_once('bb_plenty.php');
require_once('bb_truth.php');

// echo date('g').' ... '.date('i').' ... ';
// echo floor(-(date('g')*60+(intval('1'.date('i'))-100)-180)/60.0*30);

$act=BigBrotherLove::getRequest('act');
if($act=='load_option_for_recent'){
	$server_name=BigBrotherLove::getRequest('server_name');
	$server_ip=BigBrotherLove::getRequest('server_ip');
	$option_json=BigBrotherLove::getInstance()->echarts_getRecentCpuMemStatus($server_name,$server_ip,date('Y-m-d H:i:s'),'recent_x_minutes',60*12);
	echo $option_json;
	exit();
}elseif($act=='load_option_for_monitor'){
	$minutes=BigBrotherLove::getRequest('minutes',60);
	$minutes=intval($minutes);
	if($minutes<10)$minutes=10;
	$option_json=BigBrotherLove::echarts_getRecentInfoOfClients($minutes);
	echo $option_json;
	exit();
}

// echo "This server is " . BigBrotherPeace::getConfig('client_name').PHP_EOL;
$client_list=BigBrotherLove::getInstance()->getRecentClients();
foreach ($client_list as $client_index => $client_info) {
	// $info_set=BigBrotherLove::getInstance()->getLastTenPing($client_info['server_name'],$client_info['server_ip']);
	// $client_list[$client_index]['info_set']=$info_set;
}

?>
<!doctype html>
<html>
    <head>
    	<title>Big Brother Is Watching You!</title>
        <meta charset="utf-8"> 
        <script src="echarts.js"></script>
        <script src="jquery-2.2.4.min.js"></script>
		<style type="text/css">
		#general_view_div {

		}
		#client_table {
			border: 1px solid gray;
			border-collapse: collapse;
		}
		#client_table>tr,th,td{
			border: 1px solid gray;
			padding: 5px;
		}
		</style>
		<script type="text/javascript">
		var monitor_chart=null;
		$(document).ready(function(){
			<?php
			if(!empty($client_list)){
				foreach ($client_list as $client_index => $client_info) {
			?>
			makeRecentChart('recent_view_of_<?php echo $client_index; ?>',{
				server_name: '<?php echo $client_info['server_name']; ?>',
				server_ip: '<?php echo $client_info['server_ip']; ?>'
			})
			<?php
				}
			}
			?>
			monitor_chart = echarts.init(document.getElementById('monitor_view'));
        	refresh_monitor_chart();
        	window.setInterval('refresh_monitor_chart()',30000);
		})

		function makeRecentChart(target_div,server_info){
			var myChart = echarts.init(document.getElementById(target_div));
        	$.ajax({
	        	url:'index.php?act=load_option_for_recent&server_name='+server_info.server_name+'&server_ip='+server_info.server_ip,
	        	dataType:'json'
	        }).done(function(option){
	        	// 使用刚指定的配置项和数据显示图表。
	        	myChart.setOption(option);
	        })
		}

		function refresh_monitor_chart(){
    		$.ajax({
	        	url:'index.php?act=load_option_for_monitor&minutes=60',
	        	dataType:'json'
	        }).done(function(option){
	        	// 使用刚指定的配置项和数据显示图表。
	        	monitor_chart.setOption(option);
	        })
    	}
		</script>
		<style type="text/css">
		#monitor_div {
			margin: auto;
			padding: 20px;
		}
		#detail_view_div {
			text-align: center;
		}
		#general_view_div {
			margin: auto;
			padding: 20px;
		}
		div.client_detail_div {
			float: left;
			width: 580px;
			height: 400px;
			margin: auto 10px;
			padding: 10px;
			border: 1px solid green;
			border-radius: 10px;
			/*box-sizing: content-box;*/
		}
		div.recent_view_div{
			width: 560px;
			height:350px;
			margin: auto;
		}
		</style>
	</head>
	<body>
		<h1>Big Brother Web Server Monitor System</h1>
		<h2>BIG BROTHER IS WATCHING YOU!</h2>
		<hr>
		<div id="monitor_div">
    		<div id="monitor_view" style="width: 98%;height:600px;"></div>
		</div>
		<div id="general_view_div">
			<p>There were <?php echo count($client_list); ?> client server(s).</p>
			<?php 
			if(!empty($client_list)){
			?>
			<table id="client_table">
				<thead>
					<tr>
						<th>Server Name</th>
						<th>IP</th>
						<th>Last Ping</th>
					</tr>
				</thead>
				<tbody>
			<?php
				foreach ($client_list as $client_info) {
			?>
					<tr>
						<td><?php echo $client_info['server_name']; ?></td>
						<td><?php echo $client_info['server_ip']; ?></td>
						<td><?php echo $client_info['last_ping_time']; ?></td>
					</tr>
			<?php
				}
			?>
				</tbody>
			</table>
			<?php
			}
			?>
		</div>
		<div id="detail_view_div">
			<h3>In last 12 hours</h3>
			<?php 
			if(!empty($client_list)){
				foreach ($client_list as $client_index => $client_info) {
			?>
			<div id="client_detail_div_<?php echo $client_index; ?>" class="client_detail_div">
				<h3><?php echo $client_info['server_name']; ?> (<?php echo $client_info['server_ip']; ?>)</h3>
				<div id="recent_view_of_<?php echo $client_index; ?>" class="recent_view_div" style=""></div>
				<!-- <pre><?php /*print_r($client_info['info_set']);*/ ?></pre> -->
			</div>
			<?php
				}
			}
			?>
			<div style="clear:both"></div>
		</div>
		<div style="margin: 20px;padding:10px;border-top:1px solid gray;text-align:center;">
			Copyright 2016 Sinri Edogawa 
			|
			BigBrother is provided free under License GPLv3.
			|
			Echarts (BSD) and jQuery (MIT) are used in generating web page.
		</div>
	</body>
</html>
