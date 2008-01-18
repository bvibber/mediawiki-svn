<?php
/**
 * OpenID.i18n.php -- Interface messages for OpenID for MediaWiki
 * Copyright 2006,2007 Internet Brands (http://www.internetbrands.com/)
 * Copyright 2007,2008 Evan Prodromou <evan@prodromou.name>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Evan Prodromou <evan@prodromou.name>
 * @addtogroup Extensions
 */

if (!defined('MEDIAWIKI')) {
	exit( 1 );
}

$OpenIDMessages =array(
   'en' => array('openidlogin' => 'Login with OpenID',
				 'openidfinish' => 'Finish OpenID login',
				 'openidserver' => 'OpenID server',
				 'openidconvert' => 'OpenID converter',
				 'openidlogininstructions' => 'Enter your OpenID identifier to log in:',
				 'openiderror' => 'Verification error',
				 'openiderrortext' => 'An error occured during verification of the OpenID URL.',
				 'openidconfigerror' => 'OpenID Configuration Error',
				 'openidconfigerrortext' => 'The OpenID storage configuration for this wiki is invalid.  Please consult this site\'s administrator.',
				 'openidpermission' => 'OpenID permissions error',
				 'openidpermissiontext' => 'The OpenID you provided is not allowed to login to this server.',
				 'openidcancel' => 'Verification cancelled',
				 'openidcanceltext' => 'Verification of the OpenID URL was cancelled.',
				 'openidfailure' => 'Verification failed',
				 'openidfailuretext' => 'Verification of the OpenID URL failed. Error message: "$1"',
				 'openidsuccess' => 'Verification succeeded',
				 'openidsuccesstext' => 'Verification of the OpenID URL succeeded.',
				 'openidusernameprefix' => 'OpenIDUser',
				 'openidserverlogininstructions' => 'Enter your password below to log in to $3 as user $2 (user page $1).',
				 'openidtrustinstructions' => 'Check if you want to share data with $1.',
				 'openidallowtrust' => 'Allow $1 to trust this user account.',
				 'openidnopolicy' => 'Site has not specified a privacy policy.',
				 'openidpolicy' => 'Check the <a target="_new" href="$1">privacy policy</a> for more information.',
				 'openidoptional' => 'Optional',
				 'openidrequired' => 'Required',
				 'openidnickname' => 'Nickname',
				 'openidfullname' => 'Fullname',
				 'openidemail' => 'Email address',
				 'openidlanguage' => 'Language',
				 'openidnotavailable' => 'Your preferred nickname ($1) is already in use by another user.',
				 'openidnotprovided' => 'Your OpenID server did not provide a nickname (either because it can\'t, or because you told it not to).',
				 'openidchooseinstructions' => 'All users need a nickname; you can choose one from the options below.',
				 'openidchoosefull' => 'Your full name ($1)',
				 'openidchooseurl' => 'A name picked from your OpenID ($1)',
				 'openidchooseauto' => 'An auto-generated name ($1)',
				 'openidchoosemanual' => 'A name of your choice: ',
				 'openidconvertinstructions' => 'This form lets you change your user account to use an OpenID URL.',
				 'openidconvertsuccess' => 'Successfully converted to OpenID',
				 'openidconvertsuccesstext' => 'You have successfully converted your OpenID to $1.',
				 'openidconvertyourstext' => 'That is already your OpenID.',
				 'openidconvertothertext' => 'That is someone else\'s OpenID.',
				 'openidalreadyloggedin' => '<strong>User $1, you are already logged in!</strong>',
				 'tog-hideopenid' => 'Hide your <a href="http://openid.net/">OpenID</a> on your user page, if you log in with OpenID.',
				 'openidnousername' => 'No username specified.',
				 'openidbadusername' => 'Bad username specified.',
				 'openidautosubmit' => 'This page includes a form that should be automatically submitted if you have JavaScript enabled. If not, try the "Continue" button.',
				 )
);
