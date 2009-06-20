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
		header( 'Content-Type: application/octet-stream' );
		echo $result;
		return true;
	}
	
	static $formats = array('csv');
	
	function formatCsv( $data ) {
		$props = NssProperties::getAll();
		$users = NssUser::fetchAll();
		
		$result = '';
		foreach ( $data as $username => $line ) {
			$dataline = array();
			foreach ( $props as $name ) {
				$field = isset( $line[$name] ) ? $line[$name] : '';
				
				if ( !$field && in_array( $name, array( 'username', 'home', 'active', 'email' ) ) ) {
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
			$result .= implode( ',', $dataline )."\r\n";
		}
		return $result;
	}
}
