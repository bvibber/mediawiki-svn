<?php
/**
 * Internationalisation file for extension EditMessages.
 *
 * @addtogroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'editmessages-desc'     => '[[Special:EditMessages|Web-based editing]] of large numbers of Messages*.php files'
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
