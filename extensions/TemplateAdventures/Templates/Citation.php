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
			$authorArea = $this->createWriterSection ( $this->dAuthors, $this->dAuthorLinks, $this->dAuthorTruncate );
			if ( $this->dCoAuthors != null )
				$authorArea .= $this->getSeparator( 'author' ) . $this->dCoAuthors;
			if ( $this->dDate != null ) {
				$authorArea .= wfMsg ( 'ta-citeauthordate', $this->dDate);
				if ( $this->dYearNote != null ) 
					$authorArea .= wfMsg ( 'ta-citeauthoryearnote', $this->dYearNote );
			}
			$this->mOutput .= $authorArea;
		# editors
		} elseif ( count ( $this->dEditors ) > 1 ) {
			$editorArea = $this->createWriterSection ( $this->dEditors, $this->dEditorLinks, $this->dEditorTruncate );
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
		# included work title
		if ( $this->notNull( $this->dWorkTitle['includedwork'] ) && ( $this->notNull( $this->dPeriodical ) || $this->notNull( $this->dWorkTitle['transitalic'] ) || $this->notNull( $this->dWorkTitle['transtitle'] ) ) ) {
			# I am no way certain this is a correct copy of the following logic:
			# {{#if: {{{IncludedWorkTitle|}}}{{#if:{{{Periodical|}}}||{{#if:{{{TransItalic|}}}||{{{TransTitle|}}}}}}} | ... | ... }}
			if ( $authorArea != '' || $editorArea != '' ) {
				$this->mOutput .= $this->getSeparator( 'section' );
			}
			# let's get the url
			if ( $this->dWorkLink['includedwork'] != null ) {
				$url = $this->dWorkLink['includedwork'];
			} else {
				if ( $this->dWorkLink['url'] != null ) {
					$url = $this->dWorkLink['url'];
				} else {
					# some explain to me what exactly the following is supposed to mean:
					# <!-- Only link URL if to a free full text - as at PubMedCentral (PMC)-->
					# |{{#ifexpr:{{#time: U}} > {{#time: U | {{{Embargo|2001-10-10}}} }}
					if ( $this->dPubMed['pmc'] != null ) {
						$url = wfMsg ( 'ta-pubmed-url', $this->dPubMed['pmc'] );
					}
				}
			}
			# and now the title
			if ( $this->dPeriodical != null ) {
				$tmp = str_replace( "'", '&#39;', $this->dWorkTitle['includedwork']);
				$title = "''$tmp''";
			} else {
				$tmp = ( $this->dWorkTitle['includedwork'] != null
					? $this->dWorkTitle['includedwork']
					: '' );
				if ( $this->dWorkTitle['transtitle'] ) {
					if ( $tmp != '' )
						$tmp .= ' ';
					$tmp .= wfMsg( 'ta-transtitle-render', $this->dWorkTitle['transtitle'] );
				}
				$title = $tmp;
			}
			$this->mOutput .= $this->makeLink ( $url, $title );
		}
		# place, but only if different from publication place.
		if ( $this->notNull( $this->dPlace ) 
			&& $this->dPlace != $this->dPublication['place'] 
			&& ( 
				$this->notNull ( $authorArea ) 
				|| $this->notNull ( $editorArea ) 
				|| $this->notNull ( $this->dWorkTitle['includedwork'] ) 
			) 
		) {
			$this->mOutput .= $this->getSeparator ( 'section' ) . ' ' . wfMsg ( 'ta-writtenat', $this->dPlace );
		}
		# editor of complication... eerrr...
		# TODO: we'll do this later...

		# periodicals
		# TODO: I'll get on it!
	}

	/**
	 * Create the section for authors, editors, etc. in a neat similar function.
	 *
	 * @param $writers Authors, editors, etc. array
	 * @param $links Their links if any
	 * @param $truncate When to truncate the amount of data.
	 * @return The created area.
	 */
	private function createWriterSection ( $writers, $links, $truncate ) {
		$area = '';
		$n = 1;
		foreach ( $writers as $i => $writer ) {
			if ( $i == 0 )
				continue;
			if ( $i > 1 && $truncate <= $i ) {
				$area .= wfMsg( "ta-etal" );
				break;
			}
			if ( $n == count($writers)-1 && $i != 1 )
				$area .= $this->getSeparator( 'ampersand' );
			elseif ( $n > 1 )
				$area .= $this->getSeparator( 'author' );
			$tmp = '';
			if ( $writer[0] ) {
				if ( $writer[1][1] == null )
					continue;
				$tmp .= $writer[1][1];
				if ( $writer[1][0] != null )
					$tmp .= $this->getSeparator( 'name' ) . $writer[1][0];
			} else {
				# maybe we shan't support no surname/given name structure
				# in the future, but we'll leave it like this for now.
				$tmp .= $writer[1][1];
			}
			if ( isset ( $links[$i] ) )
				$tmp = "[{$links[$i]} $tmp]";
			$area .= $tmp;
			$n++;
		}
		return $area;
	}

	private function makeLink ( $url, $title ) {
		if ( !$url )
			return $title;
		return "[$url $title]";
	}

	private function notNull ( $check ) {
		if ( $check == null && $check == '' )
			return false;
		return true;
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

	private function addOtherStringValue ( $name, $value ) {
		switch ( $name[0] ) {
			case 'url':
				$this->dWorkLink['url'] = $value;
				break;
			case 'title':
				$this->dWorkTitle['title'] = $value;
				break;
			case 'includedworktitle':
				$this->dWorkTitle['includedwork'] = $value;
				break;			
		}
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
			case 'url':
			case 'title':
			case 'pmc':
			case 'includedworktitle':
			case 'periodical':
			case 'transitalic':
			case 'transtitle':
				$this->addOtherStringValue( $name, $value );
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
		global $wgContLang;

		static $magicWords = null;
		if ( $magicWords === null ) {
			$magicWords = new MagicWordArray( array(
				'ta_cc_author', 'ta_cc_authorgiven',
				'ta_cc_authorsurname', 'ta_cc_authorlink',
				'ta_cc_coauthors',
				'ta_cc_editor', 'ta_cc_editorgiven',
				'ta_cc_editorsurname', 'ta_cc_editorlink',
				'ta_cc_url', 'ta_cc_title', 'ta_cc_pmc',
				'ta_cc_includedworktitle', 'ta_cc_periodical',
				'ta_cc_transitalic', 'ta_cc_transtitle',
			) );
		}

		$num = preg_replace("@.*?([0-9]+)$@is", '\1', $value);
		if (is_numeric( $num ))
			$name = preg_replace("@(.*?)[0-9]+$@is", '\1', $value);
		else {
			$name = $value;
			$num = null;
		}

		$name = $wgContLang->lc( $name );

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
