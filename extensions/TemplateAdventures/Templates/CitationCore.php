<?php

class CitationCore extends TemplateAdventureBasic {

	private $dAuthors = array();

	public function parse() {
		$this->readOptions( );
		$this->mOutput = "''{$this->dAuthors[0]}''";
	}

	protected function optionParse( $var, $value ) {
		switch ( $name = self::parseOptionName( $var ) ) {
			case 'author':
				$this->dAuthors[] = $value;
				break;
			default:
				# Wasn't an option after all
				return $arg instanceof PPNode_DOM
					? trim( $this->mFrame->expand( $arg ) )
					: $arg;
		}
		return false;
	}

	protected function parseOptionName( $value ) {

		static $magicWords = null;
		if ( $magicWords === null ) {
			$magicWords = new MagicWordArray( array(
				'ta_cc_author'
			) );
		}

		if ( $name = $magicWords->matchStartToEnd( trim($value) ) ) {
			return str_replace( 'ta_cc_', '', $name );
		}
		
		# blimey, so not an option!?
		return false;
	}
}
