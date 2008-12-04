<?php
/**
 * LinkOpenID.i18n.php - Internationalisation for LinkOpenID
 *
 * @author Michael Holzt <kju@fqdn.org>
 * @copyright 2008 Michael Holzt
 * @license GNU General Public License 2.0
 */

$messages = array();

/** English
 * @author Michael Holzt <kju@fqdn.org>
 */
$messages['en'] = array(
	# for Special:Version
	'linkopenid-desc' => 'Allow users to link their account to an external OpenID',

	# for Special:Preferences
	'linkopenid-prefs' => 'OpenID',
	'linkopenid-prefstext-pre' => 'If you have a OpenID from an external provider you can specify it here.
This allows you to use your userpage as an OpenID as well.',
	'linkopenid-prefstext-openid' => 'Your OpenID:',
	'linkopenid-prefstext-v1url' => 'Server-URL for OpenID Version 1.1:',
	'linkopenid-prefstext-v2url' => 'Server-URL for OpenID Version 2:'
);

/** German
 * @author Michael Holzt <kju@fqdn.org>
 */
$messages['de'] = array(
	# for Special:Version
	'linkopenid-desc' => 'Erlaubt Benutzern eine externe OpenID ihrem Account zuzuordnen',

	# for Special:Preferences
	'linkopenid-prefs' => 'OpenID',
	'linkopenid-prefstext-pre' => 'Wenn Sie eine OpenID bei einem externen Anbieter besitzen, können Sie diese hier angeben.
Dies ermöglicht Ihnen die alternative Nutzung Ihrer Benutzerseite als OpenID.',
	'linkopenid-prefstext-openid' => 'Ihre OpenID:',
	'linkopenid-prefstext-v1url' => 'Server-URL für OpenID Version 1.1:',
	'linkopenid-prefstext-v2url' => 'Server-URL für OpenID Version 2:'
);
