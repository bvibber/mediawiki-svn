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

define('EMAILARTICLE_VERSION', '1.1.0, 2008-06-04');

$wgEmailArticleGroup           = 'sysop';            # Users must belong to this group to send emails (empty string means anyone can send)
$wgEmailArticleContactsCat     = '';                 # This specifies the name of a category containing categories of contact articles
$wgEmailArticleCss             = 'EmailArticle.css'; # A minimal CSS article to embed in the email (eg. monobook/main.css without portlets, actions etc)
$wgEmailArticleAllowRemoteAddr = array($_SERVER['SERVER_ADDR'],'127.0.0.1'); # Allow anonymous sending from these addresses
$wgEmailArticleAllowAllUsers   = false;              # Whether to allow sending to all users (the "user" group)
$wgEmailArticleToolboxLink     = 'Send to email';    # Link title for toolbox link (set to "" to not have any link in toolbox)
$wgEmailArticleActionLink      = 'email';            # Link title for action link (set to "" to not have any action link)
$wgPhpMailerClass              = dirname(__FILE__).'/phpMailer_v2.1.0beta2/class.phpmailer.php'; # From http://phpmailer.sourceforge.net/

if ($wgEmailArticleGroup) $wgGroupPermissions['sysop'][$wgEmailArticleGroup] = true;

$dir = dirname(__FILE__) . '/';
$wgAutoloadClasses['SpecialEmailArticle'] = $dir . 'EmailArticle_body.php';
$wgExtensionMessagesFiles['EmailArticle'] = $dir . 'EmailArticle.i18n.php';
$wgSpecialPages['EmailArticle'] = 'SpecialEmailArticle';

$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'Special:EmailArticle',
	'author'         => '[http://www.organicdesign.co.nz/nad User:Nad]',
	'description'    => 'Send rendered HTML article to an email address or list of addresses using [http://phpmailer.sourceforge.net phpmailer].',
	'descriptionmsg' => 'ea-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:EmailArticle',
	'version'        => EMAILARTICLE_VERSION
);

# If form has been posted, include the phpmailer class
if (isset($_REQUEST['ea-send'])) require_once($wgPhpMailerClass);

# Add toolbox and action links
if ($wgEmailArticleToolboxLink) {
	$wgHooks['MonoBookTemplateToolboxEnd'][] = 'wfEmailArticleToolboxLink';
}

if ($wgEmailArticleActionLink) {
	$wgHooks['SkinTemplateTabs'][] = 'wfEmailArticleActionLink';
}

function wfEmailArticleToolboxLink() {
	global $wgEmailArticleToolboxLink,$wgTitle;
	if (is_object($wgTitle)) {
		$url = Title::makeTitle(NS_SPECIAL,'EmailArticle')->getLocalURL('ea-title='.$wgTitle->getPrefixedText());
		echo("<li><a href=\"$url\">$wgEmailArticleToolboxLink</li>");
		}
	return true;
}


function wfEmailArticleActionLink(&$skin,&$actions) {
	global $wgEmailArticleActionLink,$wgTitle;
	if (is_object($wgTitle)) {
		$url = Title::makeTitle(NS_SPECIAL,'EmailArticle')->getLocalURL('ea-title='.$wgTitle->getPrefixedText());
		$actions['email'] = array('text' => $wgEmailArticleActionLink, 'class' => false, 'href' => $url);
	}
	return true;
}
