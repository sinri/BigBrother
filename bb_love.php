<?php
/**
Big Brother System - The Ministry of Love
================================================================
"War is peace", "Freedom is slavery" and "Ignorance is strength"

Grant display views to show the server status.
*/
class BigBrotherLove
{
    private static $instance=null;

    function __construct()
    {
            # code...
    }

    public static function getInstance(){
    	if(!BigBrotherLove::$instance){
    		BigBrotherLove::$instance=new BigBrotherLove();
    	}
    	return BigBrotherLove::$instance;
    }

    public function getRecentClients(){
    	$sql="SELECT `server_name`,`server_ip`,max(`ping_time`) as last_ping_time
			from `server_process_cache`
            where ping_time>date_sub(now(),interval 1 day)
			GROUP BY `server_name`
		";
		$client_list=BigBrotherPlenty::getDB()->getAll($sql);
		return $client_list;
    }

    public function getLastTenPing($server_name,$server_ip){
    	$sql="SELECT DISTINCT(`ping_time`)
			FROM `server_process_cache`
			WHERE `server_name`= '{$server_name}'
			and `server_ip`= '{$server_ip}'
			order by `ping_time` desc
			limit 10
		";
		$pings=BigBrotherPlenty::getDB()->getCol($sql);
		$pings=implode("','", $pings);
		$pings="'{$pings}'";

		$sql="SELECT * FROM `server_process_cache` 
			WHERE `server_name` ='{$server_name}' and `server_ip` ='{$server_ip}' 
			and `ping_time` IN ({$pings})
		";
		$info=BigBrotherPlenty::getDB()->getAll($sql);

		$info_set=array();

        $sql="SELECT `ping_time`,sum(cpu) total_cpu,sum(mem) total_mem
            FROM `server_process_cache`
            WHERE `server_name`= 'Test'
            and `server_ip`= '10.25.5.103'
            and `ping_time` IN({$pings})
            group by `ping_time`
        ";
        $count_set=BigBrotherPlenty::getDB()->getAll($sql);
        $total_info_mapping=array();
        foreach ($count_set as $count_set_item) {
            $total_info_mapping[$count_set_item['ping_time']]=array(
                'total_cpu'=>$count_set_item['total_cpu'],
                'total_mem'=>$count_set_item['total_mem']
            );
        }

		foreach ($info as $item) {
			if(!isset($info_set[$item['ping_time']])){
				$info_set[$item['ping_time']]=array(
                    'process_list'=>array(),
                    'total_cpu'=>$total_info_mapping[$item['ping_time']]['total_cpu'],
                    'total_mem'=>$total_info_mapping[$item['ping_time']]['total_mem'],
                );
			}
			$info_set[$item['ping_time']]['process_list'][$item['rec_id']]=$item;
		}

		return $info_set;
    }

    public static function makeProcTimeToSeconds($his){
    	$hi_pos=strpos($his, ':');
    	$is_pos=strpos($his, '.');
    	$min=0;$sec=0;$ss=0;
    	if($hi_pos){
    		$min=substr($his, 0,$hi_pos);
    		if($is_pos){
    			$sec=substr($his, $hi_pos+1,$is_pos-$hi_pos-1);
    			$ss=substr($his, $is_pos+1);
    		}else{
    			$sec=substr($his, $hi_pos+1);
    		}
    	}
    	// echo $min.' / '.$sec.'/'.$ss.PHP_EOL;
    	return $min*60+$sec+$ss/1000;
    }

    public static function getRequest($name,$default=null){
        if(isset($_REQUEST[$name])){
            return $_REQUEST[$name];
        }else{
            return $default;
        }
    }

    ///// ECharts Related, Make option

    public function getRecentCpuMemStatus($server_name,$server_ip,$date=null,$type='daily',$recent_min=0){
        $server_name=BigBrotherPlenty::getDB()->quote($server_name);
        $server_ip=BigBrotherPlenty::getDB()->quote($server_ip);
        if($date==null){
            $date=' now() ';//date('Y-m-d H:i:s');
        }else{
            $date=BigBrotherPlenty::getDB()->quote($date);
        }

        if($type=='daily'){
            $sql="SELECT date_sub(`ping_time` ,INTERVAL 8 hour) ping_time,
                    sum(cpu) total_cpu,
                    sum(mem) total_mem
                FROM `server_process_cache`
                WHERE `server_name`= {$server_name}
                and `server_ip`= {$server_ip}
                and DATE(`ping_time`)= date({$date})
                group by `ping_time`
            ";
            $title="Daily";
        }elseif($type=='recent_x_minutes'){
            $recent_min=intval($recent_min);
            $sql="SELECT date_sub(`ping_time` ,INTERVAL 8 hour) ping_time,
                    sum(cpu) total_cpu,
                    sum(mem) total_mem
                FROM `server_process_cache`
                WHERE `server_name`= {$server_name}
                and `server_ip`= {$server_ip}
                and `ping_time`> date_sub({$date}, INTERVAL {$recent_min} minute)
                group by `ping_time`
            ";
            $title="Recent";
        }


        $set=BigBrotherPlenty::getDB()->getAll($sql);
        $data_cpu=array();
        $data_mem=array();
        foreach ($set as $item) {
            $data_cpu[]=array(
                $item['total_cpu'],
                //$item['mins']
                $item['ping_time']
            );
            $data_mem[]=array(
                $item['total_mem'],
                $item['ping_time']
            );
        }

        // Make option 

        $option = array(
            'title' => array(
                'text' => $title,
            ),
            'legend' => array(
                'data' => array('cpu_line','mem_line'),
                'top' => '5',
                'right' => '5',
            ),
            'polar' => (object)array(
                // 'radius'=>'50%'
            ),
            'tooltip' => array(
                'trigger' => 'axis',
                'axisPointer' => array(
                    'type' => 'cross'
                )
            ),
            'angleAxis' => array(
                'type' => 'time',
                'startAngle' => 90
            ),
            'radiusAxis' => array(
                'min' => 0,
                // 'max' => 50,
            ),
            'series' => array(
                (object)array(
                    'coordinateSystem' => 'polar',
                    'name' => 'cpu_line',
                    'type' => 'line',
                    'data' => $data_cpu,
                    'smooth'=>true,
                    'symbol' => 'circle',
                    // 'symbol_size'=>1,
                    // 'sampling'=>'average',
                ),
                (object)array(
                    'coordinateSystem' => 'polar',
                    'name' => 'mem_line',
                    'type' => 'line',
                    'data' => $data_mem,
                    'smooth'=>true,
                    'symbol' => 'circle',
                    // 'symbol_size'=>1,
                    // 'sampling'=>'average',
                )
            )
        );

        return json_encode($option);
    }
}

// echo BigBrotherLove::makeProcTimeToSeconds('2:4.1');
