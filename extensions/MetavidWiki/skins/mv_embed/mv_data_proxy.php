<?
/*
* a simple proxy for offsite requests 
* sends back type text/plain
* 
* @@todo enhance so the payload is transmitable via javascript object payload 
* (less cross site request issues) 
*/
if(isset($_POST['url'])){
	if(function_exists('curl_init') ){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTP_VERSION, 1.0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true); //if we wanted to look at the content
		curl_setopt($ch, CURLOPT_URL, $_POST['url']);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$response = curl_exec ($ch);	
		$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch);
		//don't send javascript content type: 
		if(strpos($content_type, 'javascript')!==false)
			$content_type='text/plain';	
		header('Content-Type: ' . $content_type);
		print $response;
	}else{ 		
		//not as clean as using curl
		$out = file_get_contents($_POST['url']);
		if(substr($out, 0,5)=='<?xml' || substr($out, 0,4)=='<rss'){
			$content_type = 'text/xml';	
		}else{
			$content_type = 'text/plain';
		}
		header('Content-Type:'.$content_type);
		//print "ct:".$content_type;
		print $out;
	}
}