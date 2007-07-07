<?php
/** Module defining helper functions for detecting and dealing with mime types.
 *
 */

 /** Defines a set of well known mime types
 * This is used as a fallback to mime.types files.
 * An extensive list of well known mime types is provided by
 * the file mime.types in the includes directory.
 */
define('MM_WELL_KNOWN_MIME_TYPES',<<<END_STRING
application/ogg ogg ogm
application/pdf pdf
application/x-javascript js
application/x-shockwave-flash swf
audio/midi mid midi kar
audio/mpeg mpga mpa mp2 mp3
audio/x-aiff aif aiff aifc
audio/x-wav wav
audio/ogg ogg
image/x-bmp bmp
image/gif gif
image/jpeg jpeg jpg jpe
image/png png
image/svg+xml image/svg svg
image/tiff tiff tif
image/vnd.djvu djvu
image/x-portable-pixmap ppm
text/plain txt
text/html html htm
video/ogg ogm ogg
video/mpeg mpg mpeg
END_STRING
);

 /** Defines a set of well known mime info entries
 * This is used as a fallback to mime.info files.
 * An extensive list of well known mime types is provided by
 * the file mime.info in the includes directory.
 */
define('MM_WELL_KNOWN_MIME_INFO', <<<END_STRING
application/pdf [OFFICE]
text/javascript application/x-javascript [EXECUTABLE]
application/x-shockwave-flash [MULTIMEDIA]
audio/midi [AUDIO]
audio/x-aiff [AUDIO]
audio/x-wav [AUDIO]
audio/mp3 audio/mpeg [AUDIO]
application/ogg audio/ogg video/ogg [MULTIMEDIA]
image/x-bmp image/bmp [BITMAP]
image/gif [BITMAP]
image/jpeg [BITMAP]
image/png [BITMAP]
image/svg+xml [DRAWING]
image/tiff [BITMAP]
image/vnd.djvu [BITMAP]
image/x-portable-pixmap [BITMAP]
text/plain [TEXT]
text/html [TEXT]
video/ogg [VIDEO]
video/mpeg [VIDEO]
unknown/unknown application/octet-stream application/x-empty [UNKNOWN]
END_STRING
);

#note: because this file is possibly included by a function,
#we need to access the global scope explicitely!
global $wgLoadFileinfoExtension;

if ($wgLoadFileinfoExtension) {
	if(!extension_loaded('fileinfo')) dl('fileinfo.' . PHP_SHLIB_SUFFIX);
}

/** 
 * Implements functions related to mime types such as detection and mapping to
 * file extension.
 *
 * Instances of this class are stateles, there only needs to be one global 
 instance
 * of MimeMagic. Please use MimeMagic::singleton() to get that instance.
 */
class MimeMagic {

	/**
	* Mapping of media types to arrays of mime types.
	* This is used by findMediaType and getMediaType, respectively
	*/
	var $mMediaTypes= NULL;

	/** Map of mime type aliases
	*/
	var $mMimeTypeAliases= NULL;

	/** map of mime types to file extensions (as a space seprarated list)
	*/
	var $mMimeToExt= NULL;

	/** map of file extensions types to mime types (as a space seprarated list)
	*/
	var $mExtToMime= NULL;

	/**
	* Multidimensional array indexed by MIME type, then subtype,
	* containing among other things references to any appropriate MimePlugins.
	*/
	private $pluginsByType;

	/**
	* Multidimensional array indexed by file extension, containing
	* references to any appropriate MimePlugins.
	*/
	private $pluginsByExt;

	/**
	* flat list of extensions with reliable detection. Generation is
	* quite expensive, so this list is computed once and cached here.
	*/
	private $recognizableExtensions;

	/**
	* Mime Type strings from plugins (same format as MM_WELL_KNOWN_MIME_TYPES) 
	* are aggregated here
	*/
	private $pluginMimeTypes;

	/**
	* Mime Info strings from plugins (same format as MM_WELL_KNOWN_MIME_INFO) 
	* are aggregated here
	*/
	private $pluginMimeInfo;

	/** The singleton instance
	 */
	private static $instance;

	/** Initializes the MimeMagic object. Declared private; use 
	*   MimeMagic::singleton() to obtain the instance in client code.
	*
	* This constructor parses the mime.types and mime.info files and build 
	* internal mappings.
	*
	*/
	private function __construct() {
		global $wgMimeTypeFile, $IP, $wgHooks;

		# Add hooks to register plugins included in the main distribution
		$wgHooks['MimeMagicRegisterPlugins'][] = 'AVMimePlugin';

		$this->pluginMimeTypes = $this->pluginMimeInfo = '';
		$this->pluginsByType = array();
		//Ask for special detection plugins. Return value doesn't matter.
		wfRunHooks('MimeMagicRegisterPlugins', array($this));

		/*
		*   --- load mime.types ---
		*/

		$types = MM_WELL_KNOWN_MIME_TYPES;

		if ( $wgMimeTypeFile == 'includes/mime.types' ) {
			$wgMimeTypeFile = "$IP/$wgMimeTypeFile";
		}
		
		if ( $wgMimeTypeFile ) {
			if ( is_file( $wgMimeTypeFile ) and is_readable( $wgMimeTypeFile ) ) 
			{
				wfDebug( __METHOD__.": loading mime types from 
				$wgMimeTypeFile\n" );
				$types .= "\n";
				$types .= file_get_contents( $wgMimeTypeFile );
			} else {
				wfDebug( __METHOD__.": can't load mime types from $wgMimeTypeFile. Proceeding with buit-in & plugin types only.\n" );
			}
		} else {
			wfDebug( __METHOD__.": no mime types file defined, using built-in & plugin types only.\n" );
		}

		$types .= $this->pluginMimeTypes; //will start with newline or be empty
		unset($this->pluginMimeTypes); //no longer needed

		$types = str_replace( array( "\r\n", "\n\r", "\n\n", "\r\r", "\r" ), 
		"\n", $types );
		$types = str_replace( "\t", " ", $types );

		$this->mMimeToExt = array();
		$this->mToMime = array();

		$lines = explode( "\n",$types );
		foreach ( $lines as $s ) {
			$s = trim( $s );
			if ( empty( $s ) ) continue;
			if ( strpos( $s, '#' ) === 0 ) continue;

			$s = strtolower( $s );
			$i = strpos( $s, ' ' );

			if ( $i === false ) continue;

			#print "processing MIME line $s<br>";

			$mime = substr( $s, 0, $i );
			$ext = trim( substr($s, $i+1 ) );

			if ( empty( $ext ) ) continue;

			if ( !empty( $this->mMimeToExt[$mime] ) ) {
				$this->mMimeToExt[$mime] .= ' ' . $ext;
			} else {
				$this->mMimeToExt[$mime] = $ext;
			}

			$extensions = explode( ' ', $ext );

			foreach ( $extensions as $e ) {
				$e = trim( $e );
				if ( empty( $e ) ) continue;

				if ( !empty( $this->mExtToMime[$e] ) ) {
					$this->mExtToMime[$e] .= ' ' . $mime;
				} else {
					$this->mExtToMime[$e] = $mime;
				}
			}
		}

		/*
		*   --- load mime.info ---
		*/
		$info = MM_WELL_KNOWN_MIME_INFO;

		global $wgMimeInfoFile;
		if ( $wgMimeInfoFile == 'includes/mime.info' ) {
			$wgMimeInfoFile = "$IP/$wgMimeInfoFile";
		}

		if ( $wgMimeInfoFile ) {
			if ( is_file( $wgMimeInfoFile ) and is_readable( $wgMimeInfoFile ) ) 
			{
				wfDebug( __METHOD__.": loading mime info from $wgMimeInfoFile\n" );
				$info .= "\n";
				$info .= file_get_contents( $wgMimeInfoFile );
			} else {
				wfDebug(__METHOD__.": can't load mime info from $wgMimeInfoFile\n");
			}
		} else {
			wfDebug(__METHOD__.": no mime info file defined, using built-in & plugin types only.\n");
		}

		$info .= $this->pluginMimeInfo; //appended here so plugins get final say
		unset($this->pluginMimeInfo);

		$info = str_replace( array( "\r\n", "\n\r", "\n\n", "\r\r", "\r" ), 
		"\n", $info);
		$info = str_replace( "\t", " ", $info );

		$this->mMimeTypeAliases = array();
		$this->mMediaTypes = array();

		$lines = explode( "\n", $info );
		foreach ( $lines as $s ) {
			$s = trim( $s );
			if ( empty( $s ) ) continue;
			if ( strpos( $s, '#' ) === 0 ) continue;

			$s = strtolower( $s );
			$i = strpos( $s, ' ' );

			if ( $i === false ) continue;

			#print "processing MIME INFO line $s<br>";

			$match = array();
			$exp = '!\s*\[\s*(\w+)\s*\]!';
			if ( preg_match($exp, $s, $match ) ) {
				$s = preg_replace($exp, '', $s );
				$mtype = trim( strtoupper( $match[1] ) );
			} else {
				$mtype = MEDIATYPE_UNKNOWN;
			}

			$m = explode( ' ', $s );

			if ( !isset( $this->mMediaTypes[$mtype] ) ) {
				$this->mMediaTypes[$mtype] = array();
			}

			foreach ( $m as $mime ) {
				$mime = trim( $mime );
				if ( empty( $mime ) ) continue;

				$this->mMediaTypes[$mtype][] = $mime;
			}

			if ( sizeof( $m ) > 1 ) {
				$main = $m[0];
				for ( $i=1; $i<sizeof($m); $i += 1 ) {
					$mime = $m[$i];
					$this->mMimeTypeAliases[$mime] = $main;
				}
			}
		}

		/* Build index of plugins by extension. Plugins can influence the 
		*  extensions they are indexed under by implementing 
		*  MimePlugin->mimeTypes()
		*/
		$this->pluginsByExt = array();
		foreach($this->pluginsByType AS $type => $subtypes)
		{
			foreach($subtypes AS $subtype => $detectors)
			{
				foreach($detectors AS $detector)
				{
					if($detector[1]) //if authoritative
					{
						$exts = $this->getExtensionsForType("$type/$subtype");

						foreach(explode(' ', $exts) AS $ext)
						{
							$ext = trim($ext); if(empty($ext)) continue;
							$this->pluginsByExt[$ext][] = $detector;
						}
					}
				}
			}
		}

	}

	/**
	 * Get an instance of this class
	 */
	static function &singleton() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new MimeMagic;
		}

		return self::$instance;
	}

	/**
	* Populates private plugin arrays by MIME type (and subtype). This method should only be 
	* called by MimePlugin::registerContentType
	*/
	public function registerPluginByContentType(MimePlugin $detector, $type,
		$subtype, $authoritative)
	{
		$this->pluginsByType[$type][$subtype][] = array($detector, 
		$authoritative);
	}

	/**
	* Intended to be called by plugins during their construction/registration 
	* process. By allowing plugins to append MIME type information to the
	* built-in well-known types, complete support for a new MIME type can be
	* assured by simply installing a plugin for it, without admins having to worry
	* about the contents of their mime.types or mime.info files as well.
	*
	* @todo Move loading code in constructor to a helper method, make it more
	*  verbose about formatting errors, and use it to validate typesStrings on a 
	*  plugin by plugin basis.
	*
	* @param string typesString The mime types and extensions supported by this 
	*  plugin, in the format of a mime.types file. (See MM_WELL_KNOWN_MIME_TYPES 
	*  for an example.)
	*/
	public function addMimeTypes(&$typesString)
	{
		$this->pluginMimeTypes .= "\n" . $typesString;
	}

	/**
	* Intended to be called by plugins.
	*
	* @param string infoString Mime types supported by this plugin and their 
	* associated media types, in the format of a mime.info file. (See 
	* MM_WELL_KNOWN_MIME_INFO for an example.)
	*/
	public function addMimeInfo(&$infoString)
	{
		$this->pluginMimeInfo .= "\n" . $infoString;
	}

	/** returns a list of file extensions for a given mime type
	* as a space separated string.
	*/
	function getExtensionsForType( $mime ) {
		if(func_num_args() == 1)
		{
			$mime = strtolower( func_get_arg(0) );
			$parts = MimeMagic::splitMimeString($mime);
		} else {
			$mime = strtolower(func_get_arg(0) . "/" . func_get_arg(1));
			$parts[0] = strtolower(func_get_arg(0));
			$parts[1] = strtolower(func_get_arg(1));
		}

		if($parts[1] === '*')
		{
			//lookup all main types matching parts[0]
			$r = '';
			foreach($this->mMimeToExt AS $mimecheck => $exts)
			{
				$p = MimeMagic::splitMimeString($mimecheck);
				if(strcmp($p[0], $parts[0]) === 0)
				{
					$r .= ' ' . $exts;
				}
			}
		} else {
			$r = @$this->mMimeToExt[$mime];

			if ( @!$r and isset( $this->mMimeTypeAliases[$mime] ) ) {
				$mime = $this->mMimeTypeAliases[$mime];
				$r = @$this->mMimeToExt[$mime];
			}
		}

		return trim($r);
	}

	/** returns a list of mime types for a given file extension
	* as a space separated string.
	*/
	function getTypesForExtension( $ext ) {
		$ext = strtolower( $ext );

		$r = isset( $this->mExtToMime[$ext] ) ? $this->mExtToMime[$ext] : null;
		return $r;
	}

	/** returns a single mime type for a given file extension.
	* This is always the first type from the list returned by 
	getTypesForExtension($ext).
	*/
	function guessTypesForExtension( $ext ) {
		$m = $this->getTypesForExtension( $ext );
		if ( is_null( $m ) ) return NULL;

		$m = trim( $m );
		$m = preg_replace( '/\s.*$/', '', $m );

		return $m;
	}


	/** tests if the extension matches the given mime type.
	* returns true if a match was found, NULL if the mime type is unknown,
	* and false if the mime type is known but no matches where found.
	*/
	function isMatchingExtension( $extension, $mime ) {
		$ext = $this->getExtensionsForType( $mime );

		if ( !$ext ) {
			return NULL;  //unknown
		}

		$ext = explode( ' ', $ext );

		$extension = strtolower( $extension );
 		if ( in_array( $extension, $ext ) ) {
			return true;
		}

		return false;
	}

	/** returns true if the mime type is known to represent
	* an image format supported by the PHP GD library.
	*/
	function isPHPImageType( $mime ) {
		#as defined by imagegetsize and image_type_to_mime
		static $types = array(
			'image/gif', 'image/jpeg', 'image/png',
			'image/x-bmp', 'image/xbm', 'image/tiff',
			'image/jp2', 'image/jpeg2000', 'image/iff',
			'image/xbm', 'image/x-xbitmap',
			'image/vnd.wap.wbmp', 'image/vnd.xiff',
			'image/x-photoshop',
			'application/x-shockwave-flash',
		);

		return in_array( $mime, $types );
	}

	/**
	 * Returns true if the extension represents a type which can
	 * be reliably detected from its content. Use this to determine
	 * whether strict content checks should be applied to reject
	 * invalid uploads; if we can't identify the type we won't
	 * be able to say if it's invalid.
	 *
	 * @return bool
	 */
	function isRecognizableExtension( $extension ) {
		if(! isset($this->recognizableExtensions))
		{
			$pluginTypes = array();
			//go over plugins by extension and add authoritative ones
			foreach($this->pluginsByExt AS $ext => $detectors)
			{
				foreach($detectors AS $detector)
				{
					if($detector[1])
					{
						$pluginTypes[] = $ext;
						break;
					}
				}
			}

			 $this->recognizableExtensions = array_merge(array(
				'gif', 'jpeg', 'jpg', 'png', 'swf', 'psd',
				'bmp', 'tiff', 'tif', 'jpc', 'jp2',
				'jpx', 'jb2', 'swc', 'iff', 'wbmp',
				'xbm', 'djvu'
				),
				$pluginTypes
			);
		}

		return in_array( strtolower( $extension ), $this->recognizableExtensions );
	}


	/** mime type detection. This uses detectMimeType to detect the mime type of 
	the file,
	* but applies additional checks to determine some well known file formats 
	that may be missed
	* or misinterpreter by the default mime detection (namely xml based formats 
	like XHTML or SVG).
	*
	* @param string $file The file to check
	* @param mixed $ext The file extension, or true to extract it from the 
	filename. 
	*                   Set it to false to ignore the extension.
	*
	* @return string the mime type of $file
	*/
	function guessMimeType( $file, $ext = true ) {
		$mime = $this->detectMimeType( $file, $ext );

		// Read a chunk of the file
		$f = fopen( $file, "rt" );
		if( !$f ) return "unknown/unknown";
		$head = fread( $f, 1024 );
		fclose( $f );

		$sub4 =  substr( $head, 0, 4 );
		if ( $sub4 == "\x01\x00\x09\x00" || $sub4 == "\xd7\xcd\xc6\x9a" ) {
			// WMF kill kill kill
			// Note that WMF may have a bare header, no magic number.
			// The former of the above two checks is theoretically prone to false positives
			$mime = "application/x-msmetafile";
		}

		if ( strpos( $mime, "text/" ) === 0 || $mime === "application/xml" ) {

			$xml_type = NULL;
			$script_type = NULL;

			/*
			* look for XML formats (XHTML and SVG)
			*/
			if ($mime === "text/sgml" ||
			    $mime === "text/plain" ||
			    $mime === "text/html" ||
			    $mime === "text/xml" ||
			    $mime === "application/xml") {

				if ( substr( $head, 0, 5 ) == "<?xml" ) {
					$xml_type = "ASCII";
				} elseif ( substr( $head, 0, 8 ) == "\xef\xbb\xbf<?xml") {
					$xml_type = "UTF-8";
				} elseif ( substr( $head, 0, 10 ) == 
				"\xfe\xff\x00<\x00?\x00x\x00m\x00l" ) {
					$xml_type = "UTF-16BE";
				} elseif ( substr( $head, 0, 10 ) == 
				"\xff\xfe<\x00?\x00x\x00m\x00l\x00") {
					$xml_type = "UTF-16LE";
				}

				if ( $xml_type ) {
					if ( $xml_type !== "UTF-8" && $xml_type !== "ASCII" ) {
						$head = iconv( $xml_type, "ASCII//IGNORE", $head );
					}

					$match = array();
					$doctype = "";
					$tag = "";

					if ( 
					preg_match( '%<!DOCTYPE\s+[\w-]+\s+PUBLIC\s+["'."'".'"](.*?)["'."'".'"].*>%sim', 
					$head, $match ) ) 
					{
						$doctype = $match[1];
					}
					if ( preg_match( '%<(\w+).*>%sim', $head, $match ) ) {
						$tag = $match[1];
					}

					#print "<br>ANALYSING $file ($mime): doctype= $doctype; tag= $tag<br>";

					if ( strpos( $doctype, "-//W3C//DTD SVG" ) === 0 ) {
						$mime = "image/svg+xml";
					} elseif ( $tag === "svg" ) {
						$mime = "image/svg+xml";
					} elseif ( strpos( $doctype, "-//W3C//DTD XHTML" ) === 0 ) {
						$mime = "text/html";
					} elseif ( $tag === "html" ) {
						$mime = "text/html";
					}
				}
			}

			/*
			* look for shell scripts
			*/
			if ( !$xml_type ) {
				$script_type = NULL;

				# detect by shebang
				if ( substr( $head, 0, 2) == "#!" ) {
					$script_type = "ASCII";
				} elseif ( substr( $head, 0, 5) == "\xef\xbb\xbf#!" ) {
					$script_type = "UTF-8";
				} elseif ( substr( $head, 0, 7) == "\xfe\xff\x00#\x00!" ) {
					$script_type = "UTF-16BE";
				} elseif ( substr( $head, 0, 7 ) == "\xff\xfe#\x00!" ) {
					$script_type= "UTF-16LE";
				}

				if ( $script_type ) {
					if ( $script_type !== "UTF-8" && $script_type !== "ASCII") {
						$head = iconv( $script_type, "ASCII//IGNORE", $head);
					}

					$match = array();

					if ( preg_match( '%/?([^\s]+/)(\w+)%', $head, $match ) ) {
						$mime = "application/x-{$match[2]}";
					}
				}
			}

			/*
			* look for PHP
			*/
			if( !$xml_type && !$script_type ) {

				if( ( strpos( $head, '<?php' ) !== false ) ||
				    ( strpos( $head, '<? ' ) !== false ) ||
				    ( strpos( $head, "<?\n" ) !== false ) ||
				    ( strpos( $head, "<?\t" ) !== false ) ||
				    ( strpos( $head, "<?=" ) !== false ) ||

				    ( strpos( $head, "<\x00?\x00p\x00h\x00p" ) !== false ) ||
				    ( strpos( $head, "<\x00?\x00 " ) !== false ) ||
				    ( strpos( $head, "<\x00?\x00\n" ) !== false ) ||
				    ( strpos( $head, "<\x00?\x00\t" ) !== false ) ||
				    ( strpos( $head, "<\x00?\x00=" ) !== false ) ) {

					$mime = "application/x-php";
				}
			}

		}

		//run plugins
		//if aliased, fetch plugins for both types
		//remove plugins registered as both
		//getContentType
		//re-run final result through aliases

		$plugins = $this->getPluginsForType($mime);

		if(isset($this->mMimeTypeAliases[$mime] ))
		{
			$mime2 = $this->mMimeTypeAliases[$mime];
			$plugins = array_merge((array) $plugins, (array) $this->getPluginsForType($mime2));
			MimeMagic::killPluginDupes($plugins);
		}

		foreach($plugins AS $detector)
		{
			$plugin = $detector[0];
			$authoritative = $detector[1];

			$pMime = $plugin->getContentType($file);
			if(strpos($pMime, 'unknown/unknown') === false || $authoritative)
			{
				$mime = $pMime;
				break;
			}
		}

		if ( isset( $this->mMimeTypeAliases[$mime] ) ) {
			$mime = $this->mMimeTypeAliases[$mime];
		}

		wfDebug(__METHOD__.": final mime type of $file: $mime\n");
		return $mime;
	}

	private static function killPluginDupes(&$plugins)
	{
		$out = array();
		$existingPlugins = array();

		foreach($plugins AS $newDetector)
		{
			if(! in_array($newDetector[0], $existingPlugins))
			{
				$out[] = $newDetector;
				$existingPlugins[] = $newDetector[0];
			}
		}

		$plugins = $out;
	}

	public static function splitMimeString($type)
	{
		//split $type into type and subtype
		$subtype = '*';

		$slashPos = strpos($type, '/');
		if($slashPos > 0)
		{
			# assume the presence of a slash indicates this is a type/subtype
			if(strlen($type) - 1 === $slashPos)
			{
				#..or maybe not. Just throw out the ending /
				$type = substr($type, 0, -1);
			} else {
				$subtype = substr($type, $slashPos + 1);
				$type = substr($type, 0, $slashPos);
			}
		}

		return array($type, $subtype);
	}

	private function getPluginsForType($mime)
	{
		$type = MimeMagic::splitMimeString($mime);

		if($type[1] != '*')
		{
			$plugins = $this->pluginsByType[ $type[0] ][ $type[1] ];
			$plugins = array_merge((array) $plugins, (array) $this->pluginsByType[ $type[0] ][ '*' ]);
		} else {
			/* no subtype specified...spit out all plugins from all subtypes.
			*  this code is currently unused (and untested), but included for 
			*  completeness.
			*/
			$plugins = array();
			foreach($this->pluginsByType[ $type[0] ] AS $subtype)
			{
				foreach($subtype AS $detectors)
				{
					foreach($detectors AS $detector)
					{
						$plugins[] = $detector;
					}
				}
			}
		}

		return $plugins;
	}


	/** Internal mime type detection, please use guessMimeType() for application 
	code instead.
	* Detection is done using an external program, if $wgMimeDetectorCommand is 
	set.
	* Otherwise, the fileinfo extension and mime_content_type are tried (in this 
	order), if they are available.
	* If the dections fails and $ext is not false, the mime type is guessed from 
	the file extension, using 
	* guessTypesForExtension.
	* If the mime type is still unknown, getimagesize is used to detect the mime 
	type if the file is an image.
	* If no mime type can be determined, this function returns 
	"unknown/unknown".
	*
	* @param string $file The file to check
	* @param mixed $ext The file extension, or true to extract it from the 
	filename. 
	*                   Set it to false to ignore the extension.
	*
	* @return string the mime type of $file
	* @access private
	*/
	function detectMimeType( $file, $ext = true ) {
		global $wgMimeDetectorCommand;

		$m = NULL;
		if ( $wgMimeDetectorCommand ) {
			$fn = wfEscapeShellArg( $file );
			$m = `$wgMimeDetectorCommand $fn`;
		} elseif ( function_exists( "finfo_open" ) && function_exists( 
		"finfo_file" ) ) {

			# This required the fileinfo extension by PECL,
			# see http://pecl.php.net/package/fileinfo
			# This must be compiled into PHP
			#
			# finfo is the official replacement for the deprecated
			# mime_content_type function, see below.
			#
			# If you may need to load the fileinfo extension at runtime, set
			# $wgLoadFileinfoExtension in LocalSettings.php

			$mime_magic_resource = finfo_open(FILEINFO_MIME); /* return mime 
			type ala mimetype extension */

			if ($mime_magic_resource) {
				$m = finfo_file( $mime_magic_resource, $file );
				finfo_close( $mime_magic_resource );
			} else {
				wfDebug( __METHOD__.": finfo_open failed on 
				".FILEINFO_MIME."!\n" );
			}
		} elseif ( function_exists( "mime_content_type" ) ) {

			# NOTE: this function is available since PHP 4.3.0, but only if
			# PHP was compiled with --with-mime-magic or, before 4.3.2, with --enable-mime-magic.
			#
			# On Windows, you must set mime_magic.magicfile in php.ini to point to the mime.magic file bundeled with PHP;
			# sometimes, this may even be needed under linus/unix.
			#
			# Also note that this has been DEPRECATED in favor of the fileinfo extension by PECL, see above.
			# see http://www.php.net/manual/en/ref.mime-magic.php for details.

			$m = mime_content_type($file);

			if ( $m == 'text/plain' ) {
				// mime_content_type sometimes considers DJVU files to be 
				text/plain.
				$deja = new DjVuImage( $file );
				if( $deja->isValid() ) {
					wfDebug( __METHOD__.": (re)detected $file as 
					image/vnd.djvu\n" );
					$m = 'image/vnd.djvu';
				}
			}
		} else {
			wfDebug( __METHOD__.": no magic mime detector found!\n" );
		}

		if ( $m ) {
			# normalize
			$m = preg_replace( '![;, ].*$!', '', $m ); #strip charset, etc
			$m = trim( $m );
			$m = strtolower( $m );

			if ( strpos( $m, 'unknown' ) !== false ) {
				$m = NULL;
			} else {
				wfDebug( __METHOD__.": magic mime type of $file: $m\n" );
				return $m;
			}
		}

		# if still not known, use getimagesize to find out the type of image
		# TODO: skip things that do not have a well-known image extension? Would that be safe?
		wfSuppressWarnings();
		$gis = getimagesize( $file );
		wfRestoreWarnings();

		$notAnImage = false;

		if ( $gis && is_array($gis) && $gis[2] ) {
			
			switch ( $gis[2] ) {
				case IMAGETYPE_GIF: $m = "image/gif"; break;
				case IMAGETYPE_JPEG: $m = "image/jpeg"; break;
				case IMAGETYPE_PNG: $m = "image/png"; break;
				case IMAGETYPE_SWF: $m = "application/x-shockwave-flash"; break;
				case IMAGETYPE_PSD: $m = "application/photoshop"; break;
				case IMAGETYPE_BMP: $m = "image/bmp"; break;
				case IMAGETYPE_TIFF_II: $m = "image/tiff"; break;
				case IMAGETYPE_TIFF_MM: $m = "image/tiff"; break;
				case IMAGETYPE_JPC: $m = "image"; break;
				case IMAGETYPE_JP2: $m = "image/jpeg2000"; break;
				case IMAGETYPE_JPX: $m = "image/jpeg2000"; break;
				case IMAGETYPE_JB2: $m = "image"; break;
				case IMAGETYPE_SWC: $m = "application/x-shockwave-flash"; break;
				case IMAGETYPE_IFF: $m = "image/vnd.xiff"; break;
				case IMAGETYPE_WBMP: $m = "image/vnd.wap.wbmp"; break;
				case IMAGETYPE_XBM: $m = "image/x-xbitmap"; break;
			}

			if ( $m ) {
				wfDebug( __METHOD__.": image mime type of $file: $m\n" );
				return $m;
			}
			else {
				$notAnImage = true;
			}
		} else {
			// Also test DjVu
			$deja = new DjVuImage( $file );
			if( $deja->isValid() ) {
				wfDebug( __METHOD__.": detected $file as image/vnd.djvu\n" );
				return 'image/vnd.djvu';
			}
		}

		# if desired, look at extension as a fallback.
		if ( $ext === true ) {
			$i = strrpos( $file, '.' );
			$ext = strtolower( $i ? substr( $file, $i + 1 ) : '' );
		}
		if ( $ext ) {
			$m = $this->guessTypesForExtension( $ext );

			# TODO: if $notAnImage is set, do not trust the file extension if
			# the results is one of the image types that should have been recognized
			# by getimagesize

			if ( $m ) {
				wfDebug( __METHOD__.": extension mime type of $file: $m\n" );
				return $m;
			}
		}

		#unknown type
		wfDebug( __METHOD__.": failed to guess mime type for $file!\n" );
		return "unknown/unknown";
	}

	/**
	* Determine the media type code for a file, using its mime type, name and 
	* possibly its contents.
	*
	* This function relies on the findMediaType(), mapping extensions and mime
	* types to media types.
	*
	* @todo look at multiple extension, separately and together.
	*
	* @param string $path full path to the image file, in case we have to look 
	* at the contents
	* (if null, only the mime type is used to determine the media type code).
	*
	* @param string $mime mime type. If null it will be guessed using 
	* guessMimeType.
	*
	* @return string a value to be used with the MEDIATYPE_xxx constants.
	*/
	function getMediaType( $path = NULL, $mime = NULL ) {
		if( !$mime && !$path ) return MEDIATYPE_UNKNOWN;

		# If mime type is unknown, guess it
		if( !$mime ) $mime = $this->guessMimeType( $path, false );
		if(strpos($mime, 'unknown/unknown') === 0) return MEDIATYPE_UNKNOWN;

		/* obsoleted by plugins
		# Special code for ogg - detect if it's video (theora),
		# else label it as sound.
		if( $mime == "application/ogg" && file_exists( $path ) ) {

			// Read a chunk of the file
			$f = fopen( $path, "rt" );
			if ( !$f ) return MEDIATYPE_UNKNOWN;
			$head = fread( $f, 256 );
			fclose( $f );

			$head = strtolower( $head );

			# This is an UGLY HACK, file should be parsed correctly
			if ( strpos( $head, 'theora' ) !== false ) return MEDIATYPE_VIDEO;
			elseif ( strpos( $head, 'vorbis' ) !== false ) return 
			MEDIATYPE_AUDIO;
			elseif ( strpos( $head, 'flac' ) !== false ) return MEDIATYPE_AUDIO;
			elseif ( strpos( $head, 'speex' ) !== false ) return 
			MEDIATYPE_AUDIO;
			else return MEDIATYPE_MULTIMEDIA;
		}
		*/

		$type = MEDIATYPE_UNKNOWN;
		# check for entry for full mime type
		$type = $this->findMediaType( $mime );
		if($type == MEDIATYPE_UNKNOWN)
		{
			# Check for entry for file extension
			$e = NULL;
			if ( $path ) {
				$i = strrpos( $path, '.' );
				$e = strtolower( $i ? substr( $path, $i + 1 ) : '' );

				# TODO: look at multi-extension if this fails, parse from full path

				$type = $this->findMediaType( '.' . $e );
			}
		} else if($type == MEDIATYPE_UNKNOWN)
		{
			# Check major mime type
			$i = strpos( $mime, '/' );
			if( $i !== false ) {
				$major = substr( $mime, 0, $i );
				$type = $this->findMediaType( $major );
			}
		}

		if( $type == MEDIATYPE_MULTIMEDIA && file_exists($path))
		{
			/* check if there's a registered plugin that wants to make the 
			*  determination by examination of contents
			*/
			$detectors = $this->getPluginsForType($mime);
			foreach($detectors AS $detector)
			{
				if($detector[1]) //assume non-authoritative plugins won't know
				{
					$t = $detector[0]->getMediaType($path, $mime);
					if($t) return $t;
				}
			}
		}

		return $type;
	}

	/** returns a media code matching the given mime type or file extension.
	* File extensions are represented by a string starting with a dot (.) to
	* distinguish them from mime types.
	*
	* @todo make this function match "major" mime types, getMediaType seems to 
	* think it can use it in this capacity.
	* This funktion relies on the mapping defined by $this->mMediaTypes
	* @access private
	*/
	function findMediaType( $extMime ) {
		if ( strpos( $extMime, '.' ) === 0 ) { #if it's an extension, look up the mime types
			$m = $this->getTypesForExtension( substr( $extMime, 1 ) );
			if ( !$m ) return MEDIATYPE_UNKNOWN;

			$m = explode( ' ', $m );
		} else { 
			# Normalize mime type
			if ( isset( $this->mMimeTypeAliases[$extMime] ) ) {
				$extMime = $this->mMimeTypeAliases[$extMime];
			}

			$m = array($extMime);
		}

		foreach ( $m as $mime ) {
			$mime = trim($mime); if(empty($mime)) continue;
			foreach ( $this->mMediaTypes as $type => $codes ) {
				if ( in_array($mime, $codes, true ) ) {
					return $type;
				}
			}
		}

		return MEDIATYPE_UNKNOWN;
	}
}

?>
