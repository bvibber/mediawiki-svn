<?php
function verifyFifo($name)
{
	if(! file_exists($name))
	{
		//no fifo (or file), attempt to create one
		if(function_exists("posix_mkfifo"))
		{
			if(! posix_mkfifo($name, 0700))
			{
				return false;
			} else {
				return true;
			}
		} else {
			//try to do it via shell
			if(strlen(trim(shell_exec("mkfifo " . escapeshellarg($name)))) == 0)
			{
				chmod($name, 0700);
				return true;
			} else {
				return false;
			}
		}
	} else if(filetype($name) != 'fifo')
	{
		return false;
	} else {
		return true;
	}
}

function message_send($fh, $data)
{
	if(!is_resource($fh)) return false;
	$data = serialize($data);
	$msgLength = strlen($data);
	if($msgLength > 9999)
	{
		return false;
	} else {
		$msgLength = str_pad($msgLength, 4, "0", STR_PAD_LEFT);
		if(! fwrite($fh, $msgLength)) return false;
		if(! fwrite($fh, $data)) return false;
		return true;
	}
}

function message_readresponse($fh, &$data)
{
	if(!is_resource($fh)) return false;
	//get message length
	$bytes = fread($fh, 4);
	if(!$bytes || $bytes != intval($bytes))
	{
		$data = "Expecting byte count, got: $bytes";
		return false;
	}
	$bytes = intval($bytes);
	$read = fread($fh, $bytes);
	if(!$read || $bytes != strlen($read))
	{
		$data = "Fread stopped short of $bytes bytes. Got: $read";
		return false;
	} else {
		$data = unserialize($read);
		return true;
	}
}