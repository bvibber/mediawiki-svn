<?php

class CitationCore extends TemplateAdventureBasic {

	private $dSeparators = array(    # separators between names, items, etc.
		'regular'   => ',&#32;',
		'author'    => ',&#32;',
		'ampersand' => '&#32;&amp;&#32;',
	);
	private $dAuthorTruncate = 8;    # the amount of authors it should display,
	                                 # if truncated, 'et al' will be used instead.
	private $dAuthors = array();     # array of authors
	private $dAuthorLinks = array(); # array of authorlinks (tied to authors).
	private $dCoAuthors = array();   # array of coauthors
	private $dEditors = array();     # array of editors
	private $dEditorLinks = array(); # array of editorlinks (tied to editors).
	private $dAuthorBlock = null;    # authorblock of em length
	private $dDate = null;           # the date set
	private $dAccessDate = null;     # date accessed
	private $dYear = null;           # year of authorship or publication
	private $dYearNote = null;       # note to accompany the year
	private $dWorkTitle = array(     # data related to the title
		'title'        => null,
		'transtitle'   => null,      # translated title (if original title is
		                             # in a foreign language).
	    'transitalic'  => null,      # translated title in italic
		'includedwork' => null,
		'type'         => null,      # the title type
	);
	private $dWorkLink = array(      # data related to the link
		'url'          => null,
		'originalurl'  => null,
		'includedwork' => null,
		'at'           => null,      # wherein the source
	);
	private $dArchived = array(      # information about its archiving if archived
		'url'          => null,
		'date'         => null,
	);
	private $dPubMed = array(
		'pmc'          => null,      # PMC link
		'pmid'         => null,      # PMID
	);
	private $dSeries = null;         # whether it is part of a series
	private $dQuote = null;          # quote
	private $dPublisher = null;      # publisher
	private $dPublication = array(   # publication
		'data'         => null,
		'place'        => null,
	);
	private $dPlace = null;          # place of writing
	private $dPeriodical = array(    # name of periodical, journal or magazine.
	    'name'  => null,             # ensures it will be rendered as such
	    'issue' => null,
	    'issn'  => null,
	);
	private $dLibrary = null;        # library id
	private $dISBN = null;           # isbn number
	private $dLay = array(           # an article of the same publication, but
	                                 # written in more layman friendly fashion.
		'data'    => null,
		'summary' => null
	);
	private $dLanguage = null;       # language of the publication
	private $dId = null;             # misc id
	private $dEdition = null;        # edition
	private $dDoi = array(           # digital object identifier
		'id'     => null,
		'broken' => null,            # date broken
	);
	private $dBibcode = null;        # bibcode id
	private $dOther = null;          # other stuff

	public function parse() {
		$this->readOptions( );
		$this->mOutput = "Not implemented!";
	}

	private function addEditorLink( $name, $value ) {
		if ( $name[1] == null )
			return;
		$this->dEditorLinks[$name[1]] = $value;
	}

	private function addEditor( $name, $value ) {
		$this->appendEditorData ( $name[1], $value );
	}

	private function addEditorSurname( $name, $value ) {
		$this->appendEditorData ( $name[1], array ( null, $value ) );
	}

	private function addEditorGivenName ( $name, $value ) {
		$this->appendEditorData ( $name[1], array ( $value, null ) );
	}

	private function appendEditorData( $num, $name ) {
		$this->appendWriterData( $this->dEditors, $num, $name );
	}

	private function addAuthorLink( $name, $value ) {
		if ( $name[1] == null )
			return;
		$this->dAuthorLinks[$name[1]] = $value;
	}

	private function addAuthor( $name, $value ) {
		$this->appendAuthorData ( $name[1], $value );
	}

	private function addAuthorSurname( $name, $value ) {
		$this->appendAuthorData ( $name[1], array ( null, $value ) );
	}

	private function addAuthorGivenName ( $name, $value ) {
		$this->appendAuthorData ( $name[1], array ( $value, null ) );
	}

	private function appendAuthorData( $num, $name ) {
		$this->appendWriterData( $this->dAuthors, $num, $name );
	}

	private function appendWriterData( &$array, $num, $name ) {
		$split = is_array( $name );
		if ( $num != null ) {
			if ( isset($array[$num]) && $array[$num][0] ) {
				if ( $name[0] != null )
					$array[$num][1][0] = $name[0];
				else
					$array[$num][1][1] = $name[1];
			} else {
				$array[$num] = array (
					$split,
					$name
				);
			}
		} else {
			$array[] = array (
				$split,
				$name
			);
		}
	}

	protected function optionParse( $var, $value ) {
		$name = self::parseOptionName( $var );
		switch ( $name[0] ) {
			case 'author':
				$this->addAuthor( $name, $value );
				break;
			case 'authorsurname':
				$this->addAuthorSurname( $name, $value );
				break;
			case 'authorgiven':
				$this->addAuthorGivenName( $name, $value );
				break;
			case 'authorlink':
				$this->addAuthorLink( $name, $value );
				break;
			case 'editor':
				$this->addEditor( $name, $value );
				break;
			case 'editorsurname':
				$this->addEditorSurname( $name, $value );
				break;
			case 'editorgiven':
				$this->addEditorGivenName( $name, $value );
				break;
			case 'editorlink':
				$this->addEditorLink( $name, $value );
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

		$num = preg_replace("@.*?([0-9]+)$@is", '\1', $value);
		if (is_numeric( $num ))
			$name = preg_replace("@(.*?)[0-9]+$@is", '\1', $value);
		else {
			$name = $value;
			$num = null;
		}

		if ( $name = $magicWords->matchStartToEnd( trim($name) ) ) {
			return array(
				str_replace( 'ta_cc_', '', $name ),
				$num,
			);
		}
		
		# blimey, so not an option!?
		return array( false, null );
	}
}
