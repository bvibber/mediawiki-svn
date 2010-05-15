<?php

class Citation extends TemplateAdventureBasic {

	private $dSeparators = array(    # separators between names, items, etc.
		'section'    => ',',
		'end'        => '.',
		'author'     => '&#059;',
		'name'       => ',',
		'authorlast' => '&#32;&amp;',
		'beforepublication' => ':',
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
		'note'         => null,
	);
	private $dWorkLink = array(      # data related to the link
		'url'          => null,
		'originalurl'  => null,
		'includedwork' => null,
	);
	private $dAt = null;             # wherein the source
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
		# init data
		$this->dSeparators['section']    = wfMsg ( 'ta-citesep-section' );
		$this->dSeparators['author']     = wfMsg ( 'ta-citesep-author' );
		$this->dSeparators['name']       = wfMsg ( 'ta-citesep-name' );
		$this->dSeparators['authorlast'] = wfMsg ( 'ta-citesep-authorlast' );
		$this->dSeparators['beforepublication'] = wfMsg ( 'ta-citesep-beforepublication' );
		$this->dSeparators['end']        = wfMsg ( 'ta-citesep-end' );
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
			if ( $this->notNull ( $this->dCoAuthors ) )
				$authorArea = wfMsg ( 'ta-citecoauthors', $authorArea, $this->getSeparator( 'author' ), $this->dCoAuthors );
			if ( $this->notNull ( $this->dDate ) ) {
				$authorArea = wfMsg ( 'ta-citeauthordate', $authorArea, $this->dDate);
				if ( $this->notNull ( $this->dYearNote ) ) 
					$authorArea = wfMsg ( 'ta-citeauthoryearnote', $authorArea, $this->dYearNote );
			}
			$this->mOutput .= $authorArea . $this->getSeparator ( 'section' );
		# editors
		} elseif ( count ( $this->dEditors ) > 1 ) {
			$editorArea = $this->createWriterSection ( $this->dEditors, $this->dEditorLinks, $this->dEditorTruncate );
			if ( count ( $this->dEditors ) > 2 )
				$editorArea = wfMsg ( 'ta-citeeditorsplural', $editorArea );
			else
				$editorArea = wfMsg ( 'ta-citeeditorssingular', $editorArea );
			$editorArea .= $this->getSeparator ( 'section' );
			if ( $this->notNull ( $this->dDate ) ) {
				$editorArea = wfMsg ( 'ta-citeauthordate', $editorArea, $this->dDate);
				if ( $this->notNull ( $this->dYearNote ) ) 
					$editorArea .= wfMsg ( 'ta-citeauthoryearnote', $editorArea, $this->dYearNote );
			}
			$this->mOutput .= $editorArea . $this->getSeparator ( 'section' );
		}
		# included work title
		if ( $this->notNull( $this->dWorkTitle['includedwork'] ) 
			&& ( $this->notNull( $this->dPeriodical['name'] ) 
				|| $this->notNull( $this->dWorkTitle['transitalic'] ) 
				|| $this->notNull( $this->dWorkTitle['transtitle'] ) ) ) {
			# let's get the url
			if ( $this->notNull ( $this->dWorkLink['includedwork'] ) ) {
				$url = $this->dWorkLink['includedwork'];
			} else {
				if ( $this->notNull ( $this->dWorkLink['url'] ) ) {
					$url = $this->dWorkLink['url'];
				} else {
					# some explain to me what exactly the following is supposed to mean:
					# <!-- Only link URL if to a free full text - as at PubMedCentral (PMC)-->
					# |{{#ifexpr:{{#time: U}} > {{#time: U | {{{Embargo|2001-10-10}}} }}
					if ( $this->notNull ( $this->dPubMed['pmc'] ) ) {
						$url = wfMsg ( 'ta-citepubmed-url', $this->dPubMed['pmc'] );
					}
				}
			}
			# and now the title
			if ( $this->notNull ( $this->dPeriodical['name'] ) ) {
				$tmp = $this->clean( $this->dWorkTitle['includedwork'] );
				$title = wfMsg ( 'ta-citeincludedworktitle', $tmp );
			} else {
				$tmp = ( $this->notNull ( $this->dWorkTitle['includedwork'] )
					? $this->dWorkTitle['includedwork']
					: '' );
				if ( $this->dWorkTitle['transtitle'] ) {
					if ( $tmp != '' )
						$tmp .= ' ';
					$tmp .= wfMsg( 'ta-citetranstitle-render', $this->dWorkTitle['transtitle'] );
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
			$this->mOutput .= wfMsg ( 'ta-citewrittenat', $this->dPlace ) . $this->getSeparator ( 'section' );
		}
		# editor of complication... eerrr...
		# TODO: we'll do this later...

		# periodicals
		if ( $this->notNull ( $this->dPeriodical['name'] ) ) {
			$perArea = '';
			if ( $this->notNull ( $this->dOther ) )
				$perArea .= wfMsg ( 'ta-citeother', $this->dOther );
			# make the link!
			if ( $this->notNull ( $this->dWorkTitle['title'] ) || $this->notNull ( $this->dWorkTitle['transtitle'] ) ) {
				# let's get the url
				if ( $this->notNull ( $this->dWorkTitle['includedwork'] ) ) {
					if ( $this->notNull ( $this->dWorkLink['includedwork'] ) ) {
						if ( $this->notNull ( $this->dWorkLink['url'] ) ) {
							$url = $this->dWorkLink['url'];
						} else {
						# some explain to me what exactly the following is supposed to mean:
						# <!-- Only link URL if to a free full text - as at PubMedCentral (PMC)-->
						# |{{#ifexpr:{{#time: U}} > {{#time: U | {{{Embargo|2001-10-10}}} }}
							if ( $this->dPubMed['pmc'] != null ) {
								$url = wfMsg ( 'ta-citepubmed-url', $this->dPubMed['pmc'] );
							}				
						}
					}
				} else {
					if ( $this->notNull ( $this->dWorkLink['url'] ) ) {
						$url = $this->dWorkLink['url'];
					} else {
					# some explain to me what exactly the following is supposed to mean:
					# <!-- Only link URL if to a free full text - as at PubMedCentral (PMC)-->
					# |{{#ifexpr:{{#time: U}} > {{#time: U | {{{Embargo|2001-10-10}}} }}
						if ( $this->dPubMed['pmc'] != null ) {
							$url = wfMsg ( 'ta-citepubmed-url', $this->dPubMed['pmc'] );
						}				
					}
				}
				# and now the title
				$tmp = $this->notNull ( $this->dWorkTitle['title'] )
					? $this->dWorkTitle['title']
					: '';
				if ( $this->notNull ( $this->dWorkTitle['transtitle'] ) ) {
					if ( $tmp != '' )
						$tmp .= ' ';
					$tmp .= wfMsg ( 'ta-citetranstitle-render', $this->dWorkTitle['transtitle'] );
				}
				$title = "\"$tmp\"";
				$perArea .= $this->makeLink ( $url, $title ) . $this->getSeparator ( 'section' );
				if ( $this->notNull ( $this->dWorkTitle['note'] ) ) {
					$perArea .= $this->dWorkTitle['note'] . $this->getSeparator ( 'section' );
				}
			}
			$this->mOutput .= $perArea;
		}
		# language
		if ( $this->notNull ( $this->dLanguage ) ) {
			$this->mOutput .= wfMsg ( 'ta-citeinlanguage', $this->dLanguage );
		}
		# format
		if ( $this->notNull ( $this->dFormat ) ) {
			$this->mOutput .= wfMsg ( 'ta-citeformatrender', $this->dFormat );
		}
		# more periodical!
		if ( $this->notNull ( $this->dPeriodical['name'] ) ) {
			$newPerArea = '';
			$newPerArea .= wfMsg ( 'ta-citeperiodical', $this->clean ( $this->dPeriodical['name'] ) );
			if ( $this->notNull ( $this->dSeries ) ) {
				$newPerArea .= wfMsg ( 'ta-citeseries', $this->dSeries );
			}
			if ( $this->notNull ( $this->dPublication['place'] ) ) {
				if ( $this->notNull ( $this->dPublisher ) ) {
					$newPerArea .= wfMsg ( 'ta-citepublicationplaceandpublisher', $this->dPublication['place'], $this->dPublisher );
				} else {
					$newPerArea .= wfMsg ( 'ta-citepublicationplace', $this->dPublication['place'] );
				}
			}
			if ( $this->notNull ( $this->dVolume ) ) {
				$newPerArea .= wfMsg ( 'ta-citevolumerender', $this->dVolume );
				if ( $this->notNUll ( $this->dIssue ) ) {
					$newPerArea .= wfMsg ( 'ta-citeissuerender', $this->dIssue );
				}
			} else {
				if ( $this->notNUll ( $this->dIssue ) ) {
					$newPerArea .= wfMsg ( 'ta-citeissuerender', $this->dIssue );
				}
			}
			if ( $this->notNull ( $this->dAt ) ) {
				$newPerArea .= wfMsg ( 'ta-citeatrender', $this->dAt );
			}
			$newPerArea .= $this->getSeparator ( 'section' );
			# now we get to the title!  Exciting stuff!
			if ( $this->notNull ( $this->dWorkTitle['title'] )
				|| $this->notNull ( $this->dWorkTitle['transitalic'] ) ) {
				# let's get the url
				if ( $this->notNull ( $this->dWorkTitle['includedwork'] ) ) {
					if ( $this->notNull ( $this->dWorkLink['includedwork'] ) ) {
						if ( $this->notNull ( $this->dWorkLink['url'] ) ) {
							$url = $this->dWorkLink['url'];
						} else {
						# some explain to me what exactly the following is supposed to mean:
						# <!-- Only link URL if to a free full text - as at PubMedCentral (PMC)-->
						# |{{#ifexpr:{{#time: U}} > {{#time: U | {{{Embargo|2001-10-10}}} }}
							if ( $this->dPubMed['pmc'] != null ) {
								$url = wfMsg ( 'ta-citepubmed-url', $this->dPubMed['pmc'] );
							}				
						}
					}
				} else {
					if ( $this->notNull ( $this->dWorkLink['url'] ) ) {
						$url = $this->dWorkLink['url'];
					} else {
					# some explain to me what exactly the following is supposed to mean:
					# <!-- Only link URL if to a free full text - as at PubMedCentral (PMC)-->
					# |{{#ifexpr:{{#time: U}} > {{#time: U | {{{Embargo|2001-10-10}}} }}
						if ( $this->dPubMed['pmc'] != null ) {
							$url = wfMsg ( 'ta-citepubmed-url', $this->dPubMed['pmc'] );
						}				
					}
				}
				# and now the title
				$tmp = $this->notNull ( $this->dWorkTitle['title'] )
					? $this->dWorkTitle['title']
					: '';
				if ( $this->notNull ( $this->dWorkTitle['transitalic'] ) ) {
					if ( $tmp != '' )
						$tmp .= ' ';
					$tmp .= wfMsg ( 'ta-citetranstitle-render', $this->dWorkTitle['transitalic'] );
				}
				$tmp = $this->clean ( $tmp );
				$title = wfMsg ( 'ta-citeperiodicaltitle', $tmp );
				$newPerArea .= $this->makeLink ( $url, $title );
			}
			# may change this into some if () statements though,
			# it is easier to write this, but it also means that all of the
			# second input is actually evaluated, even if it contains nothing.
			$newPerArea .= $this->addNotNull ( $this->dWorkTitle['type'], 
				wfMsg ( 'ta-citetitletyperender', $this->dWorkTitle['type'] ) . $this->getSeparator ( 'section' ) );
			$newPerArea .= $this->addNotNull ( $this->dSeries, 
				wfMsg ( 'ta-citeseries', $this->dSeries ) . $this->getSeparator ( 'section' ) );
			$newPerArea .= $this->addNotNull ( $this->dVolume, 
				wfMsg ( 'ta-citevolumerender', $this->dVolume ) . $this->getSeparator ( 'section' ) );
			$newPerArea .= $this->addNotNull ( $this->dOther, 
				wfMsg ( 'ta-citeother', $this->dOther ) );
			$newPerArea .= $this->addNotNull ( $this->dEdition, 
				wfMsg ( 'ta-citeeditionrender', $this->dEdition ) . $this->getSeparator ( 'section' ) );
			$newPerArea .= $this->addNotNull ( $this->dPublication['place'], 
				wfMsg ( 'ta-citepublication', $this->dPublication['place'] ) );
			if ( $this->notNull ( $this->dPublisher ) ) {
				if ( $this->notNull ( $this->dPublication['place'] ) ) {
					$sep = $this->getSeparator ( 'beforepublication' );
				} else {
					$sep = $this->getSeparator ( 'section' );
				}
				$newPerArea .= $sep . wfMsg ( 'ta-citepublisherrender', $this->dPublisher );
			}
			$this->mOutput .= $newPerArea . $this->getSeparator ( 'section' );
		}
		# date if no author/editor
		if ( $authorArea == '' && $editorArea == '' ) {
			if ( $this->notNull ( $this->dDate ) ) {
				$this->mOutput .= wfMsg ( 'ta-citeauthordate', $this->dDate );
				if ( $this->notNull ( $this->dYearNote ) ) {
					$this->mOutput .= wfMsg ( 'ta-citeauthoryearnote', $this->dYearNote );
				}
				$this->mOutput .= $this->getSeparator ( 'section' );
			}
		}
		# publication date
		if ( $this->notNull ( $this->dPublication['date'] ) 
			&& $this->dPublication['date'] != $this->dDate ) {
			if ( isset ( $this->dEditors[1] ) ) {
				if ( isset ( $this->dAuthors[1] ) ) {
					$this->mOutput .= wfMsg ( 'ta-citepublicationdate', $this->dPublication['date'] );
				} else {
					$this->mOutput .= wfMsg ( 'ta-citepublished', $this->dPublication['date'] );
				}
			} else {
				if ( $this->notNull ( $this->dPeriodical['name'] ) ) {
					$this->mOutput .= wfMsg ( 'ta-citepublicationdate', $this->dPublication['date'] );
				} else {
					$this->mOutput .= wfMsg ( 'ta-citepublished', $this->dPublication['date'] );
				}
			}
			$this->mOutput .= $this->getSeparator ( 'section' );
		}
		# page within included work
		if ( !$this->notNull ( $this->dPeriodical['name'] ) 
			&& $this->notNull ( $this->dAt ) ) {
			$this->mOutput .= wfMsg ( 'ta-citeatseparated', $this->dAt );
		}
		# doi
		# TODO:  I'll do this code later:
		# {{{Sep|,}}}&#32;{{citation/identifier  |identifier=doi |input1={{{DOI|}}}  |input2={{{DoiBroken|}}} }}

		# misc identifier
		# TODO: Awh shit.
		# #if: {{{ID|}}}
        #  |{{
        #     #if: {{{Surname1|}}}{{{EditorSurname1|}}}{{{IncludedWorkTitle|}}}{{{Periodical|}}}{{{Title|}}}{{{TransItalic|}}}
        #     |{{{Sep|,}}}&#32;{{{ID}}}
        #     |{{{ID}}}
        #   }}

		# more identifiers:
		# TODO: Do all this crap.
		/*
		{{
<!--============  ISBN ============-->
  #if: {{{ISBN|}}}
  |{{{Sep|,}}}&#32;{{citation/identifier  |identifier=isbn |input1={{{ISBN|}}} }}
}}{{
<!--============  ISSN ============-->
  #if: {{{ISSN|}}}
  |{{{Sep|,}}}&#32;{{citation/identifier  |identifier=issn |input1={{{ISSN|}}} }}
}}{{
<!--============  OCLC ============-->
  #if: {{{OCLC|}}}
  |{{{Sep|,}}}&#32;{{citation/identifier  |identifier=oclc |input1={{{OCLC|}}} }}
}}{{
<!--============  PMID ============-->
  #if: {{{PMID|}}}
  |{{{Sep|,}}}&#32;{{citation/identifier  |identifier=pmid |input1={{{PMID|}}} }}
}}{{
<!--============  PMC ============-->
  #if: {{{PMC|}}}
  |{{
     #if: {{{URL|}}}
     |{{{Sep|,}}}&#32;{{citation/identifier  |identifier=pmc |input1={{{PMC|}}} }}
     |{{only in  print|{{{Sep|,}}}&#32;{{citation/identifier  |identifier=pmc |input1={{{PMC|}}} }} }}<!--Should  only display by default in print-->
   }}
}}{{
<!--============ BIBCODE ============-->
  #if: {{{Bibcode|}}}
  |{{{Sep|,}}}&#32;{{citation/identifier  |identifier=bibcode |input1={{{Bibcode|}}} }}
}}
		*/

		# archive data, etc.
		# TODO: Yeah, O_O

		# URL and accessdate
		if ( $this->notNull ( $this->dWorkLink['url'] )
			|| $this->notNull ( $this->dWorkLink['includedwork'] ) ) {
			if ( $this->notNull ( $this->dWorkTitle['title'] )
				|| $this->notNull ( $this->dWorkTitle['includedwork'] )
				|| $this->notNull ( $this->dWorkTitle['transtitle'] ) ) {
				$this->mOutput .= $this->printOnly ( 
						( $this->notNull ( $this->dWorkLink['includedwork'] )
							? $this->dWorkLink['includedwork']
							: $this->dWorkLink['url'] ) );
			} else {
				$this->mOutput .=
					( $this->notNull ( $this->dWorkLink['includedwork'] )
						? $this->dWorkLink['includedwork']
						: $this->dWorkLink['url'] );
			}
			if ( $this->notNull ( $this->dAccessDate ) ) {
				if ( $this->getSeparator ( 'section' ) == '.' )
					$tmp = wfMsg ( 'ta-citeretrievedupper', $this->getSeparator ( 'section' ), $this->dAccessDate );
				else
					$tmp = wfMsg ( 'ta-citeretrievedlower', $this->getSeparator ( 'section' ), $this->dAccessDate );
				$this->mOutput .= wfMsg ( 'ta-citeaccessdatespan', $tmp ); 
			}
		}

		# layman stuff
		# TODO

		# quote
		# TODO

		# some other shit nobody cares about.
		# COinS?  waaaaat
		# TODO
		$this->mOutput = trim($this->mOutput);
		if ( $this->mOutput[strlen($this->mOutput)-1] == $this->getSeparator ( 'section', false ) ) {
			$this->mOutput[strlen($this->mOutput)-1] = $this->getSeparator ( 'end', false );
		} elseif ( $this->mOutput[strlen($this->mOutput)-1] != $this->getSeparator ( 'end', false ) ) {
			$this->mOutput .= $this->getSeparator ( 'end', false );
		}
	}

	/**
	 * Surround the string in a span tag that tells the css not to render it
	 * unless it for print.
	 *
	 * @param $string
	 * @return $string
	 */
	private function printOnly ( $string ) {
		return wfMsg ( 'ta-citeprintonlyspan', $string );
	}

	private function addNotNull ( $check, $add ) {
		if ( $this->notNull ( $check ) )
			return $add;
		return '';
	}

	/**
	 * Clean a string for characters that might confuse the parser.
	 *
	 * @param $value The string to be cleaned.
	 * @return The cleaned string.
	 */
	private function clean ( $value ) {
		return str_replace ( "'", '&#39;', $value );
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
				$area = wfMsg( "ta-citeetal", $area );
				break;
			}
			if ( $n == count($writers)-1 && $i != 1 )
				$area .= $this->getSeparator( 'authorlast' );
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
	 * @param $name Name of separator; section, author, name or authorlast
	 * @param $addSpace Whether to add a space at the end; default true
	 * @return $separator Blank if none found.
	 */
	private function getSeparator ( $name, $addSpace=true ) {
		if ( !isset($this->dSeparators[$name]) )
			return '';
		$sep = $this->dSeparators[$name];
		if ( $addSpace )
			return $sep.' ';
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
			case 'periodical':
				$this->dPeriodical['name'] = $value;
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
