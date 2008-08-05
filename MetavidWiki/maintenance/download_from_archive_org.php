<?php
$cur_path = $IP = dirname(__FILE__);
//include commandLine.inc from the mediaWiki maintance dir: 
require_once ('../../../maintenance/commandLine.inc');

define('MV_DOWNLOAD_DIR', '/metavid/');
define('MV_ARCHIVE_ORG_DL', 'http://www.archive.org/download/mv_');

if (count($args) == 0 || isset ($options['help'])) {
	print "
	Downloads files from archive.org to configured directory:{$mvDownloadDir}

	options keyword: 
		all 		  //to get all
		[stream_name] //to grab a specifc stream

";
}else{
	proccess_streams($args[0]);
}

function proccess_streams($stream_name='all'){
	if($stream_name=='all'){
		$sql = "SELECT * FROM `mv_streams` LIMIT 0, 5000";
	}else{
		$sql = "SELECT * FROM `mv_streams` WHERE `name` ={$stream_name}";
	}
	$dbr = wfGetDB(DB_READ);
	$result = $dbr->query($sql);
	while($stream = $dbr->fetchObject($result) ){		
		$local_fl = MV_DOWNLOAD_DIR . $stream->name.'.flv';
		$remote_fl = MV_ARCHIVE_ORG_DL . $stream->name.'/'.$stream->name.'.flv';
		//senate_proceeding_08-01-07/senate_proceeding_08-01-07.flv
		//check local file size matches remote: 
		if(is_file($local_fl)){
			print "file $local_fl present";
			if( filesize($local_fl)!=remotefsize($remote_fl)){
				echo ' local:'. formatbytes(filesize($local_fl)). 
						' != remote:' . formatbytes(remotefsize($remote_fl));
			}else{
				echo ' sizes match: ' . formatbytes(filesize($local_fl)) .'='.
						formatbytes(remotefsize($remote_fl))."\n";
					
			}			
			//make
			continue;			
		}else{
			echo "DL it:";
			if(!download($remote_fl, $local_fl, $stream->name)){
				echo 'succesfully grabed '.$remote_fl; 
			};
		}
	}
	
}
function download ($file_source, $file_target, $sn){
  // Preparations
  $file_source = str_replace(' ', '%20', html_entity_decode($file_source)); // fix url format
  if (file_exists($file_target)) { chmod($file_target, 0777); } // add write permission
  $remote_size = remotefsize($file_source);
  // Begin transfer
  if (($rh = fopen($file_source, 'rb')) === FALSE) { return false; } // fopen() handles
  if (($wh = fopen($file_target, 'wb')) === FALSE) { return false; } // error messages.
  $i=0;
  while (!feof($rh)){
  	//report progress every 2000
  	if($i==2000){
  		$i=0;
  		$lfs = filesize($file_target);  		
  		print formatbytes($lfs) .' of '. formatbytes($remote_size)." of $sn \n";
  		clearstatcache();
  	}
  	$i++;
    // unable to write to file, possibly because the harddrive has filled up
    if (fwrite($wh, fread($rh, 1024)) === FALSE) { 
    	fclose($rh); fclose($wh); return false; 
    }
  }

  // Finished without errors
  fclose($rh);
  fclose($wh);
  return true;
}
function formatbytes($val, $digits = 4, $mode = "SI", $bB = "B"){ //$mode == "SI"|"IEC", $bB == "b"|"B"
        $si = array("", "k", "M", "G", "T", "P", "E", "Z", "Y");
        $iec = array("", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi");
        switch(strtoupper($mode)) {
            case "SI" : $factor = 1000; $symbols = $si; break;
            case "IEC" : $factor = 1024; $symbols = $iec; break;
            default : $factor = 1000; $symbols = $si; break;
        }
        switch($bB) {
            case "b" : $val *= 8; break;
            default : $bB = "B"; break;
        }
        for($i=0;$i<count($symbols)-1 && $val>=$factor;$i++)
            $val /= $factor;
        $p = strpos($val, ".");
        if($p !== false && $p > $digits) $val = round($val);
        elseif($p !== false) $val = round($val, $digits-$p);
        return round($val, $digits) . " " . $symbols[$i] . $bB;
  }
  
    function remotefsize($url) {
        //$sch = parse_url($url, PHP_URL_SCHEME);
        //if (($sch != "http") && ($sch != "https") && ($sch != "ftp") && ($sch != "ftps")) {
        //    return false;
        //}
        $sch='http';
        if (($sch == "http") || ($sch == "https")) {
            $headers = get_headers($url, 1);
            if ((!array_key_exists("Content-Length", $headers))) { return false; }
            return $headers["Content-Length"];
        }
        if (($sch == "ftp") || ($sch == "ftps")) {
            $server = parse_url($url, PHP_URL_HOST);
            $port = parse_url($url, PHP_URL_PORT);
            $path = parse_url($url, PHP_URL_PATH);
            $user = parse_url($url, PHP_URL_USER);
            $pass = parse_url($url, PHP_URL_PASS);
            if ((!$server) || (!$path)) { return false; }
            if (!$port) { $port = 21; }
            if (!$user) { $user = "anonymous"; }
            if (!$pass) { $pass = "phpos@"; }
            switch ($sch) {
                case "ftp":
                    $ftpid = ftp_connect($server, $port);
                    break;
                case "ftps":
                    $ftpid = ftp_ssl_connect($server, $port);
                    break;
            }
            if (!$ftpid) { return false; }
            $login = ftp_login($ftpid, $user, $pass);
            if (!$login) { return false; }
            $ftpsize = ftp_size($ftpid, $path);
            ftp_close($ftpid);
            if ($ftpsize == -1) { return false; }
            return $ftpsize;
        }
    }

?>