<?php
/** Comments should be inserted here ;) */

abstract class MimePlugin
{
	/** some convenience named constants
	*/
	const EXAMINE_UNKNOWN = true;
	const IGNORE_UNKNOWN = false;

	private $unknownFileSafe;

	/**
	* The MimeMagic instance. Plugins need to reference MimeMagic during their 
	* construction, and because this occurs *within MimeMagic's own 
	* constructor*, using MimeMagic::singleton() would just create a new 
	* MimeMagic instance (and create an endless loop.) 
	*/
	protected $MagicCore;

	protected function __construct(MimeMagic $core, $arbitraryFileSafe = MimePlugin::EXAMINE_UNKNOWN)
	{
		$this->MagicCore = $core;
		$this->MagicCore->addMimeTypes($this->mimeTypes());
		$this->MagicCore->addMimeInfo($this->mimeInfo());

		if($arbitraryFileSafe)
		{
			$this->registerContentType('unknown/unknown');
		}
	}

	/**
	* Register this plugin for a MIME content-type. 
	* This should be called as many times as necessary in your child class 
	* constructor.
	*
	* You can register for a precise content-type (ex. audio/mpeg), or 
	* for all media in a main media-type group (ex. video). See
	* http://www.iana.org/assignments/media-types/ for the possibilities.
	*
	* MimeMagic uses this information to decide what plugins to seek help from. 
	* There are two reasons for you to register a given content type X:
	* 1) This plugin targets content type X. In general, 
	*    $authoritativeRejector should be true for any such types. If you 
	*    register as authoritative for an entire media-type group, be sure 
	*    your plugin really can identify every subtype (that your wiki cares 
	*    about, anyway.)
	* 2) If MimeMagic is known to misidentify the plugin's target type as X. For 
	*    example, .asf files are usually guessed as application/octet-stream. 
	*    Thus, if your plugin targets .asf files, you should also register it 
	*    for application/octet-stream non-authoritatively. (Actually, 
	*    application/octet stream is aliased to unknown/unknown by default and 
	*    so EXAMINE_UNKNOWN plugins will get used anyway, but the example 
	*    still serves to illustrate.)
	*    There is no shortcut to register your plugin for all content types, and
	*    please don't do it. This defeats the purpose of a plugin, and
	*    really attempts to replace Magic Mime checking by thoroughly processing 
	*    all files as all file types, a generally silly idea.
	*
	* @param $type string A content type or type/subtype pair.
	* @param $authoritativeRejector boolean Whether this plugin's inability to 
	*        identify a particular file as $type really means it is not $type.
	*/
	protected final function registerContentType($type, 
		$authoritativeRejector = false)
	{
		$type = MimeMagic::splitMimeString($type);

		$this->MagicCore->registerPluginByContentType($this, $type[0], $type[1], $authoritativeRejector);
	}

	/**
	* Attempt to identify the MIME content-type of $file.
	*
	* MimeMagic will call this in the event that
	* A) It has no clue about $file and this plugin was constructed with 
	*    MimePlugin::EXAMINE_UNKNOWN.
	* B) It guessed $file to be a type that this plugin registered itself for.
	*
	* The rationale behind case B) is to correct wrong guesses by MimeMagic. 
	* This can happen in two ways. MimeMagic can:
	* 1) Misidentify a file as of a type this plugin specializes in (and for which this plugin registered itself 
	*    authoritative), when the file is in fact something else.  This plugin probably cannot determine what the file 
	*    really is, but knows (by attempting to actually render as an image, 
	*    say) that it is not what MimeMagic guessed. In this case, the return 
	*    value should be 'unknown/unknown', which will override the Magic guess 
	*    (and give any other EXAMINE_UNKNOWN plugins a crack at it)
	* 2) Misidentify a file as of a type this plugin does not specialize in, when the file is in fact of a type this plugin can identify. This means that as part 
	*    of creating your plugin, you should determine if MimeMagic is known to 
	*    misidentify your target media as something else, and register the 
	*    plugin non-authoritatively for those type(s) as necessary. When registered non-authoritatively, returning unknown/unknown will not override actual identifications from MimeMagic.
	* In all cases, if this method returns any valid mime content-type string 
	* other than unknown/unknown, all further processing stops and that string 
	* will be the final type reported by MimeMagic.
	*/
	public abstract function getContentType($file);

	/**
	* @return string typesString The mime types and extensions supported by this 
	*  plugin, in the format of a mime.types file. (See MM_WELL_KNOWN_MIME_TYPES 
	*  in MimeMagic.php for an example.)
	*/
	protected abstract function mimeTypes();

	/**
	* @return string infoString The mime types supported by this plugin and 
	*  their associated media types, in the format of a mime.info file. (See 
	*  MM_WELL_KNOWN_MIME_INFO for an example.)
	*/
	protected abstract function mimeInfo();

	/**
	* Possibly classify $file as one of the MEDIATYPE constants.
	*
	* Usually there is no need for a plugin to explicitly declare this;
	* for most mime types MimeMagic can already figure it out. However, a few 
	* types (the classic example being application/ogg) do not in themselves 
	* necessitate one particular MEDIATYPE. If your plugin will be registered 
	* for such a type and is capable of parsing the file's contents to make this
	* distinction, please override this method and do so.
	*
	* Because the assumption is that plugins are smarter than MimeMagic for the 
	* specialized types they were written for, this method will get called for 
	* *every* content-type the plugin is registered for, whether MimeMagic 
	* thinks it could classify it or not. To delegate this responsibility back 
	* to MimeMagic, just return false if $mime is not of a type that needs 
	* special parsing of the file's contents.
	*
	* @param string $file the file to classify
	* @param string $mime MimeMagic's guess for $file (including help from 
	*                     plugins)
	* @return mixed a MEDIATYPE constant, or false to let MimeMagic guess.
	*/
	public function getMediaType($file, $mime)
	{
		return false;
	}
}
?>