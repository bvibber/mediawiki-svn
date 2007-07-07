<?php
/**
* Currently this file contains a set of classes (inheriting from 
* AVUploadHandler) to be used with the UploadVerification hook for precision
* audio and video upload verification. These upload handler classes utilize the
* various AVInspector classes also included in this file. I have implemented
* AVInspectors powered by ffmpeg-php and MPlayer, as well as a composite 
* class that implements its methods in terms of the other AVInspectors.
*
* For audio file verification, ffmpeg is completely suitable, and so the 
* AudioUploadHandler only uses FfmpegAVInspector. However, ffmpeg occasionally 
* fails to validate a file that MPlayer can decode, and in this event the 
* composite class will enlist the help of MPlayerAVInspector as well. Thus,
* VideoUploadHandler works directly with CompositeAVInspector.
*
* Since the tools powering the AVInspector classes also provide ready access to 
* metadata, I will also be calling on these classes to extract information like
* (chronological) file length and bitrates. Current design has this metadata 
* extraction taking place in media handlers, not upload verification handlers,
* so for now I'm having AVInspectors cache the results of file examinations, 
* expecting that media handlers will request metadata information later as part
* of the same http request.
*/

interface AVInspector
{
	function hasVideoStream();

	function hasAudioStream();

	function hasDecodableVideoStream();

	function hasDecodableAudioStream();

	function getFrameWidth();

	function getFrameHeight();

	/*
	* These last three methods can be implemented in terms of the others, and I 
	* have done so in AVInspectorHelper.
	*/
	function isDecodable();

	function isAV();

	function isAudio();
}

abstract class AVInspectorHelper implements AVInspector
{
	/*
	* Shortcut method that passes good audio, or video with or without audio.
	* Use has(Decodable)AudioStream for that.
	*/
	public function isDecodable()
	{
		if($this->isAudio())
		{
			return $this->hasDecodableAudioStream();
		} else {
			return $this->hasDecodableVideoStream();
		}
	}

	public function isAV()
	{
		return $this->hasAudioStream() || $this->hasVideoStream();
	}

	public function isAudio()
	{
		return $this->isAV() && ! $this->hasVideoStream();
	}
}

class CompositeAVInspector extends AVInspectorHelper implements AVInspector
{
	private $inspectors;
	private $file;

	public function __construct($file)
	{
		$this->file = $file;

		/* inspectors are given as classnames only...instantiating
		*  now would cause all inspectors to run, defeating the purpose of the
		*  composite class.
		*/
		$this->inspectors = array();

		// see if we have access to the ffmpeg api...
		$extension = "ffmpeg";
		$extension_soname = $extension . "." . PHP_SHLIB_SUFFIX;
		// load extension
		if(!extension_loaded($extension)) {
			@dl($extension_soname);
		}

		// if we can use the ffmpeg api, make an inspector based on it
		if(class_exists('ffmpeg_movie'))
			$this->inspectors[] = "FfmpegAVInspector";

		// todo: scan for midentify
		$this->inspectors[] = "MPlayerAVInspector";
	}

	/* the below duplication is necessary for the parser to think we've 
	* implemented the interface. Otherwise, using __call would work.
	*/
	function hasVideoStream()
	{
		return $this->composite(__FUNCTION__);
	}

	function hasAudioStream()
	{
		return $this->composite(__FUNCTION__);
	}

	function hasDecodableVideoStream()
	{
		return $this->composite(__FUNCTION__);
	}

	function hasDecodableAudioStream()
	{
		return $this->composite(__FUNCTION__);
	}

	function getFrameWidth()
	{
		return $this->composite(__FUNCTION__);
	}

	function getFrameHeight()
	{
		return $this->composite(__FUNCTION__);
	}

	private function composite($method)
	{
		foreach($this->inspectors AS &$inspector)
		{
			if(is_string($inspector)) $inspector = new $inspector($this->file);
			$value = $inspector->$method();

			echo(get_class($inspector) . " sets $method as ");
			if($value === true) echo 'true'; else if($value === false) echo 'false'; else echo $value;
			echo "<br>\n";
			
			if((bool) $value)
			{
				return $value;
			}
		}
		return false;
	}
}

class MPlayerAVInspector extends AVInspectorHelper implements AVInspector
{
	/* Since current code involves upload handlers validating through this 
	* class, and media handlers extracting metadata from this class, results
	* of file inspections are cached here by path. This sucks, it should really
	* just all be done in the upload handler, and have the media handler do 
	* presentation only...but nobody wants to discuss this.
	*/
	private static $info;
	private $path;

	public function __construct($path)
	{
		$path = escapeshellarg($path);
		$this->path = $path;
		if(! is_array(self::$info[$path]))
		{
			exec("/data/mplayer-checkout-2007-06-08/TOOLS/midentify $path", $output);
		
			self::$info[$path] = array();
			foreach($output AS $line)
			{
				$p = strpos($line, '=');
				$key = substr($line, 0, $p);
				$value = substr($line, $p+1);
				self::$info[$path][$key] = $value;
			}
		}
	}

	/* enables some shorthand for most AVInspector methods.
	* Exact vaules can still be acquired through $this->info
	*/
	private function __get($attribute)
	{
		if($attribute != 'info')
		{
			$value = trim(self::$info[$this->path]['ID_' . strtoupper($attribute)]);
			if(strlen($value))
			{
				return true;
			} else {
				return false;
			}
		} else {
			return self::$info[$this->path];
		}
	}

	function hasVideoStream()
	{
		return $this->video_format;
	}

	function hasAudioStream()
	{
		return $this->audio_id;
	}

	function hasDecodableVideoStream()
	{
		return $this->video_codec;
	}

	function hasDecodableAudioStream()
	{
		return $this->audio_codec;
	}

	function getFrameWidth()
	{
		return $this->info['ID_VIDEO_WIDTH'];
	}

	function getFrameHeight()
	{
		return $this->info['ID_VIDEO_HEIGHT'];
	}
}

class FfmpegAVInspector extends AVInspectorHelper implements AVInspector
{
	private static $cache;
	private $path;

	/* used to be private $movie; cache was added later.
	* The __get magic method was the easy way to make it still
	* work.
	*/
	public function __get($name)
	{
		return self::$cache[$this->path];
	}

	public function __construct($file)
	{
		$this->path = $file;
		if(! is_object(self::$cache[$file]))
		{
			// see if we have access to the ffmpeg api...
			$extension = "ffmpeg";
			$extension_soname = $extension . "." . PHP_SHLIB_SUFFIX;
			// load extension
			if(!extension_loaded($extension)) {
				@dl($extension_soname);
			}

			self::$cache[$file] = @new ffmpeg_movie($file);

			/* if ffmpeg can't decypher this file, we don't even get an object.
			* The below little hack prevents all the member methods from having to 
			* check for this conidtion.
			*/
			if(! is_object(self::$cache[$file]))
			{
				self::$cache[$file] = new FalseClass();
				//no anonymous classes in php :(
			}
		}
	}

	public function hasVideoStream()
	{
		return $this->movie->getFrameCount() > 1;
	}

	public function hasAudioStream()
	{
		return $this->movie->hasAudio();
	}

	public function hasDecodableVideoStream()
	{
		//for now, do this by attempting to render the first frame.
		//There may be a less expensive way...getVideoCodec, but not sure
		$frame = $this->movie->getFrame(1);
		return $frame !== false && $frame->getWidth() && $frame->getHeight();
	}

	public function hasDecodableAudioStream()
	{
		return $this->hasAudioStream() && (bool) $this->movie->getAudioCodec() && (bool) $this->movie->getAudioSampleRate();
	}


	public function getFrameWidth()
	{
		return $this->movie->getFrameWidth();
	}

	public function getFrameHeight()
	{
		return $this->movie->getFrameHeight();
	}
}

class FalseClass
{
	public function __call($name, $args)
	{
		return false;
	}
}

abstract class AVUploadHandler
{
	protected $inspector;

	public function __construct()
	{
		$this->inspector = "CompositeAVInspector";
	}

	public abstract function getSupportedExtensions();

	public function onUploadVerification($useless, $path, &$errorRef, $ext)
	{
		$exts = $this->getSupportedExtensions();
		if(!in_array($ext, $exts)) return true;
		/*SUCKS! This way, everything else doesn't matter if the user misnames a 
		* file. We need *exclusively* upload handlers by extension.
		*/

		/*Ultimately uploading should be rewritten so that
		* this code only gets called if MimeMagic says the extension must 
		* validate as an audio or video file.
		*/

		$start = microtime(true);

		$inspector = new $this->inspector($path);

		// todo whatever needs to happen to make errors multilingual.
		// some debug & performance output is left for now for your reference,
		// should you choose to run this.
		if(! $inspector->isAV())
		{
			$errorRef .= "<br />This audio or video file is unrecognized or corrupt.";
			echo ("AV upload checking done in " . (microtime(true) - $start));
			return false;
		}
		
		if(! $inspector->isDecodable())
		{
			$errorRef = "This file cannot be prepared for the WikiMedia player. You will be unable to add it to article pages. It will be available for direct download only.";
			echo ("AV upload checking done in " . (microtime(true) - $start));
			return true;
		}

		if(! $inspector->hasDecodableAudioStream())
		{
			$errorRef = "The audio component of this video file is in an unrecognized format. No sound will be available in the WikiMedia player.";
			echo ("AV upload checking done in " . (microtime(true) - $start));
			return true;
		}

		//also could easily add warnings for too small/to large frame size or 
		//other metadata attributs here.

		echo ("AV upload passed all checks in " . (microtime(true) - $start));
		return true;
	}
}

class VideoUploadHandler extends AVUploadHandler
{
	public function getSupportedExtensions()
	{
		$mime = MimeMagic::singleton();
		$mimeKnownVideoExts = $mime->getExtensionsForType('video');

		$others = "avi mov asf yuv mp4 mpeg";

		$out = array_merge(
			 explode(' ', $mimeKnownAudioExts),
			 explode(' ', $mimeKnownVideoExts),
			 explode(' ', $others)
			);

		return $out;
	}
}

class AudioUploadHandler extends AVUploadHandler
{
	public function __construct()
	{
		$this->inspector = "FfmpegAVInspector";
	}

	public function getSupportedExtensions()
	{
		$mime = MimeMagic::singleton();
		return explode(' ', $mime->getExtensionsForType('audio'));
	}
}












/**
* This is just a stub class I was using to test my work making MimeMagic more 
* modular. See my versions of MimeMagic.php and includes/media/MimePlugin.php.
*/

class AVMimePlugin extends MimePlugin
{
	public static function onMimeMagicRegisterPlugins($core)
	{
		/*make one of itself..it'd be slick if a universal implementation of 
		* this method could be made in the superclass, but alas...php bug
		* #30423...
		*/
		new self($core);
	}

	protected function __construct($core)
	{
		parent::__construct($core);

		//$this->registerContentType('video', true);
	}

	public function getContentType($file)
	{
		return 'unknown/unknown';
	}

	protected function mimeTypes()
	{
		//return 'video/x-ms-asf asf asx';
	}

	protected function mimeInfo()
	{
		//return 'video/x-ms-asf [VIDEO]';
	}

	public function getMediaType($file, $mime)
	{
		if($mime == 'application/ogg')
		{
			return MEDIATYPE_VIDEO;
		} else {
			return false;
		}
	}
}
?>