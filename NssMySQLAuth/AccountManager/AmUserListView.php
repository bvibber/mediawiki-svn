<?php

class AmUserListView {
	function execute() {
		global $wgOut, $wgUser;
		
		$users = NssUser::fetchNames();
		$title = SpecialPage::getTitleFor( 'AccountManager' );
		$sk = $wgUser->getSkin();
		
		$wgOut->addHtml( Xml::element( 'ul', array(
			'id' => 'nss-user-listview'
		) ) . "\n" );
		
		foreach ( $users as $name ) {
			$wgOut->addHtml( "\t<li>" . 
				$sk->link( $title, $name, /* html attribs */ array(), 
					array( 'user' => $name ), 'known'
				) . "</li>\n" );
		}
		
		$wgOut->addHtml( "</ul>\n" );
	}
}