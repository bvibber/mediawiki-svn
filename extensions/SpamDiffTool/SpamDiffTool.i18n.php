<?php
/**
 * Internationalisation file for extension SpamDiffTool.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'spamdifftool' => 'Manage Spam Blacklist',
	'spamdifftool_cantedit' => 'Sorry - you don\'t have permission to edit the Spam Blacklist.',
	'spamdifftool_notext' => 'There is no text to add to the Spam Blacklist. Click <a href=\'$1\'>here</a> to continue. ',
	'spamdifftool_confirm' => 'Confirm that you want to add these entries to the Spam Blacklist. (Click <a href=\'http://www.mediawiki.org/w/index.php?title=Talk:SpamDiffTool_Extension&action=edit&section=new\' target=\'new\'>here</a> to report a problem.)',
	'spamdifftool_summary' => 'Adding to Spam Blacklist',
			'spamdifftool_urls_detected' => 'The following URLs were detected in the edit(s), which ones would you like to add to the Spam Blacklist? These options order from more restrictive to less restrictive, blocking the entire domain will block all links to anything coming from that domain. <br/><br/>Be sure not to block entire domains that host user accounts, like blogpost.com, geocities.com, etc. ',
	'spamdifftool_no_urls_detected' => 'No urls were detected. Click <a href=\'$1\'>here</a> to return.',
	'spamdifftool_spam_link_text' => 'add to spam',
	'spamdifftool_option_domain' => 'all from this domain',
	'spamdifftool_option_subdomain' => 'all from this subdomain',
	'spamdifftool_option_directory' => 'this subdomain and directory',
	'spamdifftool_option_none' => 'nothing',
	'spamdifftool_block'		=> 'Block:',
	'spamdifftool_submit_buttom'		=> 'Submit',
	);

$messages['fr'] = array(
	'spamdifftool' => 'Gestion de la Liste Noire des Spams',
	'spamdifftool_cantedit' => 'Désolé - Vous n’avez pas la permission d’éditer la Liste Noire des Spams.',
	'spamdifftool_notext' => 'Il n’y a pas de texte à ajouter à la Liste Noire des Spams. Cliquez <a href=\'$1\'>ici</a> pour continuer. ',
	'spamdifftool_confirm' => 'Confirmez que vous voulez ajouter ces entrées dans la Liste Noire des Spams. (Cliquez <a href=\'http://www.mediawiki.org/w/index.php?title=Talk:SpamDiffTool_Extension&action=edit&section=new\' target=\'new\'>ici</a> pour signaler tout problème.)',
	'spamdifftool_summary' => 'Ajouté à la Liste Noire des Spams',
	'spamdifftool_urls_detected' => 'Les URLs suivantes ont été détectées dans ces éditions. Quelles sont lesquelles que vous voulez ajouter à la Liste Noire des Spams ? Ces options vont des plus restrictives vers les moins restrictives. Le blocage d’un nom de domaine entier bloquera tous les liens provenant de celui-ci.<br/><br/>Assurez-vous de ne pas bloquer des domaines entiers que détiennent certains compteurs utilisateurs tels que blogpost.com, geocities.com, etc. ',
	'spamdifftool_no_urls_detected' => 'Aucune URL n’a été détectée. Cliquez <a href=\'$1\'>ici</a> pour revenir en arrière.',
	'spamdifftool_spam_link_text' => 'ajouter aux spams',
	'spamdifftool_option_domain' => 'tout depuis ce domaine',
	'spamdifftool_option_subdomain' => 'tout depuis ce sous-domaine',
	'spamdifftool_option_directory' => 'ce sous-domaine et ce répertoire',
	'spamdifftool_option_none' => 'néant',
	'spamdifftool_block'		=> 'Bloquer :',
	'spamdifftool_submit_buttom'		=> 'Soumettre',
	);

?>
