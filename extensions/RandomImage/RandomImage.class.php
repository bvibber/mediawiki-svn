<?php

/**
 * Class file for the RandomImage extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
class RandomImage {

	private $parser = null;
	
	private $width = false;
	private $float = false;
	private $caption = '';
	
	private $choices = array();
	
	/**
	 * Constructor
	 *
	 * @param Parser $parser Parent parser
	 * @param array $options Initial options
	 * @param string $caption Caption text
	 */
	public function __construct( $parser, $options, $caption ) {
		$this->parser = $parser;
		$this->caption = $caption;
		$this->setOptions( $options );
	}
	
	/**
	 * Extract applicable options from tag attributes
	 *
	 * @param array $options Tag attributes
	 */
	protected function setOptions( $options ) {
		if( isset( $options['size'] ) ) {
			$size = intval( $options['size'] );
			if( $size > 0 )
				$this->width = $size;			
		}
		if( isset( $options['float'] ) ) {
			$float = strtolower( $options['float'] );
			// TODO: Use magic words instead
			if( in_array( $float, array( 'left', 'right', 'centre' ) ) )
				$this->float = $float;
		}
		if( isset( $options['choices'] ) ) {
			$choices = explode( '|', $options['choices'] );
			if( count( $choices ) > 0 )
				$this->choices = $choices;
		}
	}
	
	/**
	 * Render a random image
	 *
	 * @return string
	 */
	public function render() {
		$title = $this->pickImage();
		if( $title instanceof Title ) {
			$file = wfFindFile( $title );
			if( $file->exists() ) {
				return $this->parser->parse(
					$this->buildMarkup( $file ),
					$this->parser->getTitle(),
					$this->parser->getOptions(),
					false,
					false
				)->getText();
			}
		}
		return '';
	}
	
	/**
	 * Prepare image markup for the given image
	 *
	 * @param File $image Image to render
	 * @return string
	 */
	protected function buildMarkup( $image ) {
		$parts[] = $image->getTitle()->getPrefixedText();
		if( $this->width !== false ) {
			$parts[] = "{$this->width}px";
			$parts[] = 'frame';
		} else {
			$parts[] = 'thumb';
		}
		if( $this->float )
			$parts[] = $this->float;
		$parts[] = $this->getCaption( $image->getTitle() );
		return '[[' . implode( '|', $parts ) . ']]';
	}

	/**
	 * Obtain caption text for a given image
	 *
	 * @param Title $title Image page to take caption from
	 * @return string
	 */
	protected function getCaption( $title ) {
		if( !$this->caption ) {
			if( $title->exists() ) {
				$text = Revision::newFromTitle( $title )->getText();
				if( preg_match( '!<randomcaption>(.*?)</randomcaption>!i', $text, $matches ) ) {
					$this->caption = $matches[1];
				} elseif( preg_match( "!^(.*?)\n!i", $text, $matches ) ) {
					$this->caption = $matches[1];
				} else {
					$this->caption = $text;
				}
			} else {
				$this->caption = '';
			}
		}
		return $this->caption;
	}
	
	/**
	 * Select a random image
	 *
	 * @return Title
	 */
	protected function pickImage() {
		if( count( $this->choices ) > 0 ) {
			return $this->pickFromChoices();
		} else {
			$pick = $this->pickFromDatabase();
			if( !$pick instanceof Title )
				$pick = $this->pickFromDatabase();
			return $pick;
		}
	}
	
	/**
	 * Select a random image from the choices given
	 *
	 * @return Title
	 */
	protected function pickFromChoices() {
		$name = count( $this->choices ) > 1
			? $this->choices[ array_rand( $this->choices ) ]
			: $this->choices[0];
		return Title::makeTitleSafe( NS_IMAGE, $name );
	}
	
	/**
	 * Select a random image from the database
	 *
	 * @return Title
	 */
	protected function pickFromDatabase() {
		wfProfileIn( __METHOD__ );
		$dbr = wfGetDB( DB_SLAVE );
		list( $table, $conds, $opts ) = $this->getExtraSelectOptions( $dbr );
		$res = $dbr->select(
			$table,
			array(
				'page_namespace',
				'page_title',
			),
			array(
				'page_namespace' => NS_IMAGE,
				'page_is_redirect' => 0,
				'page_random > ' . $dbr->addQuotes( wfRandom() ),
			) + $conds,
			__METHOD__,
			array(
				'ORDER BY' => 'page_random',
				'LIMIT' => 1,
			) + $opts
		);
		wfProfileOut( __METHOD__ );
		if( $dbr->numRows( $res ) > 0 ) {
			$row = $dbr->fetchObject( $res );
			$dbr->freeResult( $res );
			return Title::makeTitleSafe( $row->page_namespace, $row->page_title );
		}
		return null;
	}
	
	/**
	 * Get various options for database selection
	 *
	 * @param Database $dbr Database being queried
	 * @return array
	 */
	protected function getExtraSelectOptions( $dbr ) {
		global $wgRandomImageStrict;
		if( $wgRandomImageStrict ) {
			list( $image, $page ) = $dbr->tableNamesN( 'image', 'page' );
			$ind = $dbr->useIndexClause( 'page_random' );
			return array(
				"{$page} {$ind} LEFT JOIN {$image} ON img_name = page_title",
				array(
					'img_major_mime' => 'image',
				),
				array(),
			);
		} else {
			return array(
				'page',
				array(),
				array(
					'USE INDEX' => 'page_random',
				),
			);
		}
	}
	
	/**
	 * Parser hook callback
	 *
	 * @param string $input Tag input
	 * @param array $args Tag attributes
	 * @param Parser $parser Parent parser
	 * @return string
	 */
	public static function renderHook( $input, $args, $parser ) {
		global $wgRandomImageNoCache;
		if( $wgRandomImageNoCache )
			$parser->disableCache();
		$random = new RandomImage( $parser, $args, $input );
		return $random->render();
	}
	
	/**
	 * Strip <randomcaption> tags out of page text
	 *
	 * @param Parser $parser Calling parser
	 * @param string $text Page text
	 * @return bool
	 */
	public static function stripHook( $parser, &$text ) {
		$text = preg_replace( '!</?randomcaption>!i', '', $text );
		return true;
	}
	
}