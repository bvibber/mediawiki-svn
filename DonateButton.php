<?php

class DonateButton extends UnlistedSpecialPage {
	/* Members */

	private $mSharedMaxAge = 600;
	private $mMaxAge = 600;

	/* Functions */ 

	function execute () {
		global $wgOut;
		$wgOUt->disable();
		$this->sendHeaders();
		$js = $this->getJsOutput();
	}

	public function sharedMaxAge() {
        	return $this->mSharedMaxAge();
    	}   

	public function maxAge() {
		return $this->mMaxAge();
    	}   

	// Set the caches 
	private function sendHeaders() {
        	$smaxage = $this->sharedMaxAge();
        	$maxage = $this->maxAge();
        	$public = ( session_id() == '' );

        	header( "Content-type: text/javascript; charset=utf-8" );
        	if ( $public ) { 
            		header( "Cache-Control: public, s-maxage=$smaxage, max-age=$maxage" );
        	} else {
        		header( "Cache-Control: private, s-maxage=0, max-age=$maxage" );
       		 }	   
    	}  	

	public function getJsOutput() {
		global $wgFundraiserPortalTemplates;
	
		foreach( $wgFundraiserPortalTemplates as $template => $weight ) {
			$buttons[$template] = $this->getButtonText( $template );
		}

        	return $this->getScriptFunctions() .
			'wgFundraiserPortalButtons=(' .
				Xml::encodeJsVar( $buttons ) .
				");\n" .
				"wgFundraiserPortal=wgFundraiserPortalButtons[0];\n";
    	}

	public function getButtonText( $button ) {
		global $wgScriptPath;
		global $wgFundraiserPortalTemplates, $wgFundraiserPortalURL;
		
		$button_url = $wgFundraiserPortalURL . "&utm_source=$button";

		$IP = dirname( __FILE__ );
		
		wfLoadExtensionMessages( 'FundraiserPortal' );
		$imageUrl = $wgScriptPath . '/extensions/FundraiserPortal/images';
		//require_once( "$IP/Templates/$button.php" );
		$text = file_get_contents( "$IP/Templates/$button.php" ) ;
		//return htmlspecialchars( $text );
		return $text;
	}

	public function getScriptFunctions() {
		$script = "
function pickDonateButton() {
        var b = new Array();
        b['Ruby'] = 25;
        b['Tourmaline'] = 25;
        b['RubyText'] = 25;
        b['Sapphire'] = 25;

        var r = new Array();
        var total = 0;

        for (var button in b) {
                total += b[button];
                for(i=0; i < b[button]; i++) {
                        r[r.length] = button;
                }
        }

        if ( total == 0 )
                return '';

        var random = Math.floor(Math.random()*total);
        return r[random];
}

function setDonateButton( button ) {
        // Store cookie so portal is hidden for four weeks
        var e = new Date();
        e.setTime( e.getTime() + 28 * 24 * 60 * 60 * 10000 ) ;
        var work = 'donateButton=' + button + '; expires=' + e.toGMTString() + '; path=/';
        document.cookie = work;
}

function getDonateButton() {
        var t = 'donateButton';
        beg = document.cookie.indexOf( t );
        if ( beg != -1 ) {
                beg += t.length+1;
                end = document.cookie.indexOf(';', beg);
                if (end == -1)
                        end = document.cookie.length;
        return( document.cookie.substring(beg,end) );
        }
}

var wgDonateButton = getDonateButton();

if ( ! wgDonateButton ) {
        var wgDonateButton = pickDonateButton();
        setDonateButton( wgDonateButton );
}

document.write( wgDonateButton );
\n\n";
		return $script;
	}
}
