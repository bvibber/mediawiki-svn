<?php
/*
 * Internationalization for CloseWikis extension.
 */

$messages = array();

/**
 * English
 * @author Victor Vasiliev
 */
$messages['en'] = array(
	'closewikis-desc'           => 'Allows to close wiki sites in wiki farms',
	'closewikis-closed'         => '$1',
	'closewikis-closed-default' => 'This wiki is closed',
	'closewikis-page'           => 'Close wiki',

	'closewikis-page-close' => 'Close wiki',
	'closewikis-page-close-wiki' => 'Wiki:',
	'closewikis-page-close-dreason' => 'Reason (displayed):',
	'closewikis-page-close-reason' => 'Reason (logged):',
	'closewikis-page-close-submit' => 'Close',
	'closewikis-page-close-success' => 'Wiki successfully closed',
	'closewikis-page-reopen' => 'Reopen wiki',
	'closewikis-page-reopen-wiki' => 'Wiki:',
	'closewikis-page-reopen-reason' => 'Reason:',
	'closewikis-page-reopen-submit' => 'Reopen',
	'closewikis-page-reopen-success' => 'Wiki successfully reopened',
	'closewikis-page-err-nowiki' => 'Invalid wiki specified',
	'closewikis-page-err-closed' => 'Wiki is already closed',
	'closewikis-page-err-opened' => 'Wiki is not closed',

	'closewikis-log'         => 'Wikis closure log',
	'closewikis-log-header'  => 'Here is a log of all wiki closures and reopenings made by stewards',
	'closewikis-log-close'   => 'closed $2',
	'closewikis-log-reopen'  => 'reopened $2',
	'right-editclosedwikis'  => 'Edit closed wikis',
);
