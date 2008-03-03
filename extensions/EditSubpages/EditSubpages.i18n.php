<?php
/**
 * Internationalisation file for the EditSubpages extension
 * @addtogroup Extensions
*/

$messages = array();

/* English
 @author Ryan Schmidt
 */
$messages['en'] = array(
	'editsubpages-desc' => "Allows sysops to unlock a page and all subpages of that page
for anonymous editing via [[MediaWiki:Unlockedpages]]",
	'unlockedpages' => ' #<!-- leave this line alone --><pre>
# Put pages you want to unlock below using
# * pagename
# Unlocked pages must begin with a bullet to be counted,
# everything else is considered a comment
# Pagenames may be [[links]] and are case-sensitive
# Also, talk pages will be automatically unlocked with the page
# See http://www.mediawiki.org/wiki/Extension:EditSubpages for more info
 #</pre><!-- leave this line alone -->',
);