<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/SacredText/SacredText.setup.php" );
EOT;
	exit( 1 );
}

// the following are the parameters that can be set in LocalSettings.php
global $wgSacredChapterAlias;
global $wgSacredUseBibleTag;
$wgSacredUseBibleTag = true;

$wgSacredChapterAlias = array();
$wgSacredChapterAlias["Christian Bible"] = array();
$wgSacredChapterAlias["Christian Bible"]["1Chronicles"]="1 Chronicles";
$wgSacredChapterAlias["Christian Bible"]["1Corinthians"]="1 Corinthians";
$wgSacredChapterAlias["Christian Bible"]["1John"]="1 John";
$wgSacredChapterAlias["Christian Bible"]["1Kings"]="1 Kings";
$wgSacredChapterAlias["Christian Bible"]["1Peter"]="1 Peter";
$wgSacredChapterAlias["Christian Bible"]["1Samuel"]="1 Samuel";
$wgSacredChapterAlias["Christian Bible"]["1Thessalonians"]="1 Thessalonians";
$wgSacredChapterAlias["Christian Bible"]["1Timothy"]="1 Timothy";
$wgSacredChapterAlias["Christian Bible"]["2Chronicles"]="2 Chronicles";
$wgSacredChapterAlias["Christian Bible"]["2Corinthians"]="2 Corinthians";
$wgSacredChapterAlias["Christian Bible"]["2John"]="2 John";
$wgSacredChapterAlias["Christian Bible"]["2Kings"]="2 Kings";
$wgSacredChapterAlias["Christian Bible"]["2Peter"]="2 Peter";
$wgSacredChapterAlias["Christian Bible"]["2Samuel"]="2 Samuel";
$wgSacredChapterAlias["Christian Bible"]["2Thessalonians"]="2 Thessalonians";
$wgSacredChapterAlias["Christian Bible"]["2Timothy"]="2 Timothy";
$wgSacredChapterAlias["Christian Bible"]["3John"]="3 John";
$wgSacredChapterAlias["Christian Bible"]["1Ch"]="1 Chronicles";
$wgSacredChapterAlias["Christian Bible"]["1Co"]="1 Corinthians";
$wgSacredChapterAlias["Christian Bible"]["1Jo"]="1 John";
$wgSacredChapterAlias["Christian Bible"]["1Ki"]="1 Kings";
$wgSacredChapterAlias["Christian Bible"]["1Pe"]="1 Peter";
$wgSacredChapterAlias["Christian Bible"]["1Sa"]="1 Samuel";
$wgSacredChapterAlias["Christian Bible"]["1Th"]="1 Thessalonians";
$wgSacredChapterAlias["Christian Bible"]["1Ti"]="1 Timothy";
$wgSacredChapterAlias["Christian Bible"]["2Ch"]="2 Chronicles";
$wgSacredChapterAlias["Christian Bible"]["2Co"]="2 Corinthians";
$wgSacredChapterAlias["Christian Bible"]["2Jo"]="2 John";
$wgSacredChapterAlias["Christian Bible"]["2Ki"]="2 Kings";
$wgSacredChapterAlias["Christian Bible"]["2Pe"]="2 Peter";
$wgSacredChapterAlias["Christian Bible"]["2Sa"]="2 Samuel";
$wgSacredChapterAlias["Christian Bible"]["2Th"]="2 Thessalonians";
$wgSacredChapterAlias["Christian Bible"]["2Ti"]="2 Timothy";
$wgSacredChapterAlias["Christian Bible"]["3Jo"]="3 John";
$wgSacredChapterAlias["Christian Bible"]["Act"]="Acts";
$wgSacredChapterAlias["Christian Bible"]["Amo"]="Amos";
$wgSacredChapterAlias["Christian Bible"]["Col"]="Colossians";
$wgSacredChapterAlias["Christian Bible"]["Dan"]="Daniel";
$wgSacredChapterAlias["Christian Bible"]["Deu"]="Deuteronomy";
$wgSacredChapterAlias["Christian Bible"]["Ecc"]="Ecclesiastes";
$wgSacredChapterAlias["Christian Bible"]["Eph"]="Ephesians";
$wgSacredChapterAlias["Christian Bible"]["Est"]="Esther";
$wgSacredChapterAlias["Christian Bible"]["Exo"]="Exodus";
$wgSacredChapterAlias["Christian Bible"]["Eze"]="Ezekial";
$wgSacredChapterAlias["Christian Bible"]["Ezr"]="Ezra";
$wgSacredChapterAlias["Christian Bible"]["Gal"]="Galatians";
$wgSacredChapterAlias["Christian Bible"]["Gen"]="Genesis";
$wgSacredChapterAlias["Christian Bible"]["Ge"]="Genesis";
$wgSacredChapterAlias["Christian Bible"]["Hab"]="Habakkuk";
$wgSacredChapterAlias["Christian Bible"]["Hag"]="Haggai";
$wgSacredChapterAlias["Christian Bible"]["Heb"]="Hebrews";
$wgSacredChapterAlias["Christian Bible"]["Hos"]="Hosea";
$wgSacredChapterAlias["Christian Bible"]["Isa"]="Isaiah";
$wgSacredChapterAlias["Christian Bible"]["Jam"]="James";
$wgSacredChapterAlias["Christian Bible"]["Jer"]="Jeremiah";
$wgSacredChapterAlias["Christian Bible"]["Job"]="Job";
$wgSacredChapterAlias["Christian Bible"]["Joe"]="Joel";
$wgSacredChapterAlias["Christian Bible"]["Joh"]="John";
$wgSacredChapterAlias["Christian Bible"]["Jon"]="Jonah";
$wgSacredChapterAlias["Christian Bible"]["Jos"]="Joshua";
$wgSacredChapterAlias["Christian Bible"]["Jud"]="Jude";
$wgSacredChapterAlias["Christian Bible"]["Jud"]="Judges";
$wgSacredChapterAlias["Christian Bible"]["Lam"]="Lamentations";
$wgSacredChapterAlias["Christian Bible"]["Lev"]="Leviticus";
$wgSacredChapterAlias["Christian Bible"]["Luk"]="Luke";
$wgSacredChapterAlias["Christian Bible"]["Mal"]="Malachi";
$wgSacredChapterAlias["Christian Bible"]["Mar"]="Mark";
$wgSacredChapterAlias["Christian Bible"]["Mat"]="Matthew";
$wgSacredChapterAlias["Christian Bible"]["Mic"]="Micah";
$wgSacredChapterAlias["Christian Bible"]["Nah"]="Nahum";
$wgSacredChapterAlias["Christian Bible"]["Neh"]="Nehemiah";
$wgSacredChapterAlias["Christian Bible"]["Num"]="Numbers";
$wgSacredChapterAlias["Christian Bible"]["Oba"]="Obad";
$wgSacredChapterAlias["Christian Bible"]["Phi"]="Philemon";
$wgSacredChapterAlias["Christian Bible"]["Phi"]="Philippians";
$wgSacredChapterAlias["Christian Bible"]["Pro"]="Proverbs";
$wgSacredChapterAlias["Christian Bible"]["Psa"]="Psalms";
$wgSacredChapterAlias["Christian Bible"]["Rev"]="Revelation";
$wgSacredChapterAlias["Christian Bible"]["Rom"]="Romans";
$wgSacredChapterAlias["Christian Bible"]["Rut"]="Ruth";
$wgSacredChapterAlias["Christian Bible"]["Son"]="Song of Solomon";
$wgSacredChapterAlias["Christian Bible"]["Song"]="Song of Solomon";
$wgSacredChapterAlias["Christian Bible"]["SSol"]="Song of Solomon";
$wgSacredChapterAlias["Christian Bible"]["Tit"]="Titus";
$wgSacredChapterAlias["Christian Bible"]["Zec"]="Zechariah";
$wgSacredChapterAlias["Christian Bible"]["Zep"]="Zephaniah";


// based on http://www.mediawiki.org/wiki/Manual:Tag_extensions
if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	$wgHooks['ParserFirstCallInit'][] = 'efSacredTextParserInit';
} else { // Otherwise do things the old fashioned way
	$wgExtensionFunctions[] = 'efSacredTextParserInit';
}
 
function efSacredTextParserInit() {
	global $wgParser;
	global $wgSacredUseBibleTag;
	$wgParser->setHook( 'sacredtext', 'efSacredTextRenderHook' );
	if( $wgSacredUseBibleTag ) {
		$wgParser->setHook( 'bible', 'efSacredTextRenderHook_Bible' );
	}
	return true;
}

function efSacredTextRender( $religtext, $book, $chapternum, $versenums, $lang, $ver )
{
	global $wgSacredChapterAlias;
	$dbr = wfGetDB( DB_SLAVE );

	if( array_key_exists($religtext, $wgSacredChapterAlias) &&
		array_key_exists($book, $wgSacredChapterAlias[$religtext] ) ) 
	{
		$book = $wgSacredChapterAlias[$religtext][$book];
	}

	if( strcasecmp($religtext,"Christian Bible")==0 ) {
		if( strcasecmp($ver,"AV")==0 ) {
			$ver = "KJV";
		}
	}

	$obj = $dbr->selectRow( "sacredtext_verses", array("st_text"),
		array( "st_religious_text"=>$religtext,
				"st_book"=>$book,
				"st_chapter_num"=>$chapternum,
				"st_verse_num"=>$versenums[0],
				"st_translation"=>$ver,
		"st_language"=>$lang));
	if( $obj )
	{
		return htmlspecialchars( $obj->st_text );
	}
	else
	{
		return htmlspecialchars( "Could not find: ". $book ." ".$chapternum.":".$versenums[0]." in the ". $religtext );
	}
}
function efSacredTextParseInput( $input, &$book, &$chapternum, &$versenums )
{
	if( preg_match( "/^\s*([\s\w]*\w+)\s*(\d+):(\d+)/", $input, $matches) )
	{
		$book = $matches[1];
		$chapternum = $matches[2];

	$versenums = array();
		$versenums[] = $matches[3];
	return true;
	}
	else
	{
		return false;
	}
}
function efSacredTextRenderHook_Bible( $input, $args, &$parser, $frame )
{
	$parser->disableCache();
	if( efSacredTextParseInput( $input, $book, $chapternum, $versenums ) ) 
	{
	$lang = "en";
	$ver = "kjv";
	if( array_key_exists("lang", $args) ) $lang = $args["lang"];
	if( array_key_exists("ver", $args) ) $ver = $args["ver"];

		return htmlspecialchars( $input ) ." ". efSacredTextRender( "Christian Bible", $book, $chapternum, $versenums, $lang, $ver );
	}
	else
	{
		return htmlspecialchars( $input . " Could not parse reference.  Please use the format 'Gen 1:10'." );
	}
} 
 
function efSacredTextRenderHook( $input, $args, &$parser, $frame ) {
	$parser->disableCache();
	if( efSacredTextParseInput( $input, $book, $chapternum, $versenums ) )
	{
		$lang = "en";
		$ver = "kjv";
		$religtext = "Christian Bible";
		if( array_key_exists("lang", $args) ) $lang = $args["lang"];
		if( array_key_exists("ver", $args) ) $ver = $args["ver"];
		if( array_key_exists("text", $args) ) $religtext = $args["text"];

		return htmlspecialchars( $input ) ." ". efSacredTextRender( $religtext, $book, $chapternum, $versenums, $lang, $ver );
	}
	else
	{
		return htmlspecialchars( $input . " Could not parse reference.  Please use the format 'Gen 1:10'." );
	}
}


# Schema updates for update.php
$wgHooks['LoadExtensionSchemaUpdates'][] = 'updateSacredTextDB';

function updateSacredTextDB() {
	global $wgExtNewTables;
	$wgExtNewTables[] = array(
		'sacredtext_verses',
		dirname( __FILE__ ) . '/SacredText.verses.sql' );
	$wgExtNewTables[] = array(
		'sacredtext_verses_kjv_entire',
		dirname( __FILE__ ) . '/data/bible_kjv_entire.sql' );
	return true;
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'SacredText',
	'author' => 'Jonathan Williford',
	'description' => 'Makes it easy to quote religious scriptures.',
	'descriptionmsg' => 'This extension helps to quote religious scriptures.',
	'version' => '0.0.1',
);

?>
