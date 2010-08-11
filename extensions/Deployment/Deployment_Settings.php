<?php

/**
 * Settings file for the Deployment extension.
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:Deployment
 *
 * @file Deployment_Settings.php
 * @ingroup Deployment
 *
 * @author Jeroen De Dauw
 */

$wgRepositoryLocation = 'http://www.mediawiki.org/wiki/Special:Repository';
$wgRepositoryApiLocation = 'http://www.mediawiki.org/w/api.php';

$wgRepositoryPackageStates = array(
	//'dev',
	//'alpha',
	'beta',
	//'rc',
	'stable',
	//'deprecated',
);