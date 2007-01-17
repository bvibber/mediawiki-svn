<?php

/**
 * Book information driver for Amazon
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
class BookInformationAmazon implements BookInformationDriver {

	private $title = '', $author = '', $publisher = '', $year = '';
	private $purchase = '';

	/**
	 * Submit a request to the information source and
	 * store the results in the object's state
	 *
	 * @param string $isbn ISBN to obtain information for
	 * @return bool Success
	 */
	public function submitRequest( $isbn ) {
		global $wgBookInformationService;
		if( isset( $wgBookInformationService['accesskeyid'] ) ) {
			$this->isbn = $isbn;
			$aki = $wgBookInformationService['accesskeyid'];
			$loc = isset( $wgBookInformationServices['locale'] )
					? $wgBookInformationServices['locale']
					: 'us';
			$uri = self::buildRequestURI( $aki, $isbn );
			if( ( $xml = Http::get( $uri ) ) !== false ) {
				return $this->parseResponse( $xml );
			} else {
				return false;
			}			
		} else {
			return false;
		}
	}
	
	/**
	 * Build the URI to an Amazon Web Service request
	 *
	 * @param string $base URL base (locale-dependent, etc.)
	 * @param string $aki Access Key ID
	 * @param string $isbn ISBN to be queried
	 * @return string
	 */
	private static function buildRequestURI( $aki, $isbn ) {
		$bits[] = 'Service=AWSECommerceService';
		$bits[] = 'AWSAccessKeyId=' . urlencode( $aki );
		$bits[] = 'Operation=ItemSearch';
		$bits[] = 'SearchIndex=Books';
		$bits[] = 'Power=asin:' . urlencode( $isbn );
		return 'http://webservices.amazon.com/onca/xml'
				. '?' . implode( '&', $bits );
	}
	
	/**
	 * Parse an XML response from the service and extract
	 * the information we require
	 *
	 * @param string $response XML response
	 * @return bool Success
	 */
	private function parseResponse( $response ) {
		try {
			$xml = new SimpleXMLElement( $response );
			if( is_object( $xml ) && $xml instanceof SimpleXMLElement ) {
				$items =& $xml->Items[0];
				if( $items->Request[0]->IsValid == 'True' && isset( $items->Item[0] ) ) {
					$item =& $items->Item[0]->ItemAttributes[0];
					$this->title = $item->Title;
					$this->author = $item->Author;
					$this->publisher = $item->Manufacturer;
					$this->purchase = $items->Item[0]->DetailPageURL;
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}		
		} catch( $ex ) {
			return false;
		}
	}
	
	public function getTitle() {
		return $this->title ? $this->title : false;
	}
	
	public function getAuthor() {
		return $this->author ? $this->author : false;
	}
	
	public function getPublisher() {
		return $this->publisher ? $this->publisher : false;
	}
	
	public function getYear() {
		return false;
	}
	
	public function getPurchaseLink() {
		return '<a href="' . htmlspecialchars( $this->purchase ) . '">Amazon.com</a>';
	}
	
	public function getProviderLink() {
		return '<a href="http://www.amazon.com/webservices">Amazon Web Services</a>';
	}

}

?>