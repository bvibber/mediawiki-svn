<?php

/**
 * Class file for the RandomImage extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
class RandomImage {

	private $parser = null;
	
	private $width = 'thumb';
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
				$this->width = "{$size}px";			
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
	 * @param File $image Image
	 * @return string
	 */
	protected function buildMarkup( $image ) {
		$parts[] = $image->getTitle()->getPrefixedText();
		$parts[] = $this->width;
		if( $this->float )
			$parts[] = $this->float;
		if( $this->caption )
			$parts[] = $this->caption;
		return '[[' . implode( '|', $parts ) . ']]';
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
		$res = $dbr->select(
			'page',
			array(
				'page_namespace',
				'page_title',
			),
			array(
				'page_namespace' => NS_IMAGE,
				'page_is_redirect' => 0,
				'page_random > ' . $dbr->addQuotes( wfRandom() ),
			),
			__METHOD__,
			array(
				'USE INDEX' => 'page_random',
				'ORDER BY' => 'page_random',
				'LIMIT' => 1,
			)
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
	 * Parser hook callback
	 *
	 * @param string $input Tag input
	 * @param array $args Tag attributes
	 * @param Parser $parser Parent parser
	 * @return string
	 */
	public static function hook( $input, $args, $parser ) {
		global $wgRandomImageNoCache;
		if( $wgRandomImageNoCache )
			$parser->disableCache();
		$random = new RandomImage( $parser, $args, $input );
		return $random->render();
	}
	
}