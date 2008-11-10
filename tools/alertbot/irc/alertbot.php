<?PHP 
if(isset($_SERVER['REMOTE_ADDR']))
	die ("Thou shalt not try to run this script via http!");
/*
    alertbot - some stupid irc bots whose only purpose it is to put alerts on irc
    Copyright (C) 2008 Marco "harddisk" Schuster

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
	
	Manual:
	!kill MSGID		nuke spam messages
	!ack MSGID		tell the public that you take care of a problem.
					NEVER FORGET THIS OR YOUR COLLEAGUES WILL BE AWAKENED!	
	!ban MSGID		ban the sender of MSGID and kill the message
	!url			tell the report URL
*/
	include_once('Net/SmartIRC.php');
	include('Net/SMTP.php');

//configuration
	include("config.inc.php");
		
	class alertbot
	{
		function check_msgs(&$irc) { //check for new reports
			global $warnings;global $admins;global $channel;
			if(!file_exists("/tmp/ab_msg.txt")) return;
			$file=file("/tmp/ab_msg.txt");
			unlink("/tmp/ab_msg.txt");
			if(sizeof($file)>0) {
				for($i=0;$i<sizeof($file);$i++) {
					$msg=explode(chr(250),trim($file[$i]));
					$user=$msg[1];$msg=$msg[0];
					if(strlen($msg)<=5) break;
					$wid=sizeof($warnings)+1;
					$warnings[$wid]["text"]=$msg;
					$warnings[$wid]["time"]=time();
					$warnings[$wid]["id"]=$wid;
					$warnings[$wid]["action"]="PENDING";
					$warnings[$wid]["user"]=$user;
					$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,"$admins: $msg (id $wid)");
					echo "dumped $msg\n";
				}
			}
			$fp=fopen("/tmp/ab_dump.txt","w");fwrite($fp,serialize($warnings));fclose($fp);
		}
		
		function check_rep(&$irc) { //check for warnings whose warning time expired, and send SMS
			global $warnings;global $channel;global $ttw;global $lastsms;
			if(sizeof($warnings)<1) return; // less than one warning in the queue? don't even bother to scan it
			foreach($warnings as $warning) {
				if(time()-$warning["time"]>=$ttw && $warning["alerted"]!=true && time()-$lastsms>=$smsinterval) { //warning is older than TTL, and didn't get SMSed before, and there was no SMS for 10 minutes
					$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,"warning id ".$warning["id"].": alerting admins via SMS");
//					alert($warning["text"]); //send SMS
					$warnings[$warning["id"]]["alerted"]=true; //mark as "sms sent"
					$warnings[$warning["id"]]["action"]="SMS SENT";
					$fp=fopen("/tmp/ab_dump.txt","w");fwrite($fp,serialize($warnings));fclose($fp);
				}
			}
		}
		
		function killmsg(&$irc,&$ircdata) { //cancel a warning from sms sending (ONLY for spam!)
			global $warnings;global $channel;
			if(isset($warnings[$ircdata->messageex[1]])) {
				unset($warnings[$ircdata->messageex[1]]);
				$fp=fopen("/tmp/ab_dump.txt","w");fwrite($fp,serialize($warnings));fclose($fp);
				$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,"Killed ".$ircdata->messageex[1]);
			}
		}
		
		function ban(&$irc,&$ircdata) { //ban sender of MSGID & kill the message
			global $warnings;global $channel;
			if(isset($warnings[$ircdata->messageex[1]])) {
				$fp=fopen("/tmp/ab_bans.txt","a");fwrite($fp,$warnings[$ircdata->messageex[1]]["user"]."\r\n");fclose($fp);
				unset($warnings[$ircdata->messageex[1]]);
				$fp=fopen("/tmp/ab_dump.txt","w");fwrite($fp,serialize($warnings));fclose($fp);
				$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,"Banned author of and killed message ".$ircdata->messageex[1]);
			}
		}
		
		function url(&$irc,&$ircdata) { //tell the report URL
			global $channel;global $url;
			$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,$url);
		}
		
		function ackmsg(&$irc,&$ircdata) { //acknowledge a warning (this is regular)
			global $warnings;global $channel;
			if(isset($warnings[$ircdata->messageex[1]])) {
				$warnings[$ircdata->messageex[1]]["alerted"]=true; //so that no sms gets sent
				$warnings[$ircdata->messageex[1]]["action"]="ACKd";
				$fp=fopen("/tmp/ab_dump.txt","w");fwrite($fp,serialize($warnings));fclose($fp);
				$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,"Killed ".$ircdata->messageex[1]);
			}
		}
		function help(&$irc,&$ircdata) { //post help
			$irc->message(SMARTIRC_TYPE_USER, $ircdata->nick,"<!ack ID - acknowledge that you read warning>, <!url - tell the report URL>");
			$irc->message(SMARTIRC_TYPE_USER, $ircdata->nick,"<!kill ID - kill a spam msg>, <!ban ID - kills msg and IP-bans author>");
		}
	}
	function alert($text) { //helper function which sends the SMSes
		global $cellphones; global $irc;global $bulksms_from;global $bulksms_auth;
		$smtp = new Net_SMTP("bulksms.co.uk");
		$smtp->setDebug(true);
		if (PEAR::isError($e = $smtp->connect())) {
			$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,"mail send failed: ".$e->getMessage());
		}
		if (PEAR::isError($smtp->mailFrom($bulksms_from))) {
			$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,"mail send failed: unable to set from address");
		}
		foreach ($cellphones as $to) {
			if (PEAR::isError($res = $smtp->rcptTo($to."@bulksms.co.uk"))) {
				$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,"mail send failed: ".$res->getMessage());
			}
		}
		if (PEAR::isError($smtp->data("Subject: $bulksms_auth \r\n$text"))) {
			$irc->message(SMARTIRC_TYPE_CHANNEL, $channel,"mail send failed");
		}
		$smtp->disconnect();
	}

	//this stuff is independent of connect and so has to be executed only once
	$irc = &new Net_SmartIRC(); //init new IRC connection
	$irc->setDebug(SMARTIRC_DEBUG_NONE);
	$irc->setUseSockets(TRUE);
	$irc->setChannelSyncing(FALSE); 
	$irc->setCtcpVersion("alert.bot wikimedia edition by harddisk_wp");
	
	$alertbot_o=&new alertbot(); //initialize SmartIRC handlers
	$irc->registerTimehandler(1000, $alertbot_o, 'check_msgs');
	$irc->registerTimehandler(1000, $alertbot_o, 'check_rep');
	$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL,"^!kill",$alertbot_o,"killmsg");
	$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL,"^!ack",$alertbot_o,"ackmsg");
	$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL,"^!ban",$alertbot_o,"ban");
	$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL,"^!url",$alertbot_o,"url");
	
	while(true) { //reconnect after disconnect
		echo time().": connecting to IRC\n";
		$irc->connect("chat.freenode.net",6667);
		$irc->login($nick, 'One to ... alert?! ... you all', 8, $nick,$password); 
		$irc->join(array($channel));
		$irc->listen();
		$irc->disconnect();		//if we are klined or the connection gets lost, close the socket here
		echo time().": disconnected from IRC\n";
	}
?>

