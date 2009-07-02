<?php

class AmUserListView {
	function execute() {
		global $wgOut, $wgUser;
		
		$actives = NssUser::fetchByActive();

		$title = SpecialPage::getTitleFor( 'AccountManager' );
		$sk = $wgUser->getSkin();
		
		foreach ( $actives as $active => $users ) {
			$wgOut->addHtml( Xml::element( 'h2', null, $active ). 
			Xml::openElement( 'ul', array(
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
}