<?php

class Citation extends TemplateAdventureBasic {

	private $dSeparators = array(    # separators between names, items, etc.
		'section'   => ',',
		'regular'   => ',&#32;',
		'author'    => '&#059;&#32;',
		'name'      => ',&#32;',
		'ampersand' => '&#32;&amp;&#32;',
	);
	private $dAuthorTruncate = 8;    # the amount of authors it should display,
	                                 # if truncated, 'et al' will be used instead.
	private $dAuthors = array(null);     # array of authors
	private $dAuthorLinks = array(null); # array of authorlinks (tied to authors).
	private $dCoAuthors = null;          # coauthors is as far as I understand it
	                                     # just a string, but prove me wrong!
	private $dEditors = array(null);     # array of editors
	private $dEditorLinks = array(null); # array of editorlinks (tied to editors).
	                                     # they all contain 'junk' to avoid the
	                                     # usage of [0].
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

	/**
	 * Our construct function.
	 */
	public function __construct( $parser, $frame, $args ) {
		parent::__construct($parser, $frame, $args);
		$this->readOptions( );
		$this->parseData();
	}

	/**
	 * Render the data after the data have been read.
	 */
	public function render() {
		$this->mOutput = '';
		# authors
		if ( count( $this->dAuthors ) > 1 ) {
			$authorArea = '';
			$n = 1;
			foreach ( $this->dAuthors as $i => $author ) {
				if ( $i == 0 )
					continue;
				if ( $i > 1 && $this->dAuthorTruncate <= $i ) {
					$authorArea .= wfMsg( "ta-etal" );
					break;
				}
				if ( $n == count($this->dAuthors)-1 && $i != 1 )
					$authorArea .= $this->getSeparator( 'ampersand' );
				elseif ( $n > 1 )
					$authorArea .= $this->getSeparator( 'author' );
				$tmp = '';
				if ( $author[0] ) {
					if ( $author[1][1] == null )
						continue;
					$tmp .= $author[1][1];
					if ( $author[1][0] != null )
						$tmp .= $this->getSeparator( 'name' ) . $author[1][0];
				} else {
					# maybe we shan't support no surname/given name structure
					# in the future, but we'll leave it like this for now.
					$tmp .= $author[1][1];
				}
				if ( isset ( $this->dAuthorLinks[$i] ) )
					$tmp = "[{$this->dAuthorLinks[$i]} $tmp]";
				$authorArea .= $tmp;
				$n++;
			}
			if ( $this->dCoAuthors != null )
				$authorArea .= $this->getSeparator( 'author' ) . $this->dCoAuthors;
			if ( $this->dDate != null ) {
				$authorArea .= wfMsg ( 'ta-citeauthordate', $this->dDate);
				if ( $this->dYearNote != null ) 
					$authorArea .= wfMsg ( 'ta-citeauthoryearnote', $this->dYearNote );
			}
			$this->mOutput .= $authorArea;
		} elseif ( count ( $this->dEditors ) > 1 ) {
			$editorArea = '';
			$n = 1;
			foreach ( $this->dEditors as $i => $editor ) {
				if ( $i == 0 )
					continue;
				if ( $i > 1 && $this->dEditorTruncate <= $i ) {
					$editorArea .= wfMsg( "ta-etal" );
					break;
				}
				if ( $n == count($this->dEditors)-1 && $i != 1 )
					$editorArea .= $this->getSeparator( 'ampersand' );
				elseif ( $n > 1 )
					$editorArea .= $this->getSeparator( 'author' );
				$tmp = '';
				if ( $editor[0] ) {
					if ( $editor[1][1] == null )
						continue;
					$tmp .= $editor[1][1];
					if ( $editor[1][0] != null )
						$tmp .= $this->getSeparator( 'name' ) . $editor[1][0];
				} else {
					# maybe we shan't support no surname/given name structure
					# in the future, but we'll leave it like this for now.
					$tmp .= $editor[1][1];
				}
				if ( isset ( $this->dEditorLinks[$i] ) )
					$tmp = "[{$this->dEditorLinks[$i]} $tmp]";
				$editorArea .= $tmp;
				$n++;
			}
			if ( count ( $this->dEditors ) > 2 )
				$editorArea .= wfMsg ( 'ta-editorsplural' );
			else
				$editorArea .= wfMsg ( 'ta-editorssingular' );
			$editorArea .= $this->getSeparator ( 'section' );
			if ( $this->dDate != null ) {
				$editorArea .= wfMsg ( 'ta-citeauthordate', $this->dDate);
				if ( $this->dYearNote != null ) 
					$editorArea .= wfMsg ( 'ta-citeauthoryearnote', $this->dYearNote );
			}
			$this->mOutput .= $editorArea;
		}
	}

	/**
	 * This function should in the future do some wfMsg() magic to check if
	 * they are using a set separator message or just using a default one.
	 *
	 * @param $name Name of separator; regular, author or ampersand
	 * @return $separator Blank if none found.
	 */
	private function getSeparator ( $name ) {
		if ( !isset($this->dSeparators[$name]) )
			return '';
		$sep = $this->dSeparators[$name];
		return $sep;
	}

	/**
	 * This function parses the data the given to it during the readOptions()
	 * run.  Basically to disregard data and such that has been found to be
	 * outside the allowed logic of this 'template'.
	 */
	private function parseData() {
		# check $dAuthors for only 'given' names.
		$tmpAuthors = array(null);
		foreach( $this->dAuthors as $i => $author ) {
			if ( $i == 0 )
				continue;
			if ( $author[0] && $author[1][1] == null )
				continue;
			$tmpAuthors[$i] = $author;
		}
		$this->dAuthors = $tmpAuthors;
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

	private function addCoAuthors ( $name, $value ) {
		$this->dCoAuthors = $value;
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
		} # let's not permit them to add authors/editors/etc. without a number
	}

	/**
	 * Checks whether the data provided is a known option.
	 *
	 * @param $var The variable
	 * @param $value The value
	 * @return True if option, false if not.
	 */
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
			case 'coauthors':
				$this->addCoAuthors( $name, $value );
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
				return false;
		}
		return true;
	}

	/**
	 * This one parses the variable name given to optionParse to figure out
	 * whether this is a known parameter to this template.
	 *
	 * @param $value The parameter.
	 * @return The parameter's true name (for localisations purposes, etc.) as
	 *         well as its numeral found with it or false if not.
	 */
	protected function parseOptionName( $value ) {

		static $magicWords = null;
		if ( $magicWords === null ) {
			$magicWords = new MagicWordArray( array(
				'ta_cc_author', 'ta_cc_authorgiven',
				'ta_cc_authorsurname', 'ta_cc_authorlink',
				'ta_cc_coauthors',
				'ta_cc_editor', 'ta_cc_editorgiven',
				'ta_cc_editorsurname', 'ta_cc_editorlink',
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
