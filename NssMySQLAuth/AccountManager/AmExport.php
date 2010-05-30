<?php

class AmExport {
	function execute( $format ) {
		if ( !in_array( strtolower( $format ), self::$formats ) )
			return false;
		
		$data = NssProperties::getAllUsers();
		
		$result = call_user_func( array( $this,
				'format'.ucfirst( strtolower( $format ) ) 
			), $data );
		
		global $wgOut;
		$wgOut->disable();
		wfResetOutputBuffers();
		echo $result;
		return true;
	}
	
	static $formats = array('csv', 'csvexcel' );
	
	function formatCsvexcel( $data ) {
		return $this->formatCsv( $data, ';' );
	}
	function formatCsv( $data, $separator = ',' ) {
		$props = NssProperties::getAll();
		$users = NssUser::fetchAll();
		
		$result = '';
		foreach ( $data as $username => $line ) {
			$dataline = array();
			foreach ( $props as $name ) {
				$field = isset( $line[$name] ) ? $line[$name] : '';
				
				if ( !$field && in_array( $name, array( 'username', 'home', 'active', 'email' ) ) ) {
					if ( !isset( $users[$username] ) )
						continue;
					$field = $users[$username]->get( $name );
				}
				
				$escape = false;
				if ( strpos( $field, '"' ) !== false ) {
					$field = str_replace( '"', '""' , $field );
					$escape = true;
				}
				if ( strpos( $field, ',') !== false )
					$escape = true;
				if ( !$field )
					$escape = true;
				
				if ( $escape )
					$dataline[] = '"'.$field.'"';
				else
					$dataline[] = $field;
			}
			$result .= implode( $separator, $dataline )."\r\n";
		}
		
		header( "Content-Disposition: inline;filename*=utf-8'en'export.csv" );
		return $result;
	}
	
	public static function setSubtitle() {
		global $wgOut;
		$title = SpecialPage::getTitleFor( 'AccountManager' );
		$wgOut->setSubtitle( wfMsgExt( 'am-download-subtitle', 
			array( 'parse', 'replaceafter' ),
			Xml::element( 'a', array( 'href' => $title->getLocalURL( 'export=csv') ),
				wfMsg( 'am-download-cvs' )
			),
			Xml::element( 'a', array( 'href' => $title->getLocalURL( 'export=csvexcel') ),
				wfMsg( 'am-download-cvsexcel' )
			)
		) );
	}
}
