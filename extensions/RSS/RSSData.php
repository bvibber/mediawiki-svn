<?php

class RSSData {
	public $etag;
	public $last_modified;
	public $ERROR;
	public $xml;
	public $items;

	function __construct( $resp ) {
		$this->xml = new DOMDocument;
		$this->xml->loadXML( $resp->getContent() );
		$h = $resp->getResponseHeader( 'ETag' );
		$this->etag = $h;
		$h = $resp->getResponseHeader( 'Last-Modified' );
		$this->last_modified = $h;

		$xpath = new DOMXPath( $this->xml );
		$items = $xpath->evaluate( '/rss/channel/item' );

		foreach( $items as $item ) {
			$bit = array();
			foreach( $item->childNodes as $n ) {
				$name = $this->rssTokenToName( $n->nodeName );
				if( $name != null ) {
					$bit[$name] = $n->nodeValue;
				}
			}
			$this->items[] = $bit;
		}
	}

	function rssTokenToName( $n ) {
		switch( $n ) {
			case 'dc:date':
				return 'date';
				# parse "2010-10-18T18:07:00Z"
			case 'pubDate':
				return 'date';
				# parse RFC date
			case 'dc:creator':
				return 'author';
			case 'title':
				return 'title';
			case 'content:encoded':
				return 'encodedContent';

			case 'slash:comments':
			case 'slash:department':
			case 'slash:section':
			case 'slash:hit_parade':
			case 'feedburner:origLink':
			case 'wfw:commentRss':
			case 'comments':
			case 'category':
				return null;

			default:
				return $n;
		}
	}
}