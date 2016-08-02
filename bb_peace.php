<?php
/**
Big Brother System - The Ministry of Peace
================================================================
"War is peace", "Freedom is slavery" and "Ignorance is strength"

Send current system info to target server. 
*/
class BigBrotherPeace
{
        
    function __construct()
    {
            # code...
    }

    public static function getConfig($bb_config_file=null){
    	static $config=null;

    	if($config){
    		return $config;
    	}

    	if(empty($bb_config_file)){
    		$bb_config_file='bb_config.php';
    	}
    	// $content=file_get_contents($bb_config_file);

    	$config=array(
    		'server_api'=>'',
    		'client_name'=>'',
    		'client_keyword'=>'php',
    		'log_dir'=>'BigBrother',
    	);

		$handle = @fopen($bb_config_file, "r");
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				if(!empty($buffer) && $buffer[0]!='#'){
					$eq_index=strpos($buffer, '=');
					if($eq_index){
						$key=substr($buffer, 0, $eq_index);
						$content=substr($buffer, $eq_index+1);
						$key=trim($key);
						$content=trim($content);
						$config[$key]=$content;
					}
				}
			}
			if (!feof($handle)) {
				// die("Error: unexpected fgets() fail".PHP_EOL);
			}
			@fclose($handle);
		}

		return $config;
    }

    public static function getConfigOfServerApi($bb_config_file=null){
    	$config=BigBrotherPeace::getConfig($bb_config_file);
    	return $config['server_api'];
    }

    public static function sendToServer($data,$ver='1.0'){
    	if($ver=='1.0'){
    		$data['ver']=$ver;
    		$api=BigBrotherPeace::getConfigOfServerApi();
    		$result=BigBrotherPeace::curl_post($api,$data);
    		BigBrotherPeace::log("Ready to send ".json_encode($data)." to ".$api,'debug');
    		$obj=json_decode($result,true);
    		if($obj && isset($obj['result']) && $obj['result']=='OK'){
    			BigBrotherPeace::log("Responsed as ".$result." and OK",'debug');
    			return true;
    		}else{
    			BigBrotherPeace::log("Failed to send info to server, response as: ".$result,'debug');
    			return false;
    		}
    	}else{
    		//Do not know what to do
    		BigBrotherPeace::log("Unknown version: ".$ver,'debug');
    	}
    }

    private static function curl_post($url,$POST_DATA=array()){
		$curl=curl_init($url);
		curl_setopt($curl,CURLOPT_POST, TRUE);
		// ↓はmultipartリクエストを許可していないサーバの場合はダメっぽいです
		// @DrunkenDad_KOBAさん、Thanks
		//curl_setopt($curl,CURLOPT_POSTFIELDS, $POST_DATA);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($POST_DATA));
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, FALSE);  // オレオレ証明書対策
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, FALSE);  // 
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, TRUE);
		// curl_setopt($curl,CURLOPT_COOKIEJAR,      'cookie');
		// curl_setopt($curl,CURLOPT_COOKIEFILE,     'tmp');
		curl_setopt($curl,CURLOPT_FOLLOWLOCATION, TRUE); // Locationヘッダを追跡
		//curl_setopt($curl,CURLOPT_REFERER,        "REFERER");
		//curl_setopt($curl,CURLOPT_USERAGENT,      "USER_AGENT"); 

		$output= curl_exec($curl);
		return $output;
    }

    public static function log($content,$level='debug'){
    	$config=BigBrotherPeace::getConfig();
    	$log_dir=$config['log_dir'];
    	$log_file=$log_dir.'/bb_'.date('Ymd').'.log';

    	$fp = fopen($log_file, 'a');
		fwrite($fp, "[".date('Y-m-d H:i:s')."|".$level."]".$content.PHP_EOL);
		fclose($fp);
    }
}