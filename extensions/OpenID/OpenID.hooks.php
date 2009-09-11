<?php

class OpenIDHooks {
	public static function onSpecialPage_initList( &$list ) {
		global $wgOpenIDOnly, $wgOpenIDClientOnly;

		if ( $wgOpenIDOnly ) {
			$list['Userlogin'] = array( 'SpecialRedirectToSpecial', 'Userlogin', 'OpenIDLogin' );
			# Used in 1.12.x and above
			$list['CreateAccount'] = array( 'SpecialRedirectToSpecial', 'CreateAccount', 'OpenIDLogin' );
		}

		# Special pages are added at global scope; remove server-related ones
		# if client-only flag is set
		$addList = array( 'Login', 'Finish', 'Convert' );
		if ( !$wgOpenIDClientOnly ) {
			$addList[] = 'Server';
			$addList[] = 'XRDS';
		}

		foreach ( $addList as $sp ) {
			$list['OpenID' . $sp] = 'SpecialOpenID' . $sp;
		}

		return true;
	}

	# Hook is called whenever an article is being viewed
	public static function onArticleViewHeader( &$article, &$outputDone, &$pcache ) {
		global $wgOut, $wgOpenIDClientOnly;

		$nt = $article->getTitle();

		// If the page being viewed is a user page,
		// generate the openid.server META tag and output
		// the X-XRDS-Location.  See the OpenIDXRDS
		// special page for the XRDS output / generation
		// logic.

		if ( $nt && $nt->getNamespace() == NS_USER && strpos( $nt->getText(), '/' ) === false ) {
			$user = User::newFromName( $nt->getText() );
			if ( $user && $user->getID() != 0 ) {
				$openid = SpecialOpenID::getUserUrl( $user );
				if ( isset( $openid ) && strlen( $openid ) != 0 ) {
					global $wgOpenIDShowUrlOnUserPage;

					if ( $wgOpenIDShowUrlOnUserPage == 'always' ||
						( $wgOpenIDShowUrlOnUserPage == 'user' && !$user->getOption( 'openid-hide' ) ) )
					{
						global $wgOpenIDLoginLogoUrl;

						$url = SpecialOpenID::OpenIDToUrl( $openid );
						$disp = htmlspecialchars( $openid );
						$wgOut->setSubtitle( "<span class='subpages'>" .
											"<img src='$wgOpenIDLoginLogoUrl' alt='OpenID' />" .
											"<a href='$url'>$disp</a>" .
											"</span>" );
					}
				} else {
					# Add OpenID data iif its allowed
					if ( !$wgOpenIDClientOnly ) {
						$st = SpecialPage::getTitleFor( 'OpenIDServer' );
						$wgOut->addLink( array( 'rel' => 'openid.server',
											    'href' => $st->getFullURL() ) );
						$wgOut->addLink( array( 'rel' => 'openid2.provider',
											    'href' => $st->getFullURL() ) );
						$rt = SpecialPage::getTitleFor( 'OpenIDXRDS', $user->getName() );
						$wgOut->addMeta( 'http:X-XRDS-Location', $rt->getFullURL() );
						header( 'X-XRDS-Location: ' . $rt->getFullURL() );
					}
				}
			}
		}

		return true;
	}

	public static function onPersonalUrls( &$personal_urls, &$title ) {
		global $wgHideOpenIDLoginLink, $wgUser, $wgLang, $wgOut, $wgOpenIDOnly;

		if ( !$wgHideOpenIDLoginLink && $wgUser->getID() == 0 ) {
			wfLoadExtensionMessages( 'OpenID' );
			$wgOut->addHeadItem( 'openidloginstyle', self::loginStyle() );
			$sk = $wgUser->getSkin();
			$returnto = $title->isSpecial( 'Userlogout' ) ?
			  '' : ( 'returnto=' . $title->getPrefixedURL() );

			$personal_urls['openidlogin'] = array(
				'text' => wfMsg( 'openidlogin' ),
				'href' => $sk->makeSpecialUrl( 'OpenIDLogin', $returnto ),
				'active' => $title->isSpecial( 'OpenIDLogin' )
			);

			if ( $wgOpenIDOnly ) {
				# remove other login links
				foreach ( array( 'login', 'anonlogin' ) as $k ) {
					if ( array_key_exists( $k, $personal_urls ) ) {
						unset( $personal_urls[$k] );
					}
				}
			}
		}

		return true;
	}

	public static function onGetPreferences( $user, &$preferences ) {
		wfLoadExtensionMessages( 'OpenID' );
		
		$preferences['openid-hide'] =
			array(
				'type' => 'toggle',
				'section' => 'openid',
				'label-message' => 'openid-pref-hide',
			);

		$preferences['openid-update-userinfo-on-login'] =
			array(
				'type' => 'toggle',
				'section' => 'openid',
				'label-message' => 'openid-pref-update-userinfo-on-login',
			);

		return true;
	}
	public static function onLoadExtensionSchemaUpdates() {
		global $wgDBtype, $wgExtNewTables;

		$base = dirname( __FILE__ );

		if ( $wgDBtype == 'mysql' ) {
			$wgExtNewTables[] = array( 'user_openid', "$base/openid_table.sql" );
		} else if ( $wgDBtype == 'postgres' ) {
			$wgExtNewTables[] = array( 'user_openid', "$base/openid_table.pg.sql" );
		}

		return true;
	}

	private static function loginStyle() {
		global $wgOpenIDLoginLogoUrl;
			return <<<EOS
		<style type='text/css'>
		li#pt-openidlogin {
		  background: url($wgOpenIDLoginLogoUrl) top left no-repeat;
		  padding-left: 20px;
		  text-transform: none;
		}
		</style>
EOS;
	}
}
