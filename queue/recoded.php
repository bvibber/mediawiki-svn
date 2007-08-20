<?php

if(!function_exists("pcntl_fork"))
{
	trigger_error("Process control functions unavailable; cannot daemonize.\n Perhaps you are trying to run the daemon using a non CLI flavor of PHP, or you did not configure php with --enable-pcntl", E_USER_ERROR);
}

//ensure working directory is same as where recoded resides
chdir(dirname(__FILE__));
require('recode-config.php');

//ensure the needed MPlayer A/V pipes are in place
if(! verifyFifo('stream.wav'))
{
	trigger_error("Cannot create or use audio fifo stream.wav. Check file perms?", E_USER_ERROR);
}

if(! verifyFifo('stream.yuv'))
{
	trigger_error("Cannot create or use video fifo stream.yuv. Check file perms?", E_USER_ERROR);
}

/*
* include the necessary features from MediaWiki...database and FileRepo, 
* Title and some GlobalFunctions.
* FileRepo requires a significant chunk of the MW environment to work
*/
define("MEDIAWIKI", true);
require(MW_INSTALL_PATH . '/includes/Defines.php');
require(MW_INSTALL_PATH .'/LocalSettings.php');
require(MW_INSTALL_PATH . '/includes/ProfilerStub.php');
require(MW_INSTALL_PATH . '/includes/AutoLoader.php');
require(MW_INSTALL_PATH . '/includes/GlobalFunctions.php');
require(MW_INSTALL_PATH . '/includes/Setup.php');

$lfh = fopen(RECODE_DAEMON_LOCKFILE, 'a');
if(!$lfh)
{
	trigger_error("Unable to open the RECODE_DAEMON_LOCKFILE \"" . RECODE_DAEMON_LOCKFILE . "\" for writing", E_USER_ERROR);
}

chmod(RECODE_DAEMON_LOCKFILE, 0666);

/* if starting interactive, first action is to wait for commands on the notify
* pipe. Otherwise, the daemon just looks for unclaimed jobs in queue.
*/
if($argv[1] == 'interactive') $interactive = true; else $interactive = false;

$pid = pcntl_fork();
if ($pid == -1) {
	 trigger_error("could not fork", E_USER_ERROR); 
} else if ($pid) {
	 exit(0); // we are the parent 
}

// else we are the child

/*
* The file must be locked here, we can't do it before we've forked.
* The lock seems to be lost if it is acquired before fork()ing, and
* a subsequent call to flock within the forked process does nothing. Php bug?
*/
if(!flock($lfh, LOCK_EX + LOCK_NB))
{
	//already locked
	trigger_error("Cannot obtain lock on " . RECODE_DAEMON_LOCKFILE . ". Is another instance of recoded already running?", E_USER_ERROR);
}

//make an initial connection to the pipes
if(! verifyFifo(RECODE_DAEMON_INPIPE))
{
	trigger_error("Named pipe " . RECODE_DAEMON_INPIPE . " could not be created", E_USER_ERROR);
}
if(! verifyFifo(RECODE_DAEMON_OUTPIPE))
{
	trigger_error("Named pipe " . RECODE_DAEMON_OUTPIPE . " could not be created", E_USER_ERROR);
}

ftruncate($lfh, 0);
fseek($lfh, 0);
fwrite($lfh, posix_getpid());
fflush($lfh);

declare(ticks=1); //enables asynch. signal handling
pcntl_signal(SIGUSR1, 'handleSignal');
pcntl_signal(SIGTERM, 'handleSignal');
pcntl_signal(SIGCHLD, 'handleChildTerm');

define("IN", 0);
define("OUT", 1);
define("ERR", 2);

$children = array(); //holds vital info for accessing child procs.

// detach from the controlling terminal
if (!posix_setsid()) {
	trigger_error("could not detach from terminal", E_USER_ERROR);
}

//fatal errors can leave messes if we don't tell children to quit
register_shutdown_function("fatalError", "PHP has seen fit to terminate the daemon");
set_error_handler("phpErrorHandler");


$readPipes = array();
$writePipes = array();
$junk = NULL; // select params 2 & 3 are by reference and thus must be variable

if($interactive)
{
	echo 'opening outpipe for writing';
	$outfh = fopen(RECODE_DAEMON_OUTPIPE, 'w');
	echo ' opening inpipe for reading';
	$infh = fopen(RECODE_DAEMON_INPIPE, 'r');
	echo ' pipes open';
	$readPipes[] = $infh;

	$interactive = false;

	message_send($outfh, 'pid=' . posix_getpid());
} else {
	$outfh = -1;
	$infh = -1;
	seekJob();
}

foreach(childProcess::$childrenByPid AS $child)
{

	$readPipes[] = $child->io[OUT][childProcess::PIPE];
	$readPipes[] = $child->io[ERR][childProcess::PIPE];
}

// MAIN LOOP
while(true)
{
	//recode_log("--------------LOOP-------------");
	$pipeCount = stream_select($readPipes, $junk, $junk, NULL);
	if(!$pipeCount)
	{
		//get here if stream_select is interrupted by an incoming signal
		$readPipes = array();
	}

	foreach($readPipes AS $read)
	{
		if(isset(childProcess::$childrenByPipe[(int)$read]))
		{
			$child = childProcess::$childrenByPipe[(int)$read];
			if((int) $read == (int) $child->io[OUT][childProcess::PIPE])
			{
				$pipeIndex = OUT;
			} else {
				$pipeIndex = ERR;
			}
			$callback = $child->io[$pipeIndex][childProcess::HANDLER];
			//recode_log("Calling " . $callback);

			$callback($child->io[$pipeIndex][childProcess::PIPE]);
		} else {
			if((int) $read == (int) $infh)
			{
				handleNotification();
			} else {
				unset(childPRocess::$childrenByPipe[(int)$read]);
			}
		}
	}

	//set up for next loop
	$readPipes = array();

	/* infh is added to the watch list here and in handleSignal to ensure it is * included regardless of when a SIGUSR1 is handled.
	*/
	if(is_resource($infh))
	{
		$readPipes[] = $infh;
	}

	foreach(childProcess::$childrenByPid AS $pid => $child)
	{
		if(!is_resource($child->io[OUT][childProcess::PIPE]))
		{
			unset(childProcess::$childrenByPid[$pid]);
			continue;
		}

		if(!feof($child->io[OUT][childProcess::PIPE]))
			$readPipes[] = $child->io[OUT][childProcess::PIPE];

		if(!feof($child->io[ERR][childProcess::PIPE]))
			$readPipes[] = $child->io[ERR][childProcess::PIPE];
	}
}

function piperead($pipe)
{
	if(!is_resource($pipe)) throw new Exception();
	static $pipeBuffers = array();
	if(@isset($pipeBuffers[(int)$pipe]))
	{
		$out = $pipeBuffers[(int)$pipe];
	} else {
		$out = '';
	}
	$maxRead = 8192;
	stream_set_blocking($pipe, 0);

	do
	{
		$read = fread($pipe, $maxRead);
		$out .= $read;
	} while(strlen($read) == $maxRead);
	$endC = substr($out, -1);
	//only return complete lines
	if($endC != "\n" || $endC != "\r")
	{
		//find last end of line, and only return up through there
		$lastLB = strrpos($out, "\n");
		if($lastLB === false)
		{
			$lastLB = strrpos($out, "\r");
		}
		if(! $lastLB)
		{
			$pipeBuffers[(int)$pipe] = $out;
			return '';
		} else {
			$pipeBuffers[(int)$pipe] = substr($out, $lastLB + 1);
			return substr($out, 0, $lastLB + 1);
		}
	} else {
		return $out;
	}
}

function handleNotification()
{
	global $infh, $outfh;
	$data = null;
	if(! message_readresponse($infh, $data))
	{
		message_send($outfh, false);
		recode_log("Failed reading (misformed?) message on notify pipe. $data");
		//handleSignal(SIGUSR1); //pipes are out of sync, have to reset them
	} else {
		recode_log("New message: " . $data);
		$msg = explode(' ', $data, 2);
		recode_log("Action is " . $msg[0]);
		$action = trim($msg[0]);
		switch($action)
		{
			case 'cancel':
				$cancelFlag = true;
				if(abortRecode(trim($msg[1])))
				{
					message_send($outfh, $data);
				} else {
					message_send($outfh, 'Job "' . trim($msg[1]) . '" is not currently processing on this node, cannot cancel it.');
				}
				break;

			case 'recode':
				message_send($outfh, $data);
				seekJob();
				break;

			default:
				message_send($outfh, false);
		}
	}
	fclose($infh);
	fclose($outfh);
	$infh = -1;
	$outfh = -1;
}

function seekJob()
{
	global $currentJob;
	if($currentJob)
	{
		return false;
	}
	$dbm = wfGetDB(DB_MASTER);
	$job = $dbm->selectField('avrecode_queue', 'q_img_name', 'q_order = (select min(q_order) from avrecode_queue)', __FUNCTION__);
	if(strlen($job))
	{
		$dbm->delete('avrecode_queue', array("q_img_name = '$job'"), __FUNCTION__);
		if($dbm->affectedRows())
		{
			$dbm->immediateCommit();
			if(! startRecode($job))
			{
				return seekJob();
			} else {
				return true;
			}
		} else {
			return seekJob();
		}
	} else {
		//empty queue
		return false;
	}
}

function startRecode($name)
{
	/*$wgLocalFileRepo = array(
		'class' => 'LocalRepo',
		'name' => '?',
		'url' => 'http://mikeb.servehttp.com:8080/wiki/phase3/images',
		'hashLevels' => 2,
		'directory' => '/data/www/wiki/phase3/images'
	);
	*/

	$title = Title::makeTitleSafe( NS_IMAGE, $name );
	$original = wfFindFile($title);
	$title = $title->getDBkey(); //not the best naming here, but oh well
	if(!is_object($original))
	{
		recode_log("Enqueued job \"$title\" does not exist!");
		return false;
	}

	$mediatype = $original->getMediaType();
	if($mediatype == MEDIATYPE_VIDEO)
	{
		global $wgVideoBitrates;
		global $wgVideoWidths;
		$bitrates = $wgVideoBitrates;
		$widths = $wgVideoWidths;
	} /* else if($mediatype == MEDIATYPE_AUDIO)
	{
		global $wgAudioBitrates;
		$bitrates = $wgAudioBitrates;
		$widths = array();
	}*/
	if(JobStrategy::buildStrategy($original, $bitrates, $widths))
	{
		global $currentJob;
		$currentJob = $title;
		$dbm = wfGetDB(DB_MASTER);
		$dbm->update('avrecode_farm', array('img_name' => $title), array('notify_address' => NOTIFY_SCRIPT_URL), __FUNCTION__);

		JobStrategy::runNextPart();
		return true;
	}
}

function abortRecode($title)
{
	global $currentJob;
	if(strcmp($title, $currentJob) === 0)
	{
		if(! sigkill_children())
			fatalError("Can't quit child processe(s), giving up.");

		$currentJob = false;
		//mark all jobs associated with this file as failed
		$dbm = wfGetDB(DB_MASTER);
		$dbm->update('avrecode', array('status' => 'FAILED'), array('img_name' => $title), __FUNCTION__);

		$dbm->update('avrecode_farm', array('img_name' => ''), array('notify_address' => NOTIFY_SCRIPT_URL), __FUNCTION__);

		return true;
	} else {
		return false;
	}
}

function sigkill_children()
{
	foreach(childProcess::$childrenByPid AS $pid => $child)
	{
		if(! posix_kill($pid, SIGKILL))
		{
			return false;
		}
	}
	return true;
}

function recode_log($entry)
{
	echo ("recoded: " . $entry . "\n");
}

function fatalError($err)
{
	static $called = false;
	//prevents fatalError from rerunning by register_shutdown_function
	if(!$called)
	{
		$called = true;
		//eventually will clean up everything
		recode_log ("FATAL: " . $err);

		global $children;
		if(is_array($children) && count($children))
		{
			recode_log ("Sending children the term signal...");
			foreach($children AS $pid => $child)
			{
				posix_kill($pid, SIGTERM);
				proc_close($child->handle);
			}
		}

		global $lfh;
		if($lfh)
		{
			fclose($lfh);
			unlink(RECODE_DAEMON_LOCKFILE);
		}
		die(1);
	}
}

function handleSignal($sig)
{
	if($sig == SIGUSR1)
	{
		//reconnect with notify i/o pipes
		global $infh, $outfh;
		@fclose($infh);
		@fclose($outfh);
		$outfh = @fopen(RECODE_DAEMON_OUTPIPE, 'w');
		$infh = @fopen(RECODE_DAEMON_INPIPE, 'r');
		global $readPipes;
		$readPipes[] = $infh;
	} else {
		fatalError("Signal received");
	}
}

function handleChildTerm($sig)
{
	$status;
	$pid = pcntl_wait($status);

	if($pid > 0)
	{
		if(isset(childProcess::$childrenByPid[$pid]))
		{
			switch(childProcess::$childrenByPid[$pid]->type)
			{
				case childProcess::TYPE_MPLYR:
				//todo: add mechanism for identifying undesired terminations
				break;

				case childProcess::TYPE_ENCODER:
					//recode_log("Encoder terminated...");
					onEncoderOutput(childProcess::$childrenByPid[$pid]->io[OUT][childProcess::PIPE]);
					onEncoderOutput(childProcess::$childrenByPid[$pid]->io[ERR][childProcess::PIPE]);
				break;

				default:
					fatalError('A child with unrecognized classification "' . $children[$pid]->name . '" has terminated. Don\'t know how to recover. The lack of a classification is a bug.');
			}

			childProcess::$childrenByPid[$pid]->destruct();

			global $cancelFlag;
			if($cancelFlag)
			{
				if(count(childProcess::$childrenByPid) === 0)
				{
					$cancelFlag = false;
					seekJob();
				}
			}
		} else {
			$how = (pcntl_wifexited($status)) ? 'cleanly' : 'badly (' . pcntl_wexitstatus($status) . ')';

			fatalError("An unregistered child process has quit $how. This shouldn't happen; if you're seeing this message you have encountered a bug. Child pid $pid");
		}
	}
}

function phpErrorHandler($level, $string, $file, $line)
{
	if($level == E_USER_ERROR)
	{
		fatalError($string . " in " . $file . " on line " . $line);
	} else {
		recode_log($string . " in " . $file . " on line " . $line);
	}
	return true;
}

class childProcess
{
	static $childrenByPid = array();
	static $childrenByPipe = array();

	//for the benefits of a clearly defined type
	public $pid;
	public $type;
	public $name;
	public $io;
	public $handle;

	const TYPE_MPLYR = 1;
	const TYPE_ENCODER = 2;
	const PIPE = 0;//Index of the stream resource for entries in the io 2d array
	const HANDLER = 1;//Index of the correspondig read event handler callback

	function __construct($type, $name, &$io, &$handle)
	{
		$info = proc_get_status($handle);

		$this->pid = $info['pid'];
		$this->type = $type;
		$this->name = $name;
		$this->io = &$io;
		$this->handle = &$handle;

		childProcess::$childrenByPid[$this->pid] = $this;

		$outpipe = $io[OUT][childProcess::PIPE];
		$errpipe = $io[ERR][childProcess::PIPE];
		childProcess::$childrenByPipe[(int) $outpipe] = $this;
		childProcess::$childrenByPipe[(int) $errpipe] = $this;
	}

	function destruct()
	{
		unset(childProcess::$childrenByPid[$this->pid]);
		unset(childProcess::$childrenByPipe[(int) $this->io[OUT][childProcess::PIPE]]);
		unset(childProcess::$childrenByPipe[(int) $this->io[ERR][childProcess::PIPE]]);
	}
}

class JobStrategy
{
	public static $parts = array();
	private static $fileTitle; //used for readability of logs
	private static $origFile;

	private $sourcefile;
	private $width;
	public $bitrate;
	private $mplyr_opts;
	private $enc_opts;
	private $enc_videofile;
	private $enc_audiofile;
	private $thumbfile;

	public static function buildStrategy($original, $newBitrates, $newWidths = array())
	{
		JobStrategy::$parts = array();
		JobStrategy::$origFile = $original;
		JobStrategy::$fileTitle = $original->getTitle()->getText();

		// make sure we don't regenerate any existing versions
		$dbs = wfGetDb(DB_SLAVE);
		$key = $original->getTitle()->getDBkey();
		$result = $dbs->select('avrecode', array('bitrate', 'status'), array("img_name = '$key'", 'container_format = "ogg"'), __METHOD__);

		$existings = array();
		while($row = $dbs->fetchRow($result))
		{
			//if status is pending but no nodes are claiming it,
			//delete it from the table so this node can try it
			if($row['status'] == 'PENDING')
			{
				$dbm = wfGetDB(DB_MASTER);
				$r = $dbm->selectRow('avrecode_farm', array('notify_address'), array("img_name = '$key'"), __METHOD__);
				if(! is_object($r))
				{
					$dbm->delete('avrecode', array("img_name = '$key'", "bitrate = '" . $row['bitrate'] . "'", "container_format = 'ogg'"));
				} else {
					$existings[] = $row['bitrate'];
				}
			} else {
				$existings[] = $row['bitrate'];
			}
		}

		if(count($existings))
		{
			recode_log("Not remaking " . count($existings) . " existing versions of " . JobStrategy::$fileTitle . ":");
			$length = count($newBitrates);
			for($i = 0; $i < $length; $i++)
			{
				if(in_array($newBitrates[$i], $existings))
				{
					recode_log("\t" . $newBitrates[$i] . " kbps");
					unset($newBitrates[$i]);
					unset($newWidths[$i]);
				}
			}
		}

		if(! count($newBitrates))
		{
			global $currentJob;
			$currentJob = false;
			return false;
		}

		$origWidth = $original->getWidth();
		$mediatype = $original->getMediaType();

		if($mediatype == MEDIATYPE_AUDIO)
		{
			foreach($newBitrates AS $newBitrate)
			{
				if($newBitrate <= $origBitrate && isset($newBitrate))
				{
					JobStrategy::$parts[] = new JobStrategy($newBitrate);
				}
			}
		} else if($mediatype == MEDIATYPE_VIDEO)
		{
			reset($newWidths);
			reset($newBitrates);
			$currWidth = current($newWidths);
			$currBitrate = current($newBitrates);
			do
			{
				if($currWidth <= $origWidth)
				{
					JobStrategy::$parts[] = new JobStrategy($currBitrate, $currWidth);
				}
			} while(($currWidth = next($newWidths)) && ($currBitrate = next($newBitrates)));

			if(! count(JobStrategy::$parts) && ! count($existings))
			{
				/* the clip has a smaller frame size than the smallest preset.
				* Make a single version in the original frame size and lowest 
				* preset bitrate, unless it happens to be ogg theora already, in
				* which case no recoding will take place
				*/
				$mime = $original->getMimeType();
				if(strcasecmp($mime, 'video/ogg') === 0 || strcasecmp($mime, 'application/ogg') === 0)
				{
					/*probably the presentation code should just know to use the 
					original when these contitions are true.
					*/
					return true;
				} else {
					reset($newBitrates);
					$min = current($newBitrates);
					while($br = next($newBitrates))
					{
						if($br < $min) $min = $br;
					}
					JobStrategy::$parts[] = new JobStrategy($min, $origWidth);
				}
			}
		} else {
			recode_log("File \"" . JobStrategy::$fileTitle . "\" is not a supported media type.");
			recode_log($mediatype);
			global $currentJob;
			$currentJob = false;
			return false;
		}

		if(count(JobStrategy::$parts))
		{
			/* Sort the parts. When done, the parts will be descending by width when
			*  available (ie video), or bitrate otherwise. Because the initial
			*  part's decompressed video output may be cached, this ensures the 
			*  initial part is the largest, avoiding upscaling in subsequent parts.
			*/
			usort(JobStrategy::$parts, array(__CLASS__, "uSortCmp"));

			if($original->isLocal())
			{
				$addy = $original->getPath();
			} else {
				$addy = $original->getUrl();
			}
			JobStrategy::$parts[0]->sourcefile = $addy;
			JobStrategy::$parts[0]->enc_videofile = 'stream.yuv';
			JobStrategy::$parts[0]->enc_audiofile = 'stream.wav';

			if(count(JobStrategy::$parts) > 1)
			{
				//tell encoder in first part to cache
				JobStrategy::$parts[0]->enc_opts .= " -c";
				for($i = 1; $i < count(JobStrategy::$parts); $i++)
				{
					JobStrategy::$parts[$i]->sourcefile = 'cache_stream.yuv';
					JobStrategy::$parts[$i]->enc_audiofile = 'cache_stream.wav';
					JobStrategy::$parts[$i]->enc_videofile = 'stream.yuv';
					JobStrategy::$parts[$i]->mplyr_opts = '-speed 100';
				}
			}

			//write rows for each part to avrecode.
			$dbm = wfGetDB(DB_MASTER);
			foreach(JobStrategy::$parts AS $p)
			{
				$dbm->insert('avrecode', array('img_name' => $original->getTitle()->getDBKey(), 'container_format' => 'ogg', 'bitrate' => $p->bitrate), __METHOD__);
			}

			reset(JobStrategy::$parts);
		}
		return true;
	}

	public static function uSortCmp(JobStrategy $a, JobStrategy $b)
	{
		if($a->width)
		{
			return $b->width - $a->width;
		} else if($b->width)
		{
			return -1;
		} else {
			return $b->bitrate - $a->bitrate;
		}
	}

	public function __construct($bitrate, $width = null)
	{
		$this->bitrate = $bitrate;
		$this->width = $width;

		$thumbName = "{$bitrate}kbps-";
		$dotPos = strrpos(JobStrategy::$origFile->getName(), '.');
		if($dotPos)
		{
			$thumbName .= substr(JobStrategy::$origFile->getName(), 0, $dotPos);
		}
		$thumbName .= '.ogg';

		$this->thumbfile = JobStrategy::$origFile->getThumbPath() . '/' . $thumbName;
		recode_log("new part br:" . $this->bitrate . ", w:" . $width);
	}

	public static function runNextPart($started = false)
	{
		if($started)
		{
			foreach(childProcess::$childrenByPid AS $pid => $child)
			{
				posix_kill($pid, SIGTERM);
				proc_close($child->handle);
				//removing from childrenByPid is not appropriate until SIGCHLD
			}

			$strategy = next(JobStrategy::$parts);
		} else {
			$started = true;
			$strategy = current(JobStrategy::$parts);
		}

		if(! $strategy)
		{
			//no more parts
			recode_log("No more parts, looking for new job...");
			global $currentJob;
			$currentJob = false;
			$dbm = wfGetDB(DB_MASTER);
			$dbm->update('avrecode_farm', array('img_name' => ''), array('notify_address' => NOTIFY_SCRIPT_URL), __METHOD__);
			return seekJob();
		}

		$descriptorspec = array(
		   IN => array("pipe", "r"),
		   OUT => array("pipe", "w"),
		   ERR => array("pipe", "w")
		);

		$mplyr_io = array();
		$enc_io = array();

		//start new decoder as per $strategy settings.
		/* if it were made possible to manipulate the software scaler in slave
		* mode, restarting MPlayer like this wouldn't be necessary.
		*/
		$mplyr_command = 'mplayer -idle -slave -ao pcm:waveheader:fast:file=stream.wav -vo yuv4mpeg -vf scale=' . $strategy->width . ':-2 -af format=u16le ' . $strategy->mplyr_opts;
		//recode_log("Starting MPlayer with: " . $mplyr_command);
		$mplyr_resource = proc_open($mplyr_command, $descriptorspec, $mplyr_io);
		$mplyr_io[IN] = array($mplyr_io[IN], 'no-mplayer-in-handler');
		$mplyr_io[OUT] = array($mplyr_io[OUT], 'onMplayerOutput');
		$mplyr_io[ERR] = array($mplyr_io[ERR], 'onMplayerError');
		new childProcess(childProcess::TYPE_MPLYR, 'MPlayer (' . $strategy->__toString() . ')', $mplyr_io, $mplyr_resource);
		//implicit calling of toString supposedly works as of PHP 5.2

		/**
		* wait for it to load up. The below sync command and sync response is an
		* ugly hack for detecting completion of mplayer's startup sequence. Mplayer
		* only writes the sync response to standard error when it has finished loading.
		*/
		$sync_cmd = "v\n";
		$sync_resp = "Command volume requires at least 1 arguments, we found only 0 so far.\n";
		fwrite($mplyr_io[IN][childProcess::PIPE], $sync_cmd);
		$mplyr_verbosity = '';
		do
		{
			$waitSet = array($mplyr_io[ERR][childProcess::PIPE]);
			$junk = array();
			$startTime = microtime(true);
			$r = stream_select($waitSet, $junk, $junk, 25);
			$select_wait = microtime(true) - $startTime;
			if(!$r)
			{
				if($select_wait > 24)
				{
					fatalError("Mplayer did not start successfully. Any MPlayer error output follows:\n" . $mplyr_verbosity);
				} //otherwise it might have just gotten interrupted by a signal
			} else {
				$mplyr_verbosity .= piperead($mplyr_io[ERR][childProcess::PIPE]);
			}
		} while(strcmp(substr($mplyr_verbosity, -strlen($sync_resp)), $sync_resp)!==0);
		unset($mplyr_verbosity);
		//MPlayer's ready to go!
		fwrite($mplyr_io[IN][childProcess::PIPE], "loadfile " . $strategy->sourcefile . "\n");

		//and the encoder...
		$output = $strategy->thumbfile;
		// ^ the big todo: send recoded files to repo
		$thumbdir = dirname($strategy->thumbfile);
		if(! is_dir($thumbdir) && ! wfMkdirParents($thumbdir))
		{
			//we can't write where we need to, this daemon is useless until fixed.
			abortRecode();
			fatalError("Cannot create thumbnail directory " . $thumbdir);
		}
		global $encoder_directory;
		$enc_command = $encoder_directory . '/encoder_example ' .
			$strategy->enc_opts . ' -a 2 -V ' . $strategy->bitrate . ' ' . $strategy->enc_audiofile . ' ' . $strategy->enc_videofile . ' > ' . $output;
		recode_log("Starting encoder with: " . $enc_command);
		$enc_resource = proc_open($enc_command, 
		$descriptorspec, $enc_io);

		$enc_io[IN] = array($enc_io[IN], 'no-encoder-in-handler');
		$enc_io[OUT] = array($enc_io[OUT], 'onEncoderOutput');
		$enc_io[ERR] = array($enc_io[ERR], 'onEncoderOutput');

		new childProcess(childProcess::TYPE_ENCODER, "Encoder (" . $strategy->__toString() . ")", $enc_io, $enc_resource);
	}

	public function __toString()
	{
		return ('"' . JobStrategy::$fileTitle . "\", w:$this->width;b:$this->bitrate;source:$this->sourcefile");
	}
}

function onMplayerOutput($pipe)
{
	piperead($pipe);
}

function onMplayerError($pipe)
{
	$error = piperead($pipe);
	if(strlen($error))
	{
		if(substr($error, 0, 9) == "SwScaler:")
		{
			return;
		}
		recode_log("On Mplayer error pipe: " . piperead($pipe));
	}
}

function onEncoderOutput($pipe)
{
	$lines = explode("\n", piperead($pipe));
	foreach($lines AS $line)
	{
		if($line == "done.")
		{
			global $cancelFlag;
			if(!$cancelFlag)
			{
				recode_log("Part done. Running next...");

				//mark this part as available in avrecode.
				$dbm = wfGetDB(DB_MASTER);
				$currStrategy = current(JobStrategy::$parts);
				global $currentJob;
				$dbm->update('avrecode', array('status' => 'AVAILABLE'), array('img_name' => $currentJob, 'bitrate' => $currStrategy->bitrate, 'container_format' => 'ogg'), __FUNCTION__);

				JobStrategy::runNextPart(true);
			}
		} else {
			//recode_log("e: " . $line);
		}
	}
}