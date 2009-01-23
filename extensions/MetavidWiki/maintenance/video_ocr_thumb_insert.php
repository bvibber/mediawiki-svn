<?php
/*
 * video_ocr_thumb_insert.php  Created on January, 2009
 * based on ogg_thumb_insert
 * All Metavid Wiki code is Released under the GPL2
 * for more info visit http://metavid.org/wiki/Code
 *
 * @author Michael Dale, aphid
 * @email dale@ucsc.edu, aphid@ucsc.edu
 * @url http://metavid.org
 */

$cur_path = $IP = dirname( __FILE__ );
// include commandLine.inc from the mediaWiki maintance dir:
require_once ( '../../../maintenance/commandLine.inc' );
require_once ( 'metavid2mvWiki.inc.php' );

// include util functions:
require_once( 'maintenance_util.inc.php' );

if ( count( $args ) == 0 || isset ( $options['help'] ) ) {
	print'
USAGE
 php ogg_thumb_insert.php stream_name filename interval

EXAMPLE we get a frame every 5 seconds from input file stream.mpeg: 
 video2image2mvwiki.php stream_name stream.ogg 5

DURATION is scraped from ffmpeg

Notes:
  if possible you want to use the source footage rather than the ogg to generate the thumbnails (ie the mpeg2 or dv)
';
exit();

}


//maybe we derive stream name from filename? one less thing to think about.
$stream_name = $args[0];
$filename = $args[1];
$interval = $args[2];


$MV_Stream = MV_Stream::newStreamByName( $stream_name );
$stream_id = $MV_Stream->getStreamId();

$filedir = '../stream_images/' . MV_StreamImage::getRelativeImagePath( $stream_id );
$workingdir = '/metavid/raw_mpeg';
$duration = getDuration($filename);

$ocrfile = "";


//gets duration from ffmpeg

$dbw = $dbr = wfGetDB( DB_MASTER );
for ( $i = 0; $i < $duration; $i += $interval ) {
  shell_exec( "ffmpeg -ss $i -i {$filename} -vcodec mjpeg -vframes 1 -an -f rawvideo -y {$filedir}/{$i}.jpg 2>&1" );
  if(is_file("{$filedir}/{$i}.jpg")){
  	//$dbw->query( "INSERT INTO `mv_stream_images` (`stream_id`, `time`) VALUES ($stream_id, $i)" );
  	shell_exec("convert $filedir/$i.jpg -crop 457x30+63+358  $workingdir/temp.ocr.tif && convert $workingdir/temp.ocr.tif -resize 300% -level 10%,1,20% -monochrome +compress $workingdir/temp.ocr.tif");
    shell_exec("tesseract $workingdir/temp.ocr.tif $workingdir/ocrtemp nobatch lettersonly 2>&1");
    $ocr = shell_exec("tail $workingdir/ocrtemp.txt") ." at " .sec2hms($i) ." \n";
    echo $ocr;
    $ocrfile .= $ocr;	
  }else{
  	print "failed to create file: {$filedir}/{$i}.jpg \n";  
  }
}

$ocrfileloc = "$workingdir/$stream_name.ocr";
$fh = fopen($ocrfileloc, 'w') or die ("can't write ocr file");
fwrite($fh, $ocrfile);
fclose($fh);

function getDuration($filename)
{
    $string = shell_exec( "ffmpeg -i $filename 2>&1");
    $pattern = "/Duration: ([0-9])([0-9]):([0-9])([0-9]):([0-9])([0-9])/";
    preg_match($pattern, $string, $reg_array);
    $result = $reg_array[0];
    $hms = explode(" ", $result);
    $durationhms = $hms[1];
    echo "duration is $durationhms \n";
    $durarray = explode(":", $durationhms);
    return ($durarray[0]* 3600) + ($durarray[1]* 60) + $durarray[2];
}

function sec2hms ($sec, $padHours = false) {

    $hms = "";
    
    // there are 3600 seconds in an hour, so if we
    // divide total seconds by 3600 and throw away
    // the remainder, we've got the number of hours
    $hours = intval(intval($sec) / 3600); 

    // add to $hms, with a leading 0 if asked for
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
          : $hours. ':';
     
    // dividing the total seconds by 60 will give us
    // the number of minutes, but we're interested in 
    // minutes past the hour: to get that, we need to 
    // divide by 60 again and keep the remainder
    $minutes = intval(($sec / 60) % 60); 

    // then add to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

    // seconds are simple - just divide the total
    // seconds by 60 and keep the remainder
    $seconds = intval($sec % 60); 

    // add to $hms, again with a leading 0 if needed
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    return $hms;
}
