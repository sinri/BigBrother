<?php
/**
Big Brother System - The Ministry of Truth
================================================================
"War is peace", "Freedom is slavery" and "Ignorance is strength"

To get the system info for current time. 
*/
class BigBrotherTruth
{
        
        function __construct()
        {
                # code...
        }

        public static test(){
               echo "CPU Cores: ".BigBrotherTruth::check_cpu_cores().PHP_EOL;

                $keyword='php';
                $list=BigBrotherTruth::check_process($keyword);
                print_r($list); 
        }


        public static function check_cpu_cores(){
                //cat /proc/cpuinfo |grep "cores"|uniq|awk '{print $4}'
                $cores=exec("cat /proc/cpuinfo |grep cores|uniq|awk '{print $4}'");
                return intval($cores);
        }

        public static function check_process($keyword){
                $last_line=exec("ps aux|grep ".escapeshellarg($keyword),$output,$ret);
                // print_r($output);
                $list=array();
                foreach($output as $line){
                        // echo $line . PHP_EOL;
                        // root      6583  0.0  0.0  10100   876 pts/0    S+   22:50   0:00 grep php
                        preg_match_all('/^(\S+)\s+(\d+)\s+([0-9\.]+)\s+([0-9\.]+)\s+(\d+)\s+(\d+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\d+:\d+[\.\d+]*)\s+(.+)\s*$/',$line,$items);

                        //USER       PID %CPU %MEM    VSZ   RSS TTY      STAT START   TIME COMMAND
                        // print_r($items);echo PHP_EOL;
                        $item=$items;
                        $p=array(
                                'user'=>$item[1][0],
                                'pid'=>$item[2][0],
                                'cpu'=>$item[3][0],
                                'mem'=>$item[4][0],
                                'vsz'=>$item[5][0],
                                'rss'=>$item[6][0],
                                'tty'=>$item[7][0],
                                'stat'=>$item[8][0],
                                'start'=>$item[9][0],
                                'time'=>$item[10][0],
                                'command'=>$item[11][0],
                        );
                        $list[$p['pid']]=$p;
                }
                return $list;
        }

}
/*
CREATE TABLE `server_process_cache` (
        `rec_id` int(11) NOT NULL AUTO_INCREMENT,
        `server_name` varchar(64) NOT NULL,
        `server_ip` varchar(16) NOT NULL,
        `ping_time` datetime NOT NULL,
        `user` varchar(32) NOT NULL,
        `pid` varchar(32) NOT NULL,
        `cpu` float NOT NULL,
        `mem` float NOT NULL,
        `vsz` int(11) NOT NULL,
        `rss` int(11) NOT NULL,
        `tty` varchar(32) NOT NULL,
        `stat` varchar(32) NOT NULL,
        `start` varchar(32) NOT NULL,
        `time` varchar(32) NOT NULL,
        `command` varchar(256) NOT NULL,
        PRIMARY KEY (`rec_id`),
        KEY `server_ping_index`(`server_name`,`ping_time`),
        KEY `server_name_index`(`server_name`)
) ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
*/
