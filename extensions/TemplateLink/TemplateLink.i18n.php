<?php
/**
 * TemplateLink extension - shows a template as a new page
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Magnus Manske
 * @copyright © 2007 Magnus Manske
 * @licence GNU General Public Licence 2.0 or later
 */

$allMessages = array(
        'en' => array( 
                'templatelink' => 'Template Link',
                'templatelink_empty' => 'You have to supply a parameter.',
                'templatelink_newtitle' => '$1 (transcluded)',
        ),
        'de' => array(
                'templatelink' => 'Vorlagen-Link',
                'templatelink_empty' => 'Ein Parameter muss angegeben werden.',
                'templatelink_newtitle' => '$1 (ersetzt)',
        ),
	'hsb' => array(
		'templatelink' => 'Předłohowy wotkaz',
		'templatelink_empty' => 'Dyrbiš parameter podać.',
		'templatelink_newtitle' => '$1 (narunany)',
	),
        'nl' => array(
                'templatelink' => 'Sjabloonverwijzing',
                'templatelink_empty' => 'Geef een parameter op.',
                'templatelink_newtitle' => '$1 (getranscludeerd)',
        ),
);
