<?php

/**
 * Script to automatically generate invoices and send them to pesky 
 * administrators once per month, thus leaving programmers to be engrossed 
 * in their work on a continuous basis.
 *
 * TODO: have it also pay my tax and cook my food
 */

if ( php_sapi_name() != 'cli' ) exit;

class Invoice {
	var $conf;

	function __construct() {
		include( dirname(__FILE__) . '/conf.php' );
		$_vars = get_defined_vars();
		$this->conf = (object)array();
		foreach ( $_vars as $name => $value ) {
			if ( $name[0] == '_' ) {
				continue;
			}
			$this->conf->$name = $value;
		}
	}

	function timeInMonths( $time ) {
		return idate('Y', $time) * 12 + idate( 'm', $time );
	}

	function addMonths( $time, $months ) {
		return strtotime( "+$months month", $time );
	}

	function formatDate( $time ) {
		return date( $this->conf->dateFormat, $time );
	}

	function generateInvoice( $num = false ) {
		$epochStart = strtotime( $this->conf->epochStart );
		if ( $num === false ) {
			// Calculate number of months since invoiceStart
			$num = $this->timeInMonths( time() ) - $this->timeInMonths( $epochStart ) + 1;
		}
		$periodStart = $this->addMonths( $epochStart, $num - 1 );
		$periodEnd = strtotime( "-1 day", $this->addMonths( $periodStart, 1 ) );
		$replacements = array(
			'$periodStart' => $this->formatDate( $periodStart ),
			'$periodEnd' => $this->formatDate( $periodEnd ),
		);
		$encItems = array();
		foreach ( $this->conf->items as $item ) {
			$encItem = array();
			foreach ( $item as $key => $value ) {
				$encItem[$key] =  htmlspecialchars( strtr( $value, $replacements ) );
			}
			$encItems[] = $encItem;
		}

		$tplData = array(
			'address' => nl2br( htmlspecialchars( $this->conf->address ) ),
			'phone' => htmlspecialchars( $this->conf->phone ),
			'soldTo' => nl2br( htmlspecialchars( $this->conf->soldTo ) ),
			'invoiceNumber' => htmlspecialchars( $num ),
			'date' => $this->formatDate( time() ),
			'items' => $encItems,
			'currency' => $this->conf->currency
		);
		return array(
			'text' => $this->runTemplate( 'invoice.tpl', $tplData ),
			'subject' => strtr( $this->conf->emailSubject, $replacements )
		);
	}

	function runTemplate( $name, $tplData ) {
		extract( $tplData );
		ob_start();
		include( dirname(__FILE__).'/'.$name );
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	function mailInvoice( $num = false ) {
		$invoice = $this->generateInvoice( $num );

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "From: {$this->conf->emailFrom}\r\n";
		if ( $this->conf->bccTo ) {
			$headers .= "Bcc: {$this->conf->bccTo}\r\n";
		}
		if ( $this->conf->ccTo ) {
			$headers .= "Cc: {$this->conf->ccTo}\r\n";
		}

		mail( $this->conf->emailTo, $invoice['subject'], $invoice['text'], $headers );
	}
}

$invoice = new Invoice;
#$num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : false;
$num = isset( $argv[1] ) ? intval( $argv[1] ) : false;
$invoice->mailInvoice( $num );
