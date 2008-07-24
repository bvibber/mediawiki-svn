<?php
/**
 * Simple Security extension
 * - Extends the MediaWiki article protection to allow restricting viewing of article content
 * - Also adds #ifusercan and #ifgroup parser functions for rendering restriction-based content
 *
 * See http://www.mediawiki.org/Extension:Simple_Security for installation and usage details
 * See http://www.organicdesign.co.nz/Extension_talk:SimpleSecurity4.php for development notes and disucssion
 * Version 4.0.0 started 2007-10-11
 * Version 4.1.0 started 2008-06-12 (development funded for a slimmed down functional version)
 * 
 * @package MediaWiki
 * @subpackage Extensions
 * @author Aran Dunkley [http://www.organicdesign.co.nz/nad User:Nad]
 * @copyright © 2007 Aran Dunkley
 * @licence GNU General Public Licence 2.0 or later
 */

if (!defined('MEDIAWIKI')) die('Not an entry point.');

define('SIMPLESECURITY_VERSION', '4.1.2, 2008-07-23');

# Global security settings
$wgSecurityMagicIf              = "ifusercan";                  # the name for doing a permission-based conditional
$wgSecurityMagicGroup           = "ifgroup";                    # the name for doing a group-based conditional
$wgSecurityLogActions           = array('edit', 'download');    # Actions that should be logged
$wgSecurityUseDBHook            = true;                         # Use the DatabaseFetchHook to validate database access
$wgSecurityAllowUser            = false;                        # Allow restrictions based on user not just group
$wgSecurityAllowUnreadableLinks = false;                        # Should links to unreadable pages be allowed? (MW1.7+)

# Extra actions to allow control over in protection form
$wgSecurityExtraActions  = array(
	'read'    => 'Read',
	'source'  => 'Source',
	'history' => 'History'
);
$wgSecurityExtraActions  = array('read' => 'Read');

# Extra groups available in protection form
$wgSecurityExtraGroups   = array();

array_unshift($wgExtensionFunctions, 'wfSetupSimpleSecurity'); # Put SimpleSecurity's setup function before all others

$wgHooks['LanguageGetMagic'][] = 'wfSimpleSecurityLanguageGetMagic';
$wgExtensionCredits['parserhook'][] = array(
	'name'        => "SimpleSecurity",
	'author'      => '[http://www.organicdesign.co.nz/User:Nad User:Nad]',
	'description' => 'Extends the MediaWiki article protection to allow restricting viewing of article content',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:Simple_Security',
	'version'     => SIMPLESECURITY_VERSION
	);

class SimpleSecurity {

	var $guid  = '';
	var $cache = array();

	/**
	 * Constructor
	 */
	function __construct() {
		global $wgParser, $wgHooks, $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions, $wgMessageCache,
			$wgSecurityMagicIf, $wgSecurityMagicGroup, $wgSecurityExtraActions, $wgSecurityExtraGroups,
			$wgRestrictionTypes, $wgRestrictionLevels, $wgGroupPermissions, $wgSecurityAllowUnreadableLinks;

		# $wgGroupPermissions has to have its default read entry removed because Title::userCanRead checks it directly
		if ($this->default_read = isset($wgGroupPermissions['*']['read']) && $wgGroupPermissions['*']['read'])
			$wgGroupPermissions['*']['read'] = false;

		# Add our parser-hooks
		$wgParser->setFunctionHook($wgSecurityMagicIf, array($this, 'ifUserCan'));
		$wgParser->setFunctionHook($wgSecurityMagicGroup, array($this, 'ifGroup'));
		$wgHooks['UserGetRights'][] = $this;

		# If preventing links to unreadable content, add hook and a unique string
		if (!$wgSecurityAllowUnreadableLinks) {
			$wgHooks['GetLocalURL'][] = $this;
			$this->guid = uniqid('ss4-');
		}

		# Add a new log type
		$wgLogTypes[]                  = 'security';
		$wgLogNames  ['security']      = 'securitylogpage';
		$wgLogHeaders['security']      = 'securitylogpagetext';
		$wgLogActions['security/deny'] = 'securitylogentry';

		# Extend protection form groups, actions and messages
		$wgMessageCache->addMessages(array('protect-unchain' => "Modify actions individually"));
		#$wgMessageCache->addMessages(array('loginreqpagetext' => "Sorry, you'll need to $1 to an account with 
		#sufficient permissions to view this page."));
		foreach ($wgSecurityExtraActions as $k => $v) {
			if (empty($v)) $v = ucfirst($k);
			$wgRestrictionTypes[] = $k;
			$wgMessageCache->addMessages(array( "restriction-$k" => $v ));
			#$wgGroupPermissions['sysop'][$k] = true; # Ensure sysops have the right to perform this extra action
		}
		
		foreach ($wgSecurityExtraGroups as $k => $v) {
			if (empty($v)) $v = ucfirst($k);
			$wgRestrictionLevels[] = $k;
			$wgMessageCache->addMessages(array( "protect-level-$k" => $v ));
			$wgGroupPermissions[$k]['not an action'] = true; # Ensure the new groups show up in rights management
		}
	}

	/**
	 * Process the ifUserCan conditional security directive
	 */
	public function ifUserCan(&$parser, $action, $title, $then, $else = '') {
		return $title->userCan($action) ? $then : $else;
	}

	/**
	 * Process the ifGroup conditional security directive
	 * - evaluates to true if current uset belongs to any of the comma-separated users and/or groups in the first parameter
	 */
	public function ifGroup(&$parser, $groups, $then, $else = '') {
		global $wgUser;
		$intersection = array_intersect(array_map('strtolower', split(',', $groups)), $wgUser->getEffectiveGroups());
		return count($intersection) > 0 ? $then : $else;
	}

	/**
	 * Make links to unreadable pages into a guid for an url so they can be converted to not a link later
	 */
	public function onGetLocalURL(&$title, &$url, &$query) {
		global $wgUser, $wgHooks;
		if (!$this->validateTitle($wgUser, $title, $error)) {
			$url = $this->guid;
			static $hookAdded = 0;
			if ($hookAdded++ == 0) $wgHooks['BeforePageDisplay'][] = $this;
		}
		return true;
	}

	/**
	 * Convert the urls with guids for hrefs into non-clickable text of class "unreadable"
	 */
	public function onBeforePageDisplay(&$out) {
		$out->mBodytext = preg_replace(
			"|<a href=\"{$this->guid}\".+?>(.+?)</a>|",
			"<span class=\"unreadable\">$1</span>",
			$out->mBodytext
		);
		return true;
	}

	/**
	 * User::getRights returns a list of rights (allowed actions) based on the current users group membership
	 * Title::getRestrictions returns a list of groups who can perform a particular action
	 * So getRights should filter out any title-based restriction's actions which require groups that the user is not a member of
	 * Allows sysop access
	 */
	public function onUserGetRights(&$user, &$rights, $title = NULL) {
		global $wgGroupPermissions, $wgTitle, $wgRequest;

		if (!is_object($title)) $title = $wgTitle;
		$groups = $user->getEffectiveGroups();

		# If no title, or user is sysop, bail out now
		if (!is_object($title) || in_array('sysop', $groups)) return true;

		# Hack to prevent specialpage operations on unreadable pages
		$ns = $title->getNamespace();
		if ($ns == NS_SPECIAL) {
			list($name, $par) = explode('/', $title->getDBkey(), 2);
			if ($par) $title  = Title::newFromText($par);
			elseif ($wgRequest->getVal('target'))   $title = Title::newFromText($wgRequest->getVal('target'));
			elseif ($wgRequest->getVal('oldtitle')) $title = Title::newFromText($wgRequest->getVal('oldtitle'));
		}

		# If title is not readable by user, remove the read and move rights
		if (!$this->validateTitle($user, $title, $error))
			foreach ($rights as $i => $right) if ($right == 'read' || $right == 'move') unset($rights[$i]);

		# See constructor for details of this
		elseif ($this->default_read) $wgGroupPermissions['*']['read'] = $this->default_read;

		return true;
	}
		
	/**
	 * Patches SQL queries to ensure that the old_id field is present in all requests for the old_text field
	 * otherwise the title that the old_text is associated with can't be determined
	 */
	static function patchSQL($match) {
		if (!preg_match("/old_text/", $match[0])) return $match[0];
		$fields = str_replace(" ", "", $match[0]);
		return ($fields == "*" || preg_match("/old_id/", $fields)) ? $fields : "$fields,old_id";
	}

	/**
	 * Validate the passed database row and replace any invalid content
	 * - called from DatabaseFetchHook whenever a row contains old_text
	 * - old_id is guaranteed to exist due to patchSQL method
	 */
	static function validateRow(&$row) {
		global $wgUser, $wgSimpleSecurity;
		$groups = $wgUser->getEffectiveGroups();
		if (in_array('sysop', $groups)) return;

		# Obtain a title object from the old_id
		$dbr   =& wfGetDB(DB_SLAVE);
		$tbl   = $dbr->tableName('revision');
		$rev   = $dbr->selectRow($tbl, 'rev_page', "rev_text_id = {$row->old_id}", __METHOD__);
		$title = Title::newFromID($rev->rev_page);

		# Replace text content in the passed database row if title unreadable by user
		if (!$wgSimpleSecurity->validateTitle($wgUser, $title, $error)) $row->old_text = $error;
	}

	/**
	 * Return bool for whether or not a title can be read by user
	 * - if there are read restrictions in place for the title, check if user a member of any groups required for read access
	 */
	public function validateTitle(&$user, &$title, &$error) {
		$groups = $user->getEffectiveGroups();
		if (!is_object($title) || in_array('sysop', $groups)) return true;

		# Cache results
		$key = $user->getID().'\x07'.$title->getPrefixedText();
		if (array_key_exists($key, $this->cache)) {
			$error = $this->cache[$key][1];
			return $this->cache[$key][0];
		}
		
		# Determine whether valid and create error message if not
		$restrictions = $title->getRestrictions('read');
		if ($valid = (count($restrictions) < 1 || count(array_intersect($restrictions, $groups)) > 0)) $error = '';
		else {
			$restrictions = array_map('ucfirst', $restrictions);
			$groups = array_pop($restrictions);
			if (count($restrictions) > 0) $groups = 'groups '.join(', ', $restrictions)." and $groups";
			else $groups = "the $groups group";
			$error = wfMsg('badaccess-read', $title->getPrefixedText(), $groups);
		}
		
		$this->cache[$key] = array($valid, $error);
		return $valid;
	}

	/**
	 * Needed in some versions to prevent Special:Version from breaking
	 */
	public function __toString() {
		return __CLASS__;
	}
}

/**
 * Hooks into Database::query and Database::fetchObject via the LoadBalancer class
 * - this is a global because PHP doesn't like nested class definitions
 */
function wfAddDatabaseHooks() {
	global $wgLoadBalancer, $wgDBtype;

	# This ensures that $wgLoadBalancer is not a stub object when we subclass it
	# todo: this should be able to work in the case of it being a stub object
	wfGetDB();

	# Create a replica of the Database class
	# - query method is overriden to ensure that old_id field is returned for all queries which read old_text field
	# - fetchObject method is overridden to validate row content based on old_id
	# - the changes to this class are only active for SELECT statements and while not processing security directives
	$type = ucfirst($wgDBtype);
	eval("class Database{$type}2 extends Database{$type}".' {
		public function query($sql, $fname = "", $tempIgnore = false) {
			$count = false;
			$patched = preg_replace_callback("/(?<=SELECT ).+?(?= FROM)/", "SimpleSecurity::patchSQL", $sql, 1, $count);
			return parent::query($count ? $patched : $sql, $fname, $tempIgnore);
		}
		function fetchObject(&$res) {
			$row = parent::fetchObject($res);
			if (isset($row->old_text)) SimpleSecurity::validateRow($row);
			return $row;
		}
	}');

	# Create a replica of the LoadBalancer class which uses the new Database subclass for its connection objects
	class LoadBalancer2 extends LoadBalancer {
		function reallyOpenConnection(&$server) {
			$server['type'] .= '2';
			return parent::reallyOpenConnection($server);
		}
	}

	# Replace the $wgLoadBalancer object with an identical instance of the new LoadBalancer2 class
	$wgLoadBalancer->closeAll(); # Close any open connections as they will be of the original Database class
	$oldLoadBalancer = $wgLoadBalancer;
	$wgLoadBalancer  = new LoadBalancer2($oldLoadBalancer->mServers);
	foreach (array_keys(get_class_vars('LoadBalancer')) as $k) $wgLoadBalancer->$k = $oldLoadBalancer->$k;
}

/**
 * Called from $wgExtensionFunctions array when initialising extensions
 */
function wfSetupSimpleSecurity() {
	global $wgSimpleSecurity, $wgLanguageCode, $wgMessageCache, $wgSecurityUseDBHook;

	# Hooks into Database::query and Database::fetchObject via the LoadBalancer class
	if ($wgSecurityUseDBHook) wfAddDatabaseHooks();

	# Instantiate the SimpleSecurity singleton now that the environment is prepared
	$wgSimpleSecurity = new SimpleSecurity();

	# Add the messages used by the specialpage
	if ($wgLanguageCode == 'en') {
		$wgMessageCache->addMessages(array(
			'security'            => "Security log",
			'securitylogpage'     => "Security log",
			'securitylogpagetext' => "This is a log of actions blocked by the [[MW:Extension:SimpleSecurity|SimpleSecurity extension]].",
			'securitylogentry'    => "",
			'badaccess-read'      => "\nWarning: \"$1\" is referred to here, but it can only be viewed by $2.\n"
		));
	}
}

/**
 * Register magic words
 */
function wfSimpleSecurityLanguageGetMagic(&$magicWords, $langCode = 0) {
	global $wgSecurityMagicIf, $wgSecurityMagicGroup;
	$magicWords[$wgSecurityMagicIf]    = array($langCode, $wgSecurityMagicIf);
	$magicWords[$wgSecurityMagicGroup] = array($langCode, $wgSecurityMagicGroup);
	return true;
}
