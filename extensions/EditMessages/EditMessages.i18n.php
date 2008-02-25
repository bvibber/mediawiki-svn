<?php
/**
 * Internationalisation file for extension EditMessages.
 *
 * @addtogroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'editmessages-desc'     => '[[Special:EditMessages|Web-based editing]] of large numbers of Messages*.php files',
	'editmessages' => 'Edit messages',
	'editmsg-target' => 'Target message: ',
	'editmsg-search' => 'Search',
	'editmsg-get-patch' => 'Generate patch',
	'editmsg-new-search' => 'New search',
	'editmsg-warning-parse1' => '* Message name regex not matched: $1',
	'editmsg-warning-parse2' => '* Quote character expected after arrow: $1',
	'editmsg-warning-parse3' => '* End of value string not found: $1',
	'editmsg-warning-file' => '* File read errors were encountered for the following languages: $1',
	'editmsg-warning-mismatch' => '* The original text did not have the expected value for the following languages: $1',
	'editmsg-apply-patch' => 'Apply patch',
	'editmsg-no-patch' => 'Unable to execute "patch" command',
	'editmsg-patch-failed' => 'Patch failed with exit status $1',
	'editmsg-patch-success' => 'Successfully patched.',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'editmessages-desc'        => '[[Special:EditMessages|Webbasiertes Bearbeiten]] eine großen Anzahl an Messages*.php-Dateien',
	'editmessages'             => 'Systemnachricht bearbeiten',
	'editmsg-target'           => 'zu bearbeitende Systemnachricht:',
	'editmsg-search'           => 'Suche',
	'editmsg-get-patch'        => 'Erstelle Patch',
	'editmsg-new-search'       => 'Neue Suche',
	'editmsg-warning-parse1'   => '* Regex trifft auf keine Systemnachrichten zu: $1',
	'editmsg-warning-parse2'   => '* Quote character expected after arrow: $1',
	'editmsg-warning-parse3'   => '* Ende der Zeichenkette nicht gefunden: $1',
	'editmsg-warning-file'     => '* Es ist ein Dateilesefehler für die folgenden Sprachen aufgetreten: $1',
	'editmsg-warning-mismatch' => '* Der Originaltext hat nicht den erwarteten Wert für die folgenden Sprachen: $1',
	'editmsg-apply-patch'      => 'Patch anwenden',
	'editmsg-no-patch'         => 'Patch-Kommando kann nicht angewendet werde',
	'editmsg-patch-failed'     => 'Patch ist fehlgeschlagen mit dem exit-Status $1',
	'editmsg-patch-success'    => 'Erfolgreich gepatcht.',
);
