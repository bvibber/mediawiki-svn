// Loosely based on ajaxwatch.js

var wgAjaxRandom = {};

wgAjaxRandom.inprogress = false;
wgAjaxRandom.supported = true;

// Entry point
wgAjaxRandom.ajaxCall = function() {
	if(!wgAjaxRandom.supported || wgAjaxRandom.inprogress) {
		return;
	}
	wgAjaxRandom.inprogress = true;
	sajax_do_call("wfAjaxRandom", [] , wgAjaxRandom.callback);

	return;
}

wgAjaxRandom.callback = function(request) {
	if(!wgAjaxRandom.supported) {
		return;
	}

	// Tag name is defined in AjaxFunctions.php wfAjaxRandom()
	// we will have title, content, interwikis
	var data = request.responseText.split( "jqZlBX9PEbSGebfBL8mz9HyeMfCVdl7W" );
	mHTMLTitle  = data[0];
	mPageTitle  = data[1];
	mPageName   = data[2];
	mContent    = data[3];
	mCatLinks   = data[4];
	mInterwikis = data[5];

	// Update document title (top bar)
	document.title=mHTMLTitle;

	// Update the page <h1>title</h1>
	var firstHeading = false;
	var content = document.getElementById("content");
	searchLoop:
	for( var i=0; i<content.childNodes.length; i++) {
		if( content.childNodes[i].className == 'firstHeading' ) {
			firstHeading = content.childNodes[i];
			break searchLoop;
		}
	}
	firstHeading.innerHTML = mPageTitle;

	// Mediawiki global variables
	wgTitle = mPageTitle;
	wgPageName = mPageName;

	// Update page content
	var bodyContent = document.getElementById("bodyContent");
	bodyContent.innerHTML = mContent;

	// Update interwikis
	var langPortlet = document.getElementById("p-lang");
	if( langPortlet ) {
		langPortlet.innerHTML = mInterwikis;
	} else {
		langPortlet = document.createElement( "div" );
		langPortlet.id = "p-lang";
		langPortlet.class = "portlet";
		langPortlet.innerHTML = mInterwikis;
		insertAfter(
			document.getElementById("column-one"),
			langPortlet,
			document.getElementById("p-tb")
			);
	}

	wgAjaxRandom.inprogress = false;
	return;
}



// Constructor
wgAjaxRandom.onLoad = function() {
	if(!wfSupportsAjax()) {
		wgAjaxRandom.supported = false;
		return;
	}

	// <li> enclosing the randompage link
	var el = document.getElementById("n-randompage");
	rndLink = el.firstChild;
	rndLink.setAttribute( "href", "javascript:wgAjaxRandom.ajaxCall()");
	return;
}



// Register us with sajax
hookEvent("load", wgAjaxRandom.onLoad);

/**
 * @return boolean whether the browser supports XMLHttpRequest
 */
function wfSupportsAjax() {
	var request = sajax_init_object();
	var supportsAjax = request ? true : false;
	delete request;
	return supportsAjax;
}

