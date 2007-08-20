<?php
require('recode-config.php');

if(! in_array($_SERVER['REMOTE_ADDR'], $acceptIPs) && false)
	spewError("The request originated from an unauthorized host.");

if(isset($_POST['stop']))
{
	//ensure the current job is the one this request intended to stop
	if(strlen($_POST['stop']))
	{
		cancel($_POST['stop']);
	} else {
		spewError("Stop job request is missing the targeted job name.");
	}
}

if(isset($_POST['recode']))
{
	startRecode($_POST['recode']);
}

function spewError($msg = "Unspecified error")
{
	header("Content-type: text/plain");
	echo "error\n";
	echo "$msg";
	exit(1);
}

function spewSuccess($msg = "")
{
	header("Content-type: text/plain");
	echo "success\n";
	echo $msg;
	exit(0);
}

function startRecode()
{
	$result = message("recode");
	if($result && $result['status'] == RECODE_DAEMON_STATUS_OK
		&& $result['response'])
	{
		spewSuccess($result['response']);
	} else {
		spewError($result['comment']);
	}
}

function cancel($name)
{
	$message = "cancel $name";
	$result = message($message);
	if($result['status'] == RECODE_DAEMON_STATUS_ERR)
	{
		spewError("Unable to stop job \"$name\". " . $result['comment']);
	} else {
		if(strcmp($result['response'], $message) === 0)
		{
			spewSuccess("Job \"$name\" has been stopped.");
		} else {
			spewError("Unable to stop job \"$name\". " . $result['response']);
		}
	}
}

/**
* Sends a message to the daemon and gets its response.
* @param string $msg What will be sent to the daemon.
* @return array Associative array with at least the index 'status',
*				set to one of the RECODE_DAEMON_STATUS constants.
*/
function message($msg)
{
	static $daemonPid = false;
	static $outfh;
	static $infh;

	if(!$daemonPid)
	{
		//wait for exclusive access to the daemon
		$lfh = fopen(RECODE_NOTIFY_LOCKFILE, 'a');
		$reps = 0;
		do
		{
			$wait = flock($lfh, LOCK_EX + LOCK_NB);
			$wait = true;
			$reps++;
			sleep(1);
		} while(!$wait && $reps < 4);
		if(!$wait)
		{
			spewError("Timeout waiting for exclusive access to the daemon");
		}

		//examine the daemon's own lockfile to ensure one is running
		$dlfh = fopen(RECODE_DAEMON_LOCKFILE, 'a');
		if(!$dlfh)
		{
			spewError("Cannot access daemon's lockfile. Check file permissions.");
		}
		if(flock($dlfh, LOCK_EX + LOCK_NB))
		{
			//no daemon already running!
			flock($dlfh, LOCK_UN);
			echo 'no daemon already! ';
			//ensure that the communication pipes exist
			if(! verifyFifo(RECODE_DAEMON_OUTPIPE))
			{
				return array('status' => RECODE_DAEMON_STATUS_ERR, 'comment' => "Daemon communication error: Couldn't establish outut pipe.");
			}
			if(! verifyFifo(RECODE_DAEMON_INPIPE))
			{
				return array('status' => RECODE_DAEMON_STATUS_ERR, 'comment' => "Daemon communication error: Couldn't establish input pipe.");
			}

			//wait for the daemon to come up on the output fifo

			$retval = null;
			echo 'starting daemon ';
			$retval = popen('php ./recoded.php interactive', 'r');
			echo 'daemon is independent ';
			if(! is_resource($retval))
			{
				spewError("Couldn't start recoded! Popen failed, check your PHP CLI installation?");
			} else {
				echo 'notify opening outpipe for reading';
				$outfh = fopen(RECODE_DAEMON_OUTPIPE, 'r');
				echo ' notify opening inpipe for writing';
				$infh = fopen(RECODE_DAEMON_INPIPE, 'w');
				echo ' pipes open';
				$junk = array();
				$read = array($outfh);
				$count = stream_select($read, $junk, $junk, 12);
				if(!$count)
				{
					return array('status' => RECODE_DAEMON_STATUS_ERR, 'comment' => "Timeout waiting for daemon startup.");
				} else {
					$data = null;
					$startupMsg = message_readresponse($outfh, $data);
					if(!$startupMsg || !$data)
					{
						return array('status' => RECODE_DAEMON_STATUS_ERR, 'comment' => "Bad message or failed read during daemon startup.");
					} else {
						$m = array();
						if(! preg_match("/pid=(\d*)/", $data, $m))
						{
							return array('status' => RECODE_DAEMON_STATUS_ERR, 'comment' => "Daemon sent unexpected message at startup.");
						}

						$daemonPid = $m[1];
					}
				}
			}
			
		} else {
			$daemonPid = intval(file_get_contents(RECODE_DAEMON_LOCKFILE));
		}

		if(!$daemonPid)
		{
			return array('status' => RECODE_DAEMON_STATUS_ERR, 'comment' => "Couldn't find daemon's pid.");
		} else {
			//connect pipes, our end
			if(!$outfh)
			{
				//signal daemon to connect to pipes on its end
				posix_kill($daemonPid, SIGUSR1) or die('cant signal :(');
				//shell_exec("/bin/kill -SIGUSR1 " . $daemonPid);
				$outfh = fopen(RECODE_DAEMON_OUTPIPE, 'r');
			}
			if(!$infh)
			{
				$infh = fopen(RECODE_DAEMON_INPIPE, 'w');
			}
		}
	}

	message_send($infh, $msg);
	$data = null;
	if(message_readresponse($outfh, $data))
	{
		return array('status' => RECODE_DAEMON_STATUS_OK, 'response' => $data);
	} else {
		return array('status' => RECODE_DAEMON_STATUS_ERR, 'comment' => 'Daemon communication fault. ' . $data);
	}
}

if(!count($_POST))
{
?>
<html>
<body>
<form method="post">
stop: <input type="text" name="stop">
<input type="submit">
</form>
<form method="post">
check queue: <input name="recode" type="submit">
</form>
</body>
</html>
<?php
}