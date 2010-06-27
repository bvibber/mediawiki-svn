<?php
/**
 * This extension creates a special page which lets users find their edit counts.
 * 
 * @package MediaWiki
 * @subpackage EditCount
 * @author Fahad Sadah
 * @copyright 2009 Fahad Sadah and Benjamin Peterson
 * @license GPL http://www.gnu.org/copyleft/gpl.html
 */

if (!defined("MEDIAWIKI")) {
	die("This is not valid entry point");
}

/*************************************
 Configuartion
 ***************************************/

/**
 * True to turn on parser function and false to not
 */
$egECParserFunction = true;

/**
 * An array of the names of the parser functions
 * 
 * This array of parser function names must be single words (can have - and _). They are not case sensitive. These will have "#" appended to the front of them in wikimarkup.
 * @var array
 */
$egECParserFunctionNames = array("editcount", "ec");

/**
 * True to enable the Special:EditCount page
 */
$egECEnableSpecialPage = true;

/*************************************
 End Config
 ***************************************/

$egEditCountCredits = array(
	"name" => "Edit Count",
	"author" => "Fahad Sadah",
	"description" => "Gets the edit count of a user",
	"url" => "http://www.mediawiki.org/wiki/Extension:EditCount"
);
$wgExtensionCredits["parserhook"][] = $egEditCountCredits;
$wgExtensionCredits["specialpage"][] = $egEditCountCredits;

$wgExtensionFunctions[] = "efEditCount";
if ($egECParserFunction) {
	$wgHooks["LanguageGetMagic"][] = "efEditCountMagic";
}

if ($egECEnableSpecialPage) {
	$wgHooks["SkinTemplateBuildNavUrlsNav_urlsAfterPermalink"][] = "efEditCountNavUrls";
	$wgHooks["MonoBookTemplateToolboxEnd"][] = "efEditCountToolbox";
}

/**
 * The extension function that's called to set up EditCount.
 */
function efEditCount() {
	global $wgAutoloadClasses, $wgSpecialPages, $wgParser,
		$egECParserFunction, $egECEnableSpecialPage, $egECParserFunctionNames, $wgVersion;
    
    //for cross version compatibility
    $before17 = version_compare($wgVersion, "1.7", "<");
    
    //Autoload
	$wgAutoloadClasses["EditCountPage"] = dirname(__FILE__) . "/EditCountPage.php";
	$wgAutoloadClasses["EditCount"] = dirname(__FILE__) . "/EditCountPage.php";
	if ($before17) {
		//autoloading not supported
		require_once "EditCountPage.php";
	}
    
	if ($egECEnableSpecialPage) {
    	//add to special page (object if less than 1.7)
    	if ($before17) {
        	$wgSpecialPages["EditCount"] = new EditCountPage;
    	}
    	else {
        	$wgSpecialPages["EditCount"] = "EditCountPage";
    	}
	}
	
	if ($egECParserFunction) {
        if ($before17) {
        	//have to do this without magic words
        	foreach ($egECParserFunctionNames as $funcName) {
        		$wgParser->setFunctionHook("#" . $funcName, "efEditCountParserFunction");
        	}
        }
        else {
        	$wgParser->setFunctionHook("editcount", "efEditCountParserFunction");
        }
	}
	
	efEditCountMsgs();
}

/**
 * Sets up the parser function magic words in Mediawiki 1.7 and greater.
 * 
 * @param array $magicWords the array of magic word we'll add to
 * @return bool always true
 */
function efEditCountMagic(&$magicWords) {
	global $egECParserFunctionNames;
	
	if (!is_array($egECParserFunctionNames) || count($egECParserFunctionNames) == 0) {
		$egECParserFunctionNames = array("editcount", "ec");
	}
	
	$magicWords["editcount"] = array_merge(array(0), $egECParserFunctionNames);
	return true;
}

/**
 * Injects EditCount's messages into the message system
 */
function efEditCountMsgs() {
	global $wgMessageCache, $wgContLang, $wgVersion;
	static $msgsLoaded = false;
    
    wfProfileIn(__FUNCTION__);
	
	$before17 = version_compare($wgVersion, "1.7", "<");
	
	if (!$msgsLoaded) {
		$weECMessages = array();
		require_once "EditCount.i18n.php";
		if ($before17) {
			//1.6 doesn't support multiple languages
        	$code = $wgContLang->getCode();
        	if (!array_key_exists($code, $weECMessages)) {
            	$code = "en";
        	}
			$wgMessageCache->addMessages($weECMessages[$code], $code);
		}
		else {
			//add all the message to fill in language gaps
			foreach ($weECMessages as $code => $msgs) {
				$wgMessageCache->addMessages($weECMessages[$code], $code);
			}
		}
		$msgsLoaded = true;
	}
    
    wfProfileOut(__FUNCTION__);
}

/**
 * Adds the path of the EditCount special page to toolboxes on user pages
 * 
 * @param SkinTemplate $skinTemplate
 * @param array $navUrls the navagation urls
 * @param int $oldid the oldid of the article
 * @param int $revisionid the revision id
 * @return bool always true
 */
function efEditCountNavUrls(&$skinTemplate, &$navUrls, $oldid, $revisionid) {
	global $wgAutoloadClasses, $egECEnableSpecialPage;
	
	if (!$egECEnableSpecialPage) {
		return;
	}
	
	$title = $skinTemplate->mTitle;
	if ($title->getNamespace() == NS_USER && $revisionid !== 0) {
		$navUrls["editcount"] = array(
			"text" => wfMsg("editcount-toolbox"), 
			"href" => $skinTemplate->makeSpecialUrl("EditCount", "target=" . wfUrlencode($title->getText())));
	}
	return true;
}

/**
 * Preforms the parser function action (getting the edit count of a user)
 * 
 * @param Parser $parser the parser instance
 * @param string $param1 the name of the user in question (hopefully)
 * @param string $param2 (optional) namespace
 * @return mixed
 */
function efEditCountParserFunction($parser, $param1 = "", $param2 = "") {
	global $wgContLang;
    
    wfProfileIn(__FUNCTION__);
	
	if ($param1 == "" || !Title::newFromText($param1)) {
        wfProfileOut(__FUNCTION__);
		return array("found" => false);
	}
		
	$ec = new EditCount($param1);
	
	if ($param2 === "") {
        wfProfileOut(__FUNCTION__);
		return $ec->getTotal();
	}
	
	if (!is_numeric($param2)) {
		$index = Namespace::getCanonicalIndex(strtolower($param2));
		if ($index === null) {
            wfProfileOut(__FUNCTION__);
			return array("found" => false);
		}
	}
	else {
		$namespaces = $wgContLang->getNamespaces();
		if (!array_key_exists($param2, $namespaces)) {
            wfProfileOut(__FUNCTION__);
			return array("found" => false);
		}
		$index = $param2;
	}
    
    wfProfileOut(__FUNCTION__);
	return $ec->getByNamespace($index);
}

/**
 * Actually adds the HTML
 * 
 * @param SkinMonoBook $monobook the template we're in
 * @return bool always true
 */
function efEditcountToolbox(&$monobook) {
	if (array_key_exists("editcount", $monobook->data["nav_urls"])) {
		?><li id="t-editcount">
			<a href="<?php echo htmlspecialchars($monobook->data["nav_urls"]["editcount"]["href"]) ?>"><?php
			echo $monobook->msg("editcount-toolbox"); ?></a>
		</li>
		<?php
	}
	return true;
}

?>