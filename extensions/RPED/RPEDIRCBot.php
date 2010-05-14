<?php
# RPEDIRC.php by Tisane, http://www.mediawiki.org/wiki/User:Tisane
#
# This script is free software that is available under the terms of the Creative Commons
# Attribution 3.0 license and the current version of the GNU General Public License.
#
# The purpose of this script is to get the titles of all new, deleted and restored
# pages from #en.wikipedia and add/delete them from the rped_table.

ini_set('include_path', ini_get('include_path').':..');

// Include Class
error_reporting(E_ALL);

include 'wikibot.classes.php'; /* The wikipedia classes. */
include 'RPED.config.php'; /* This file is very simple, but it contains sensitive information, we just define $user, $ircserver, $ircport, $ircchannel, $pass, $owner, and $status. */

$wpapi	= new wikipediaapi;
$wpq	= new wikipediaquery;
$wpi	= new wikipediaindex;
if ($wpapi->login($user,$pass)!='true'){
    die();
}

$readbuffer="";
$startSep="[[";
$endSep="]]";


// open a socket connection to the IRC server
$fp = fsockopen($host, $port, $erno, $errstr, 30);

// print the error if there is no connection
if (!$fp) {
    echo $errstr." (".$errno.")<br />\n";
} else {
    // write data through the socket to join the channel
    fwrite($fp, "NICK ".$nick."\r\n");
    fwrite($fp, "USER ".$ident." ".$host." bla :".$realname."\r\n");
    fwrite($fp, "JOIN :".$chan."\r\n");
    
    # Launch daemon!
    $pid = pcntl_fork(); // fork
    if ($pid < 0)
        exit;
    else if ($pid) // parent
        exit;
    else { // child
   
        $sid = posix_setsid();
       
        if ($sid < 0)
            exit;

        while (!feof($fp)) {
             
            $line =  fgets($fp, 512);
            $pingLine = explode(' ', $line);
            if(strtolower($pingLine[0]) == 'ping'){
                $response="PONG ".$pingLine[1]."\n";
                fwrite($fp, "PONG ".$response);
            }
            usleep(10);
            $startPos=strpos($line,$startSep);
            $endPos=strpos($line,$endSep);
            $subLine=substr($line,$startPos+5,$endPos-$startPos-8);
            if ($subLine=="Special:Log/delete"){
                $delstartPos=strpos($line,$startSep,$endPos);
                $delendPos=strpos($line,$endSep,$endPos+1);
                $delLine=substr($line,$delstartPos+5,$delendPos-$delstartPos-8);
                $action=substr($line,$delstartPos-9,7);
                if ($action=="deleted"){    
                    $wpapi->rpedDelete($delLine);
                } else {
                    $wpapi->rpedInsert($delLine);
                }
            }
            if ($subLine=="Special:Log/move"){
                $delstartPos=strpos($line,$startSep,$endPos);
                $delendPos=strpos($line,$endSep,$endPos+1);
                $delstartPos=strpos($line,$startSep,$delstartPos+1);
                $delendPos=strpos($line,$endSep,$delendPos+1);
                $delLine=substr($line,$delstartPos+2,$delendPos-$delstartPos-2);
                $wpapi->rpedInsert($delLine);
            }
            if (substr($line,$endPos+5,1)=="N" || substr($line,$endPos+6,1)=="N"){
                $wpapi->rpedInsert($subLine);
            }
        }
        
        fclose($fp);
    }
}