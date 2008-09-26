<?php

/*
 * A plugin to authenticate against a libnss-mysql database
 *
 * Copyright 2008 - Bryan Tong Minh / Delft Aerospace Rocket Engineering
 * Licensed under the terms of the GNU General Public License, version 2
 * or any later version.
 *
 */
 
 ### READ BEFORE USING ###
 /* 
  * This plugin allows authentication against an libnss-mysql database and thus
  * allows the use of the same login for MediaWiki as for shell.
  *
  */
  

$wgAutoloadClasses['NssMySQLAuthPlugin'] = dirname( __FILE__ ) . '/NssMySQLAuthPlugin.php';
$wgAutoloadClasses['Md5crypt'] = dirname( __FILE__ ) . '/Md5crypt.php';
$wgAutoloadClasses['SpecialAccountManager'] = dirname( __FILE__ ) . '/SpecialAccountManager.php';
$wgSpecialPages['AccountManager'] = 'SpecialAccountManager';

$wgNssMySQLAuthDB = false;

$wgExtensionFunctions[] = array( 'NssMySQLAuthPlugin', 'initialize' );

$wgUserProperties = array( 'address', 'city' );
$wgActivityModes = array( 'active', 'inactive' );
