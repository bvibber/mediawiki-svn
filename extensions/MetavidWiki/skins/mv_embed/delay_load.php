<?php
die('delay load is dissabled by default, should only be used for testing');

sleep(1);
if(isset($_SERVER['PATH_INFO'])){
	$file_path = dirname(__FILE__) . str_replace('delay_load.php', '', $_SERVER['PATH_INFO']);	
	if(is_file($file_path)){
		@readfile($file_path);
	}
}
?>