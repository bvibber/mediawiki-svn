<?php
# RPEDFileReader.php by Tisane, http://www.mediawiki.org/wiki/User:Tisane
#
# This script is free software that is available under the terms of the Creative Commons
# Attribution-ShareAlike 3.0 license and the current version of the GNU General Public License.
#
# The purpose of this script is to read a text file (specifically, the list of page titles from
# Wikipedia's data dump) and add each page title to a database table.

include 'wikibot.classes.php'; /* The wikipedia classes. */
include 'RPED.config.php'; /* This file is very simple, but it contains sensitive information, we just define $user, $ircserver, $ircport, $ircchannel, $pass, $owner, and $status. */

$wpapi	= new wikipediaapi;
$wpq	= new wikipediaquery;
$wpi	= new wikipediaindex;
if ($wpapi->login($user,$pass)!='true'){
    die();
}

$searching=false;

if (isset($argv[1]) && $argv[1]!=''){
    $searching=true;
}

$handle = @fopen("enwiki-20100312-all-titles-in-ns0", "r");
$lineNumber=0;
$line=""; 
     
if ($handle) {
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

        while (!feof($handle)) {
            $buffer = fgets($handle, 4096);
            $buffer=str_replace("\n","",$buffer);
            if ($searching==true){
                if ($buffer==$argv[1]){
                    $searching=false;
                }
            } else {
                $buffer=urlencode  (  $buffer  );
                if ($line!=""){
                    $line.="|";
                }
                if (strlen($line)+strlen($buffer)>200){
                    $wpapi->rpedInsert($line);
                    $line="";
                }
                $line.=$buffer;
            }
        }
        fclose($handle);
    }
}