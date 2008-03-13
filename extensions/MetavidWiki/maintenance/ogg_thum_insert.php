<?php
/*
 * ogg_thum_insert.php Created on Mar 13, 2008
 *
 * All Metavid Wiki code is Released under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 */
 
 $cur_path = $IP = dirname(__FILE__);
//include commandLine.inc from the mediaWiki maintance dir: 
require_once ('../../../maintenance/commandLine.inc');
require_once ('metavid2mvWiki.inc.php');

//include util functions: 
require_once('maintenance_util.inc.php');

if (count($args) == 0 || isset ($options['help'])) { 
	print<<<EOT
USAGE

 php ogg_thumb_insert.php stream_id filename interval duration

 EXAMPLE

 ogg_thumb_insert.php 17 /var/www/localhost/htdocs/media/stream.ogg 20
EOT;
	exit ();
}
$streamid=$args[0];
$filename=$args[1];
$interval=$args[2];
$duration=$args[3];
$filedir='../stream_images/'.substr($stream_id, -1).'/'.$streamid;
$dbw =$dbr = wfGetDB(DB_MASTER); 
for($i=0;$i<$duration;$i+=$interval){
  $dbw->query("INSERT INTO `mv_stream_images` (`stream_id`, `time`) VALUES ($stream_id, $i)");
  shell_exec("ffmpeg -ss $i -i {$filename} -vcodec mjpeg -vframes 1 -an -f rawvideo -y {$filedir}/{$i}.jpg");
}

?>
