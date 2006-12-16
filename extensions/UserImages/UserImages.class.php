<?php

/**
 * User image gallery generator
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
 
class UserImagesGallery {

	private $parser = NULL;
	private $user = NULL;
	
	private $caption = '';
	private $limit = 10;
	
	public function __construct( $args, &$parser ) {
		$this->parser =& $parser;
		$this->loadOptions( $args );
		$this->setUser( $args );
	}
	
	private function loadOptions( $options ) {
		if( isset( $options['caption'] ) )
			$this->title = $options['caption'];
		if( isset( $options['limit'] ) )
			$this->limit = min( $options['limit'], 50 );
	}
	
	private function setUser( $options ) {
		if( isset( $options['user'] ) ) {
			$this->user = User::newFromName( $options['user'] );
		}
	}
	
	public function render() {
		if( is_object( $this->user ) ) {
			$this->user->load();
			if( $this->user->getId() > 0 ) {
				$images = $this->getImages();
				if( count( $images ) > 0 ) {
					$gallery = new ImageGallery();
					$gallery->setParsing( true );
					$gallery->setCaption( $this->getCaption() );
					$gallery->useSkin( $this->parser->mOptions->getSkin() );
					foreach( $images as $image ) {
						$object = new Image( Title::makeTitleSafe( NS_IMAGE, $image->img_name ) );
						$object->loadFromFile();
						$object->loadFromRow( $image );
						$gallery->add( $object );
					}
					return $gallery->toHtml();
				} else {
					# no images
					return '<p>' . wfMsgForContent( 'userimages-noimages', $this->user->getName() ) . '</p>';
				}
			} else {
				# no such user
				return '<p>' . wfMsgForContent( 'nosuchusershort', $this->user->getName() ) . '</p>';
			}
		} else {
			# invalid username
			return '<p>' . wfMsgForContent( 'userimages-noname' ) . '</p>';
		}
	}
	
	private function getImages() {
		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'image', '*', array( 'img_user' => $this->user->getId() ), __METHOD__, array( 'ORDER BY' => 'img_timestamp', 'LIMIT' => $this->limit ) );
		if( $res && $dbr->numRows( $res ) > 0 ) {
			$images = array();
			while( $row = $dbr->fetchObject( $res ) )
				$images[] = $row;
			$dbr->freeResult( $res );
			return $images;
		} else {
			return array();
		}
	}
	
	private function getCaption() {
		return $this->caption ? $this->caption : wfMsgForContent( 'userimages-caption', $this->user->getName() );
	}
	
}

?>