<?php

/**
 * Internationalization file for the Deployment extension.
 *
 * @file Deployment.i18n.php
 * @ingroup Deployment
 *
 * @author Jeroen De Dauw
 */

$messages = array();

/** English
 * @author Jeroen De Dauw
 */
$messages['en'] = array(
	// General
	'deployment-desc' => 'Provides a way to install extensions via GUI and update them and the wiki itself via another GUI',

	// Filesystem: Direct

	// Filesystem: FTP
	'deploy-ftp-not-loaded' => 'The FTP PHP extension is not available',
	'deploy-ftp-ssl-not-loaded' => 'The loaded FTP PHP extension does not support SSL',
	'deploy-ftp-username-required' => 'FTP username is required',
	'deploy-ftp-password-required' => 'FTP password is required',
	'deploy-ftp-hostname-required' => 'FTP hostname is required',
	'deploy-ftp-connect-failed' => 'Failed to connect to FTP server $1:$2',
	
	// Filesystem: FTP
	'deploy-ssh-not-loaded' => 'The FTP SSH2 extension is not available',
	'deploy-ssh2-no-stream-get-contents'  => 'The SSH2 PHP extension is available, however, the PHP5 function stream_get_contents() is also required',
	'deploy-ssh2-username-required' => 'SSH username is required',
	'deploy-ssh2-password-required' => 'SSH password or private key is required',
	'deploy-ssh2-hostname-required' => 'SSH hostname is required',
	'deploy-ssh2-connect-failed' => 'Failed to connect to SSH2 server $1:$2',
	'deploy-ssh2-key-authentication-failed' => 'Public and private keys are incorrect for username $1',
	'deploy-ssh2-password-authentication-failed' => 'Username or password incorrect for username $1',
	'deploy-ssh2-command-failed' => 'Unable to perform command: $1',
	
);
