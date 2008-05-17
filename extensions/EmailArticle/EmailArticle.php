<?php
/**
 * EmailArticle extension - Send rendered HTML article to an email address or list of addresses using phpmailer
 *
 * See http://www.mediawiki.org/wiki/Extension:EmailArticle for installation and usage details
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Aran Dunkley [http://www.organicdesign.co.nz/nad User:Nad]
 * @copyright © 2007 Aran Dunkley
 * @licence GNU General Public Licence 2.0 or later
 */

if (!defined('MEDIAWIKI')) die('Not an entry point.');

define('EMAILARTICLE_VERSION','1.0.9, 2008-01-19');

$wgEmailArticleGroup           = 'sysop';            # Users must belong to this group to send emails (empty string means anyone can send)
$wgEmailArticleContactsCat     = '';                 # This specifies the name of a category containing categories of contact articles
$wgEmailArticleCss             = 'EmailArticle.css'; # A minimal CSS article to embed in the email (eg. monobook/main.css without portlets, actions etc)
$wgEmailArticleAllowRemoteAddr = array($_SERVER['SERVER_ADDR'],'127.0.0.1'); # Allow anonymous sending from these addresses
$wgEmailArticleAllowAllUsers   = false;              # Whether to allow sending to all users (the "user" group)
$wgEmailArticleToolboxLink     = 'Send to email';    # Link title for toolbox link (set to "" to not have any link in toolbox)
$wgEmailArticleActionLink      = 'email';            # Link title for action link (set to "" to not have any action link)
$wgPhpMailerClass              = dirname(__FILE__).'/phpmailer/class.phpmailer.php'; # From http://phpmailer.sourceforge.net/

if ($wgEmailArticleGroup) $wgGroupPermissions['sysop'][$wgEmailArticleGroup] = true;

$wgExtensionFunctions[] = 'wfSetupEmailArticle';

$wgExtensionCredits['specialpage'][] = array(
	'name'        => 'Special:EmailArticle',
	'author'      => '[http://www.organicdesign.co.nz/nad User:Nad]',
	'description' => 'Send rendered HTML article to an email address or list of addresses using [http://phpmailer.sourceforge.net phpmailer].',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:EmailArticle',
	'version'     => EMAILARTICLE_VERSION
	);

# If form has been posted, include the phpmailer class
if (isset($_REQUEST['ea_send'])) require_once($wgPhpMailerClass);

# Add toolbox and action links
if ($wgEmailArticleToolboxLink) {
	$wgHooks['MonoBookTemplateToolboxEnd'][] = 'wfEmailArticleToolboxLink';
	function wfEmailArticleToolboxLink() {
		global $wgEmailArticleToolboxLink,$wgTitle;
		if (is_object($wgTitle)) {
			$url = Title::makeTitle(NS_SPECIAL,'EmailArticle')->getLocalURL('ea_title='.$wgTitle->getPrefixedText());
			echo("<li><a href=\"$url\">$wgEmailArticleToolboxLink</li>");
			}
		return true;
		}
	}
if ($wgEmailArticleActionLink) {
	$wgHooks['SkinTemplateTabs'][] = 'wfEmailArticleActionLink';
	function wfEmailArticleActionLink(&$skin,&$actions) {
		global $wgEmailArticleActionLink,$wgTitle;
		if (is_object($wgTitle)) {
			$url = Title::makeTitle(NS_SPECIAL,'EmailArticle')->getLocalURL('ea_title='.$wgTitle->getPrefixedText());
			$actions['email'] = array('text' => $wgEmailArticleActionLink, 'class' => false, 'href' => $url);
			}
		return true;
		}
	}


# Define a new class based on the SpecialPage class
require_once("$IP/includes/SpecialPage.php");
class SpecialEmailArticle extends SpecialPage {

	var $recipients = array();
	var $title;
	var $subject;
	var $header;
	var $cat;
	var $group;
	var $list;
	var $textonly;
	var $css;

	# Constructor
	function SpecialEmailArticle() {
		global $wgEmailArticleGroup;
		SpecialPage::SpecialPage('EmailArticle',$wgEmailArticleGroup);
		}


	# Override SpecialPage::execute($param = '')
	function execute($param) {
		global $wgOut,$wgUser,$wgEmailArticleContactsCat,$wgGroupPermissions,$wgSitename,$wgEmailArticleCss,$wgEmailArticleAllowAllUsers;
		$db =& wfGetDB(DB_SLAVE);
		$param = str_replace('_',' ',$param);
		$this->setHeaders();

		# Get info from request or set to defaults
		$this->title    = isset($_REQUEST['ea_title'])    ? $_REQUEST['ea_title']    : $param;
		$this->subject  = isset($_REQUEST['ea_subject'])  ? $_REQUEST['ea_subject']  : "\"{$this->title}\" article sent from $wgSitename";
		$this->header   = isset($_REQUEST['ea_header'])   ? $_REQUEST['ea_header']   : '';
		$this->cat      = isset($_REQUEST['ea_cat'])      ? $_REQUEST['ea_cat']      : '';
		$this->group    = isset($_REQUEST['ea_group'])    ? $_REQUEST['ea_group']    : '';
		$this->list     = isset($_REQUEST['ea_list'])     ? $_REQUEST['ea_list']     : '';
		$this->textonly = isset($_REQUEST['ea_textonly']) ? $_REQUEST['ea_textonly'] : false;
		$this->css      = isset($_REQUEST['ea_css'])      ? $_REQUEST['ea_css']      : $wgEmailArticleCss;

		# Bail if no article title to send has been specified
		if ($this->title) $wgOut->addWikiText(wfMsg('ea_heading',$this->title));
		else return $wgOut->addWikiText(wfMsg('ea_noarticle'));

		# If the send button was clicked, attempt to send and exit
		if (isset($_REQUEST['ea_send'])) return $this->send();

		# Render form
		$special = Title::makeTitle(NS_SPECIAL,'EmailArticle');
		$wgOut->addHTML(wfElement('form',array(
			'class'  => 'EmailArticle',
			'action' => $special->getLocalURL('action=submit'),
			'method' => 'POST'
			),null));
		$wgOut->addHTML('<fieldset><legend>'.wfMsg('ea_selectrecipients').'</legend>');
		$wgOut->addHTML('<table style="padding:0;margin:0;border:none;">');

		# If $wgEmailArticleContactsCat is set, create a select list of all categories
		if ($wgEmailArticleContactsCat) {
			$cl = $db->tableName('categorylinks');
			$cats = '';
			$result = $db->query("SELECT cl_from FROM $cl WHERE cl_to = '$wgEmailArticleContactsCat' ORDER BY cl_sortkey");
			if ($result instanceof ResultWrapper) $result = $result->result;
			if ($result) while ($row = $db->fetchRow($result)) {
				$t = Title::newFromID($row[0]);
				if ($t->getNamespace() == NS_CATEGORY) {
					$cat = $t->getText();
					$selected = $cat == $this->cat ? ' selected' : '';
					$cats .= "<option$selected>$cat</option>";
					}
				}
			if ($cats) $wgOut->addHTML("<tr><td>From category:</td><td><select name=\"ea_cat\"><option/>$cats</select></td></tr>\n");
			}

		# Allow selection of a group
		$groups = '<option/>';
		foreach (array_keys($wgGroupPermissions) as $group) if ($group != '*') {
			$selected = $group == $this->group ? ' selected' : '';
			if ($wgEmailArticleAllowAllUsers || $group != 'user') $groups .= "<option$selected>$group</option>";
			}
		$wgOut->addHTML("<tr><td>From group:</td><td><select name=\"ea_group\">$groups</select></td></tr>\n");
		$wgOut->addHTML('</table>');

		# Addition of named list
		$wgOut->addWikiText(wfMsg('ea_selectlist'));
		$wgOut->addHTML("<textarea name=\"ea_list\" rows=\"5\">{$this->list}</textarea><br />\n");
		$wgOut->addHTML('</fieldset>');

		$wgOut->addHTML('<fieldset><legend>'.wfMsg('ea_compose').'</legend>');

		# Subject
		$wgOut->addWikiText(wfMsg('ea_subject'));
		$wgOut->addHTML(wfElement('input',array('type' => 'text', 'name' => 'ea_subject', 'value' => $this->subject, 'style' => "width:100%")));

		# Header
		$wgOut->addWikiText(wfMsg('ea_header'));
		$wgOut->addHTML("<textarea name=\"ea_header\" rows=\"5\">{$this->header}</textarea><br />\n");

		# CSS
		$page = $db->tableName('page');
		$csss = '';
		$result = $db->query("SELECT page_id FROM $page WHERE page_title LIKE '%.css' ORDER BY page_title");
		if ($result instanceof ResultWrapper) $result = $result->result;
		if ($result) while ($row = $db->fetchRow($result)) {
			$t = Title::newFromID($row[0])->getPrefixedText();
			$selected = $t == $this->css ? ' selected' : '';
			$csss .= "<option$selected>$t</option>";
			}
		if ($csss) {
			$wgOut->addWikiText(wfMsg('ea_selectcss'));
			$wgOut->addHTML("<select name=\"ea_css\"><option/>$csss</select>\n");
			}

		$wgOut->addHTML("</fieldset>");

		# Submit buttons & hidden values
		$wgOut->addHTML(wfElement('input',array('type' => 'submit','name' => 'ea_send', 'value' => wfMsg('ea_send'))));
		$wgOut->addHTML(wfElement('input',array('type' => 'submit','name' => 'ea_show', 'value' => wfMsg('ea_show'))));
		$wgOut->addHTML(wfElement('input',array('type' => 'hidden','name' => 'ea_title','value' => $this->title)));

		$wgOut->addHTML('</form>');

		# If the show button was clicked, render the list
		if (isset($_REQUEST['ea_show'])) return $this->send(false);
		}


	# Send the message to the recipients (or just list them if arg = false)
	function send($send = true) {
		global $wgOut,$wgUser,$wgParser,$wgServer,$wgScript,$wgArticlePath,$wgScriptPath,
			$wgEmailArticleCss,$wgEmailArticleGroup,$wgEmailArticleAllowRemoteAddr,$wgEmailArticleAllowAllUsers;

		# Set error and bail if user not in postmaster group, and request not from trusted address
		if ($wgEmailArticleGroup && !in_array($wgEmailArticleGroup,$wgUser->getGroups()) && !in_array($_SERVER['REMOTE_ADDR'],$wgEmailArticleAllowRemoteAddr)) {
			$wgOut->addWikiText(wfMsg('ea_error',$this->title,'Permission denied'));
			return false;
			}

		$db       = &wfGetDB(DB_SLAVE);
		$title    = Title::newFromText($this->title);
		$opt      = new ParserOptions;

		# Get contact article titles from selected cat
		if ($this->cat) {
			$cl     = $db->tableName('categorylinks');
			$result = $db->query("SELECT cl_from FROM $cl WHERE cl_to = '{$this->cat}' ORDER BY cl_sortkey");
			while ($row = mysql_fetch_row($result)) $this->addRecipient(Title::newFromID($row[0]));
			}

		# Get email addresses from users in selected group
		if ($this->group && ($wgEmailArticleAllowAllUsers || $this->group != 'user')) {
			$u  = str_replace('`','',$db->tableName('user'));
			$ug = str_replace('`','',$db->tableName('user_groups'));
			if ($this->group == 'user') $sql = "SELECT user_email FROM $u WHERE user_email != ''";
			else $sql = "SELECT $u.user_email FROM $u,$ug WHERE $ug.ug_user = $u.user_id AND $ug.ug_group = '{$this->group}'";
			$result = $db->query($sql);
			if ($result instanceof ResultWrapper) $result = $result->result;
			while ($row = $db->fetchRow($result)) $this->addRecipient($row[0]);
			}

		# Recipients from list (expand templates in wikitext)
		$list = $wgParser->preprocess($this->list,$title,$opt);
		foreach (preg_split("/[\\x00-\\x1f,;*]+/",$list) as $item) $this->addRecipient($item);

		# Compose the wikitext content of the article to send
		$article = new Article($title);
		$message = $article->getContent();
		if ($this->header) $message = "{$this->header}\n\n$message";

		# Convert the message text to html unless textonly
		if ($this->textonly == '') {

			# Parse the wikitext using absolute URL's for local article links
			$tmp           = array($wgArticlePath,$wgScriptPath,$wgScript);
			$wgArticlePath = $wgServer.$wgArticlePath;
			$wgScriptPath  = $wgServer.$wgScriptPath;
			$wgScript      = $wgServer.$wgScript;
			$message       = $wgParser->parse($message,$title,$opt,true,true)->getText();
			list($wgArticlePath,$wgScriptPath,$wgScript) = $tmp;

			# Get CSS content if any
			if ($this->css) {
				$article = new Article(Title::newFromText($this->css));
				$css = '<style type="text/css">'.$article->getContent().'</style>';
				}

			# Create a html wrapper for the message
			$head    = "<head>$css</head>";
			$message = "<html>$head<body style=\"margin:10px\"><div id=\"#bodyContent\">$message</div></body></html>";

			}
 
		# Send message or list recipients
		$count = count($this->recipients);
		if ($count > 0) {

			# Set up new mailer instance if sending
			if ($send) {
				$mail           = new PHPMailer();
				$mail->From     = $wgUser->isValidEmailAddr($wgUser->getEmail()) ? $wgUser->getEmail() : "wiki@$wgServer";
				$mail->FromName = User::whoIsReal($wgUser->getId());
				$mail->Subject  = $this->subject;
				$mail->Body     = $message;
				$mail->IsHTML(!$this->textonly);
				}
			else $msg = wfMsg('ea_listrecipients',$count);
	
			# Loop through recipients sending or adding to list
			foreach ($this->recipients as $recipient) $send ? $mail->AddAddress($recipient) : $msg .= "\n*[mailto:$recipient $recipient]";
 
			if ($send) {
				if ($state = $mail->Send()) $msg = wfMsg('ea_sent',$this->title,$count,$wgUser->getName());
				else $msg = wfMsg('ea_error',$this->title,$mail->ErrorInfo);
				}
			else $state = $count;
			}
		else $msg = wfMsg('ea_error',$this->title,wfMsg('ea_norecipients'));

		$wgOut->addWikiText($msg);
		return $state;
		}

	# Add a recipient the list
	# - accepts title objects for article containing email address, or string of actual address
	function addRecipient($recipient) {
		if (is_object($recipient) && $recipient->exists()) {
			$article = new Article($recipient);
			if (preg_match('/[a-z0-9_.-]+@[a-z0-9_.-]+/i',$article->getContent(),$emails)) $recipient = $emails[0];
			else $recipient = '';
			}
		if ($valid = User::isValidEmailAddr($recipient)) $this->recipients[] = $recipient;
		return $valid;
		}

	}

# Called from $wgExtensionFunctions array when initialising extensions
function wfSetupEmailArticle() {
	global $wgLanguageCode,$wgMessageCache;

	# Add the messages used by the specialpage
	if ($wgLanguageCode == 'en') {
		$wgMessageCache->addMessages(array(
			'emailarticle'        => "EmailArticle",
			'ea_heading'          => "=== Emailing [[$1]] article ===",
			'ea_noarticle'        => "Please specify an article to send, for example [[Special:EmailArticle/Main Page]].",
			'ea_norecipients'     => "No valid email addresses found!",
			'ea_listrecipients'   => "=== List of $1 {{PLURAL:$1|recipient|recipients}} ===",
			'ea_error'            => "'''Error sending [[$1]]:''' ''$2''",
			'ea_sent'             => "Article [[$1]] sent successfully to '''$2''' {{PLURAL:$2|recipient|recipients}} by [[User:$3|$3]].",
			'ea_selectrecipients' => "Select recipients",
			'ea_compose'          => "Compose content",
			'ea_selectlist'       => "Additional recipients as article titles or email addresses\n"
			                       . "*''separate items with , ; * \\n\n"
			                       . "*''list can contain templates and parser-functions''",
			'ea_show'             => "Show recipients",
			'ea_send'             => "Send!",
			'ea_subject'          => "Enter a subject line for the email",
			'ea_header'           => "Prepend content with optional message (wikitext)",
			'ea_selectcss'        => "Select a CSS stylesheet"
			));
		}

	# Add the specialpage to the environment
	SpecialPage::addPage(new SpecialEmailArticle());
	}
