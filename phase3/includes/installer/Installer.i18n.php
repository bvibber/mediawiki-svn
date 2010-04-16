<?php
/*
 * Internationalization file for the install/upgrade process. None of the
 * messages used here are loaded during normal operations, only during
 * install and upgrade. So you should not put normal messages here.
 */

$messages = array();

/**
 * English
 */
$messages['en'] = array(
	'config-title'                    => 'MediaWiki $1 installation',
	'config-information'              => 'Information',
	'config-session-error'            => 'Error starting session: $1',
	'config-session-expired'          => 'Your session data seems to have expired.
Sessions are configured for a lifetime of $1.
You can increase this by setting session.gc_maxlifetime in php.ini.
Please restart the installation process.',
	'config-no-session'               => 'Your session data was lost!
Please check your php.ini and make sure session.save_path is set to an appropriate directory.',
	'config-session-path-bad'         => 'Your session.save_path ($1) seems to be invalid or unwritable.',
	'config-show-help'                => 'Help',
	'config-hide-help'                => 'Hide help',
	'config-your-language'            => 'Your language:',
	'config-your-language-help'       => 'Select a language to use during the installation process.',
	'config-wiki-language'            => 'Wiki language:',
	'config-wiki-language-help'       => 'Select the language that the wiki will predominantly be written in.',
	'config-back'                     => '← Back',
	'config-continue'                 => 'Continue →',
	'config-page-language'            => 'Language',
	'config-page-welcome'             => 'Welcome',
	'config-page-dbconnect'           => 'Connect to database',
	'config-page-upgrade'             => 'Upgrade existing',
	'config-page-dbsettings'          => 'Database settings',
	'config-page-name'                => 'Name',
	'config-page-options'             => 'Options',
	'config-page-install'             => 'Install',
	'config-page-complete'            => 'Complete!',
	'config-page-restart'             => 'Restart installation',
	'config-page-readme'              => 'Read me',
	'config-page-releasenotes'        => 'Release notes',
	'config-page-copying'             => 'Copying',
	'config-page-upgradedoc'          => 'Upgrading',
	'config-help-restart'             => 'Do you want to clear all saved data that you have entered and restart the installation process?',
	'config-restart'                  => 'Yes, restart it',
	'config-welcome'                  => 'Welcome to MediaWiki!

=== Technical data ===
Below is some technical data that you can provide to us if you need help during installation.',
	'config-copyright'                => "=== Copyright and Terms ===

$1

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but '''without any warranty'''; without even the implied warranty of '''merchantability''' or '''fitness for a particular purpose'''.
See the GNU General Public License for more details.

You should have received <doclink href=Copying>a copy of the GNU General Public License</doclink> along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. or [http://www.gnu.org/copyleft/gpl.html read it online].",
	'config-authors'                  => 'MediaWiki is Copyright © 2001-2010 by Magnus Manske, Brion Vibber, Lee Daniel Crocker, Tim Starling, Erik Möller, Gabriel Wicke, Ævar Arnfjörð Bjarmason, Niklas Laxström, Domas Mituzas, Rob Church, Yuri Astrakhan, Aryeh Gregor, Aaron Schulz, Andrew Garrett, Raimond Spekking, Alexandre Emsenhuber, Siebrand Mazeland, Chad Horohoe and others.',
	'config-sidebar'                  => "* [http://www.mediawiki.org MediaWiki home]
* [http://www.mediawiki.org/wiki/Help:Contents User's Guide]
* [http://www.mediawiki.org/wiki/Manual:Contents Administrator's Guide]
* [http://www.mediawiki.org/wiki/Manual:FAQ FAQ]",
	'config-env-good'                 => '<span class="success-message">The environment has been checked.
You can install MediaWiki.</span>', // FIXME: take span out of message.
	'config-env-bad'                  => 'The environment has been checked.
You cannot install MediaWiki.',
	'config-env-php'                  => 'PHP $1 installed',
	'config-env-latest-ok'            => 'You are installing the latest version of Mediawiki.',
	'config-env-latest-new'           => "'''Note:''' You are installing a development version of Mediawiki.",
	'config-env-latest-old'           => "'''Warning:''' You are installing an outdated version of Mediawiki.",
	'config-env-latest-help'          => 'You are installing version $1, but the latest version is $2.
You are advised to use the latest release, which can be downloaded from [http://www.mediawiki.org/wiki/Download Mediawiki.org]',
	'config-no-db'                    => 'Could not find a suitable database driver!',
	'config-no-db-help'               => 'You need to install a database driver for PHP.
The following database types are supported: $1.

If you are on shared hosting, ask your hosting provider to install a suitable database driver.
If you compiled PHP yourself, reconfigure it with a database client enabled, for example using the ./configure --with-mysql.
If you installed PHP from a Debian or Ubuntu package, then you also need install the php5-mysql module.',
	'config-have-db'                  => 'Found database drivers for: $1',
	'config-register-globals'         => "'''Warning: PHP\'s <code>[http://php.net/register_globals register_globals]</code> option is enabled.'''
'''Disable it if you can.'''
MediaWiki will work, but your server is exposed to potential security vulnerabilities.",
	'config-magic-quotes-runtime'     => "'''Fatal: [http://www.php.net/manual/en/ref.info.php#ini.magic-quotes-runtime magic_quotes_runtime] is active!'''
This option corrupts data input unpredictably; you cannot install or use MediaWiki unless this option is disabled.",
	'config-magic-quotes-sybase'      => "'''Fatal: [http://www.php.net/manual/en/ref.info.php#ini.magic-quotes-sybase magic_quotes_sybase] is active!'''
This option corrupts data input unpredictably; you cannot install or use MediaWiki unless this option is disabled.",
	'config-mbstring'                 => "'''Fatal: [http://www.php.net/manual/en/ref.mbstring.php#mbstring.overload mbstring.func_overload] is active!'''
This option causes errors and may corrupt data unpredictably; you cannot install or use MediaWiki unless this option is disabled.",
	'config-ze1'                      => "'''Fatal: [http://www.php.net/manual/en/ini.core.php zend.ze1_compatibility_mode] is active!'''
This option causes horrible bugs with MediaWiki; you cannot install or use MediaWiki unless this option is disabled.",
	'config-safe-mode'                => "'''Warning:'''
'''PHP's [http://www.php.net/features.safe-mode safe mode] is active.'''
It may cause problems, particularly if using image uploads and math support.",
	'config-xml-good'                 => 'Have XML / Latin1-UTF-8 conversion support.',
	'config-xml-bad'                  => "PHP's XML module is missing;
the wiki requires functions in this module and will not work in this configuration.
If you're running Mandrake, install the php-xml package.",
	'config-pcre'                     => 'The PCRE support module appears to be missing.
MediaWiki requires the Perl-compatible regular expression functions to work.',
	'config-memory-none'              => 'PHP is configured with no <code>memory_limit</code>',
	'config-memory-ok'                => "PHP's <code>memory_limit</code> is $1, ok.",
	'config-memory-raised'            => "PHP's <code>memory_limit</code> is $1, raised to $2.",
	'config-memory-bad'               => "'''Warning:''' PHP's <code>memory_limit</code> is $1.
This is probably too low, the installation may fail!",
	'config-xcache'                   => '[http://trac.lighttpd.net/xcache/ XCache] installed',
	'config-apc'                      => '[http://www.php.net/apc APC] installed',
	'config-eaccel'                   => '[http://eaccelerator.sourceforge.net/ eAccelerator] installed',
	'config-no-cache'                 => "'''Warning:'''
Could not find [http://eaccelerator.sourceforge.net eAccelerator],
[http://www.php.net/apc APC] or [http://trac.lighttpd.net/xcache/ XCache].
Object caching is not enabled.",
	'config-diff3-good'               => 'Found GNU diff3: <code>$1</code>.',
	'config-diff3-bad'                => 'GNU diff3 not found.',
	'config-imagemagick'              => 'Found ImageMagick: <code>$1</code>.
Image thumbnailing will be enabled if you enable uploads.',
	'config-gd'                       => 'Found GD graphics library built-in.
Image thumbnailing will be enabled if you enable uploads.',
	'config-no-scaling'               => 'Could not find GD library or ImageMagick.
Image thumbnailing will be disabled.',
	'config-dir'                      => 'Installation directory: <code>$1</code>',
	'config-uri'                      => 'Script URI path: <code>$1</code>',
	'config-no-uri'                   => "'''Error:''' Could not determine the current URI.'''
'''Installation aborted.'''",
	'config-dir-not-writable'         => "'''Error:''' Cannot write config file.
Installation aborted.

To make the directory writable on a Unix/Linux system:
<pre>cd $1
chmod a+w config</pre>",
	'config-file-extension'           => 'Installing MediaWiki with <tt>$1</tt> file extensions',
	'config-shell-locale'             => 'Detected shell locale, $1',
	'config-uploads-safe'             => 'Default uploads directory is safe from arbitrary scripts execution.',
	'config-uploads-not-safe'         => "'''Warning:''' Your default uploads directory <code>$1</code> is vulnerable to arbitrary scripts execution.
Uploads will be disabled.",
	'config-db-type'                  => 'Database type:',
	'config-db-host'                  => 'Database host:',
	'config-db-host-help'             => 'If your database server is on different server, enter the host name or IP address here.

If you are using shared web hosting, your hosting provider should give you the correct host name in their documentation.',
	'config-db-wiki-settings'         => 'Identify this wiki',
	'config-db-name'                  => 'Database name:',
	'config-db-name-help'             => 'Choose a name that identifies your wiki.
It should not contain spaces or hyphens.

If you are using shared web hosting, your hosting provider will either give you a specific database name to use, or let you create databases via a control panel.',
	'config-db-install-account'       => 'User account for installation',
	'config-db-username'              => 'Database username:',
	'config-db-password'              => 'Database password:',
	'config-db-install-help'          => 'Select the username and password that will be used to connect to the database during the installation process.',
	'config-db-account-lock'          => 'Use the same username and password during normal operation',
	'config-db-wiki-account'          => 'User account for normal operation',
	'config-db-wiki-help'             => 'Select the username and password that will be used to connect to the database during normal wiki operation.
If the account does not exist, and the installation account has sufficient privileges, this user account will be created with the minimum privileges required to operate the wiki.',
	'config-db-prefix'                => 'Database table prefix:',
	'config-db-prefix-help'           => 'If you need to share one database between multiple wikis, or between MediaWiki and another web application, you may choose to add a prefix to all the table names to avoid conflicts.
Do not use spaces or hyphens.

This field is usually left empty.',
	'config-db-charset'               => 'Database character set',
	'config-charset-mysql5-binary'    => 'MySQL 4.1/5.0 binary',
	'config-charset-mysql5'           => 'MySQL 4.1/5.0 UTF-8',
	'config-charset-mysql4'           => 'MySQL 4.0 backwards-compatible UTF-8',
	'config-charset-help'             => "'''WARNING:''' If you use '''backwards-compatible UTF-8''' on MySQL 4.1+, and subsequently back up the database with <code>mysqldump</code>, it may destroy all non-ASCII characters, irreversibly corrupting your backups!.

In '''binary mode''', MediaWiki stores UTF-8 text to the database in binary fields.
This is more efficient than MySQL's UTF-8 mode, and allows you to use the full range of Unicode characters.
In '''UTF-8 mode''', MySQL will know what character set your data is in, and can present and convert it appropriately,
but it will not let you store characters above the [http://en.wikipedia.org/wiki/Mapping_of_Unicode_character_planes Basic Multilingual Plane].",
	'config-mysql-old'                => 'MySQL $1 or later is required, you have $2.',
	'config-db-port'                  => 'Database port:',
	'config-db-schema'                => 'Schema for MediaWiki',
	'config-db-ts2-schema'            => 'Schema for tsearch2',
	'config-db-schema-help'           => 'The above schemas are usually correct.
Only change them if you know you need to.',
	'config-sqlite-dir'               => 'SQLite data directory:',
	'config-sqlite-dir-help'          => "SQLite stores data into a file in the filesystem.
This directory must be writable by the webserver.
It should '''not''' accessible via the web.",
	'config-type-mysql'               => 'MySQL',
	'config-type-postgres'            => 'PostgreSQL',
	'config-type-sqlite'              => 'SQLite',
	'config-type-oracle'              => 'Oracle',
	'config-header-mysql'             => 'MySQL settings',
	'config-header-postgres'          => 'PostgreSQL settings',
	'config-header-sqlite'            => 'SQLite settings',
	'config-header-oracle'            => 'Oracle settings',
	'config-invalid-db-type'          => 'Invalid database type',
	'config-missing-db-name'          => 'You must enter a value for "Database name"',
	'config-invalid-db-name'          => 'Invalid database name "$1".
It may only contain numbers, letters and underscores.',
	'config-invalid-db-prefix'        => 'Invalid database prefix "$1".
It may only contain numbers, letters and underscores.',
	'config-connection-error'         => '$1.

Check the host, username and password below and try again.',
	'config-invalid-schema'           => 'Invalid schema for MediaWiki "$1".
Please use only letters, numbers and underscores.',
	'config-invalid-ts2schema'        => 'Invalid schema for tsearch2 "$1".
Please use only letters, numbers and underscores.',
	'config-postgres-old'             => 'PostgreSQL $1 or later is required, you have $2.',
	'config-sqlite-name-help'         => 'Choose a name that identifies your wiki.
Do not use spaces or hyphens.
This will be used for the SQLite data file name.',
	'config-sqlite-parent-unwritable' => 'Cannot create the data directory "$1", because the parent directory "$2" is not writable by the webserver.
Please create this directory yourself, make it writable, and try again.',
	'config-sqlite-mkdir-error'       => 'Error creating the data directory "$1".
Please check the location and try again.',
	'config-sqlite-dir-unwritable'    => 'Unable to write to the given directory: $1.
Please change its permissions so that the webserver can write to it, and try again.',
	'config-sqlite-connection-error'  => '$1.

Check the data directory and database name below and try again.',
	'config-sqlite-readonly'          => 'File $1 is not writeable.',
	'config-sqlite-cant-create-db'    => 'Could not create database file $1.',
	'config-can-upgrade'              => "There are MediaWiki tables in this database.
To upgrade them to MediaWiki $1, click '''Continue'''.",
	'config-upgrade-done'             => "Upgrade complete.

You can now [$1 start using your wiki].

If you want to regenerate your LocalSettings.php file, click the button below.
This is '''not recommended''' unless you are having problems with your wiki.",
	'config-regenerate'               => 'Regenerate LocalSettings.php →',
	'config-show-table-status'        => 'SHOW TABLE STATUS query failed!',
	'config-unknown-collation'        => "'''Warning:''' Datbase is using unrecognised collation.",
	'config-db-web-account'           => 'Database account for web access',
	'config-db-web-help'              => 'Select the username and password that the web server will use to connect to the database server, during ordinary operation of the wiki.',
	'config-db-web-account-same'      => 'Use the same account as for installation',
	'config-db-web-create'            => 'Create the account if it does not already exist',
	'config-db-web-no-create-privs'   => 'The account you specified for installation does not have enough privileges to create an account.
The account you specify here must already exist.',
	'config-mysql-engine'             => 'Storage engine',
	'config-mysql-innodb'             => 'InnoDB',
	'config-mysql-myisam'             => 'MyISAM',
	'config-mysql-engine-help'        => "'''InnoDB''' is almost always the best option, since it has good concurrency support.

'''MyISAM''' may be faster in single-user or read-only installations.
MyISAM databases tend to get corrupted more often than InnoDB databases.",
	'config-mysql-charset'            => 'Database character set',
	'config-mysql-binary'             => 'Binary',
	'config-mysql-utf8'               => 'UTF-8',
	'config-mysql-charset-help'       => "In '''binary mode''', MediaWiki stores UTF-8 text to the database in binary fields.
This is more efficient than MySQL's UTF-8 mode, and allows you to use the full range of Unicode characters.

In '''UTF-8 mode''', MySQL will know what character set your data is in, and can present and convert it appropriately, but it will not let you store characters above the [http://en.wikipedia.org/wiki/Mapping_of_Unicode_character_planes Basic Multilingual Plane].",
	'config-site-name'                => 'Name of wiki:',
	'config-site-name-help'           => "This will appear in the browser's title bar and various other places.",
	'config-site-name-blank'          => 'Please enter a site name.',
	'config-project-namespace'        => 'Project namespace',
	'config-ns-generic'               => 'Project',
	'config-ns-site-name'             => 'Same as the wiki name: $1',
	'config-ns-other'                 => 'Other (please specify)',
	'config-ns-other-default'         => 'MyWiki',
	'config-project-namespace-help'   => 'Following Wikipedia\'s example, many wikis keep their policy and help pages separate from their content pages, in a "\'\'\'project namespace\'\'\'".
All page titles in this namespace start with a certain prefix, which you can specify here.
Traditionally, this prefix is derived from the name of the wiki, but it cannot contain punctuation characters such as "#" or ":".',
	'config-ns-invalid'               => 'The specified namespace "<nowiki>$1</nowiki>" is invalid.
Please specify a different project namespace',
	'config-admin-default-username'   => 'WikiSysop',
	'config-admin-box'                => 'Administrator account',
	'config-admin-name'               => 'Your name:',
	'config-admin-password'           => 'Password:',
	'config-admin-password-confirm'   => 'Password again:',
	'config-admin-help'               => 'Enter your preferred username here, for example "Joe Bloggs".
This is the name you will use to log in to the wiki.',
	'config-admin-name-blank'         => 'Please enter an administrator username.',
	'config-admin-name-invalid'       => 'The specified username "<nowiki>$1</nowiki>" is invalid.
Please specify a different username.',
	'config-admin-password-blank'     => 'Please enter a password for administrator account.',
	'config-admin-password-same'      => 'The password must not be the same as the username.',
	'config-admin-password-mismatch'  => 'The two passwords you entered do not match.',
	'config-admin-email'              => 'E-mail address:',
	'config-admin-email-help'         => 'Enter an e-mail address here to allow you to receive e-mail from other users on the wiki, reset your password, and be notified of changes to pages on your watchlist.',
	'config-subscribe'                => 'Subscribe to the [https://lists.wikimedia.org/mailman/listinfo/mediawiki-announce release announcements mailing list].',
	'config-subscribe-help'           => 'This is a low-volume mailing list used for release announcements, including important security announcements.
You should subscribe to it and update your copy of MediaWiki when new versions come out.',
	'config-almost-done'              => 'You are almost done! You can now skip the remaining configuration and install the wiki right now.',
	'config-optional-continue'        => 'Ask me more questions.',
	'config-optional-skip'            => "I'm bored already, just install the wiki.",
	'config-profile'                  => 'User rights profile',
	'config-profile-wiki'             => 'Traditional wiki',
	'config-profile-no-anon'          => 'Account creation required',
	'config-profile-fishbowl'         => 'Fishbowl',
	'config-profile-private'          => 'Private wiki',
	'config-profile-help'             => "Wikis work best when you let as many people edit them as possible.
In MediaWiki, it's easy to review the recent changes, and to revert any damage that is done by naïve or malicious users.

However, many people have found MediaWiki to be useful in a wide variety of roles, and sometimes it's not easy to convince everyone around you of the benefits of the wiki way.
So we give you the choice.

A '''traditional wiki''' allows anyone to edit, without even logging in.
Some people prefer a wiki with '''account creation required''', since this provides extra accountability (but may deter casual contributors).

A '''fishbowl''' only allows approved users to edit, but the public can view the pages, including history.
A '''private wiki''' only allows approved users to view pages, with the same group allowed to edit.

More complex user rights configurations are available after installation, see the [http://www.mediawiki.org/wiki/Manual:User_rights relevant manual entry].",
	'config-license'                  => 'Copyright and license',
	'config-license-none'             => 'No license footer',
	'config-license-gfdl-old'         => 'GNU Free Documentation License 1.2 or later',
	'config-license-gfdl-current'     => 'GNU Free Documentation License 1.3 or later',
	'config-license-pd'               => 'Public Domain',
	'config-license-cc-choose'        => 'A Creative Commons license',
	'config-license-help'             => "Many public wikis put all contributions under a [http://freedomdefined.org/Definition free license].
This helps to create a sense of community ownership and encourages long-term contribution.
It is not generally necessary for a private or corporate wiki.

If you want to be able to use text from Wikipedia, and you want Wikipedia to be able to accept text copied from your wiki, you should choose '''GNU Free Documentation License 1.2'''.
However, this license has some features which make reuse and interpretation difficult.

If Wikipedia-compatibility is not important, '''Creative Commons''' with the '''Share Alike''' option (cc-by-sa) is a good choice.",
	'config-email-settings'           => 'E-mail settings',
	'config-enable-email'             => 'Enable outbound e-mail',
	'config-enable-email-help'        => "If you want e-mail to work, [http://www.php.net/manual/en/mail.configuration.php PHP's mail settings] need to be configured correctly.
If you do not want any e-mail features, you can disable them here.",
	'config-email-user'               => 'Enable user-to-user e-mail',
	'config-email-user-help'          => 'All users to send each other e-mail, if they have enabled it in their preferences',
	'config-email-usertalk'           => 'Enable user talk page notification',
	'config-email-usertalk-help'      => 'Allow users to receive notifications on user talk page changes, if they have enabled it in their preferences',
	'config-email-watchlist'          => 'Enable watchlist notification',
	'config-email-watchlist-help'     => 'Allow users to receive notifications to their watched pages, if they have enabled it in their preferences',
	'config-email-auth'               => 'Enable e-mail authentication',
	'config-email-auth-help'          => "If this option is enabled, users have to confirm their e-mail address using a magic link sent to them whenever they set or change it, and only authenticated e-mail addresses can receive mails from other users or change notification mails.
Setting this option is '''recommended''' for public wikis because of potential abuse of the e-mail features.",
	'config-email-sender'             => 'Return e-mail address:',
	'config-email-sender-help'        => 'Enter the e-mail address to use as the return address on outbound e-mail.
This is where bounces will be sent.
Many mail servers require at least the domain name part to be valid.',
	'config-upload-settings'          => 'Images and file uploads',
	'config-upload-enable'            => 'Enable file uploads',
	'config-upload-help'              => "File uploads potentially expose your server to security risks.
For more information, read the [http://www.mediawiki.org/wiki/Manual:Security security section] in the manual.

To enable file uploads, change the mode on the <code>images</code> subdirectory under MediaWiki's root directory so that the web server can write to it.
Then enable this option.",
	'config-upload-disabled'          => 'Because your web server is configured to execute scripts from the default uploads directory, uploads will be disabled.',
	'config-upload-deleted'           => 'Directory for deleted files :',
	'config-upload-deleted-help'      => 'Choose a directory in which to archive deleted files.
Ideally, this should not be accessible from the web.',
	'config-logo'                     => 'Logo URL:',
	'config-logo-help'                => "MediaWiki's default skin includes space for a 135x135 pixel logo in the top left corner.
Upload an image of the appropriate size, and enter the URL here.

If you do not want a logo, leave this box blank.",
	'config-cc-error'                 => 'The Creative Commons license chooser gave no result.
Please enter the license name manually.',
	'config-cc-again'                 => 'Pick again...',
	'config-cc-not-chosen'            => 'Please choose which Creative Commons license you want and click "proceed".',
	'config-advanced-settings'        => 'Advanced configuration',
	'config-cache-options'            => 'Settings for object caching',
	'config-cache-help'               => 'Object caching is used to improve the speed of MediaWiki by caching frequently used data.
Medium to large sites are highly encouraged to enable this, and small sites will see benefits as well.',
	'config-cache-none'               => 'No caching.
No functionality is removed, but speed may be impacted.',
	'config-cache-accel'              => 'PHP object caching (APC, eAccelerator or XCache)',
	'config-cache-memcached'          => 'Use Memcached (requires additional setup and configuration)',
	'config-cache-db'                 => 'Cache data into the database',
	'config-cache-anything'           => 'MediaWiki will attempt to cache data anywhere possible, except Memcached',
	'config-memcached-servers'        => 'Memcached servers',
	'config-memcached-help'           => 'List of IP addresses to use for Memcached.
Should be separated with commas and specify the port to be used (eg: 1.2.3.4:56, 7.8.9.10:11)',
	'config-extensions'               => 'Extensions',
	'config-extensions-help'          => 'The extensions listed above were automatically detected in your <code>./extensions</code> directory.

They may require additional configuration, but you can enable them now',
	'config-install-step-done'        => 'done',
	'config-install-step-failed'      => 'Failed',
	'config-install-extensions'       => 'Including extensions',
	'config-install-database'         => 'Setting up database',
	'config-install-pg-schema-failed' => 'Tables creation failed.
Make sure that the user "$1" can write to the schema "$2".',
	'config-install-tables'           => 'Creating tables',
	'config-install-interwiki-sql'    => 'Could not find file interwiki.sql',
	'config-install-secretkey'        => 'Generating secret key',
	'config-insecure-secretkey'       => 'Warning: Unable to create secure $wgSecretKey.
Consider changing it manually.',
	'config-install-sysop'            => 'Creating administrator user account',
	'config-install-localsettings'    => 'Creating LocalSettings.php',
	'config-install-localsettings-unwritable' => 'Warning: could not write LocalSettings.php.
Please create it yourself, using the following text:',
	'config-install-done'             => "'''Congratulations''', you have successfully installed MediaWiki.

You will need to move it from <code>./config/LocalSettings.php</code> to <code>./LocalSettings.php</code> in order for MediaWiki to begin working.

[$1 Link to your wiki]",
	'config-install-done-moved'       => "'''Congratulations''', you have successfully installed MediaWiki.

[$1 Link to your wiki]",
);
