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
);
