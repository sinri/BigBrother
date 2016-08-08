<?php
/**
Big Brother System - The Ministry of Plenty
================================================================
"War is peace", "Freedom is slavery" and "Ignorance is strength"

Receive servers' info and record it into database
*/
class BigBrotherPlenty
{
        
    function __construct()
    {
        # code...
    }

    public static function processClientInfo($data){
        foreach ($data['process_list'] as $pid => $proc) {
            if($data['cpu_cores']>0){
                $proc['cpu']=$proc['cpu']/$data['cpu_cores'];
            }
            $sql="INSERT INTO server_process_cache (
                `rec_id`,
                `server_name`,
                `server_ip`,
                `ping_time`,
                `user`,
                `pid`,
                `cpu`,
                `mem`,
                `vsz`,
                `rss`,
                `tty`,
                `stat`,
                `start`,
                `time`,
                `command`
            )VALUES(
                NULL,
                '{$data['client_name']}',
                '{$data['client_ip']}',
                '{$data['timestamp']}',
                '{$proc['user']}',
                '{$proc['pid']}',
                '{$proc['cpu']}',
                '{$proc['mem']}',
                '{$proc['vsz']}',
                '{$proc['rss']}',
                '{$proc['tty']}',
                '{$proc['stat']}',
                '{$proc['start']}',
                '{$proc['time']}',
                '{$proc['command']}'
            )
            ";
            $done=BigBrotherPlenty::getDB()->insert($sql);
            BigBrotherPeace::log('Inserted as '.$done.' SQL: '.$sql,'debug');
        }
    }

    public static function killOldDaysRecords($daysBefore=3){
        $daysBefore=intval($daysBefore);
        if($daysBefore<3)throw new Exception("Too short", 1);
        $sql="DELETE from `server_process_cache` where `ping_time` < date_sub(now(),INTERVAL {$daysBefore} day)";
        $done=BigBrotherPlenty::getDB()->exec($sql);
        return $done;
    }

    public static function getDB(){
        static $db=null;
        if($db){
            return $db;
        }

        $config=BigBrotherPeace::getConfig();

        $params=array();
        $params['host']=$config['db_host'];
        $params['port']=$config['db_port'];
        $params['username']=$config['db_username'];
        $params['password']=$config['db_password'];
        $params['database']=$config['db_scheme'];

        $db=new SinriPDO($params);

        return $db;
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