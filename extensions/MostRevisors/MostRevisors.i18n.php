<?php
/**
 * Internationalisation file for extension mostrevisors.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'mostrevisors' => 'Pages with the most revisors',
	'mostrevisors-desc' => 'List [[Special:MostRevisors|pages with the most revisors]]',
	'mostrevisors-header' => "'''This page lists the {{PLURAL:$1|page|$1 pages}} with most revisors on the wiki.'''",
	'mostrevisors-limitlinks' => 'Show up to $1 pages',
	'mostrevisors-namespace' => 'Namespace:',
	'mostrevisors-none' => 'No entries were found.',
	'mostrevisors-ns-header' => "'''This page lists the {{PLURAL:$1|page|$1 pages}} with most revisors in the $2 namespace.'''",
	'mostrevisors-showing' => 'Listing {{PLURAL:$1|page|$1 pages}}:',
	'mostrevisors-submit' => 'Go',
	'mostrevisors-showredir' => 'Show redirect pages',
	'mostrevisors-hideredir' => 'Hide redirect pages',
	'mostrevisors-viewcontributors' => 'View main contributors',
	'mostrevisors-text' => 'Show [[Special:MostRevisions|pages with the most revisors]], starting from [[MediaWiki:mostrevisors-limit-few-revisors|{{MediaWiki:Mostrevisors-limit-few-revisors}} revisors]].',

	// Settings. Do not translate these messages.
	'mostrevisors-users' => 'editors',
	'mostrevisors-limit-few-revisors' => '1',
);

$messages['qqq'] = array(
	'mostrevisors' => '{{Identical|Most Revisors}}',
	'mostrevisors-desc' => 'Short description of the extension, shown on [[Special:Version]].',
);
