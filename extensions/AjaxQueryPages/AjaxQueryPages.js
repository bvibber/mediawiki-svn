var wgAjaxQueryPages = {};

wgAjaxQueryPages.onLoad = function() {
	wgAjaxQueryPages.replacelinks( document );
}

wgAjaxQueryPages.replacelinks = function( target ) {
	var elsPrev = getElementsByClassName(target, "a", "mw-prevlink");
	var elsNext = getElementsByClassName(target, "a", "mw-nextlink");
	var elsNums = getElementsByClassName(target, "a", "mw-numlink");
	var els = elsPrev.concat( elsNext, elsNums );

	var reoff = /offset=(\d+)/ ;
	var relim = /limit=(\d+)/ ;

	var nEls = els.length ;
	for (var i=0; i<nEls;i++) {
		var offset = reoff.exec( els[i].getAttribute("href") )[1];
		var limit  = relim.exec( els[i].getAttribute("href") )[1];

		els[i].setAttribute("href", "javascript:wgAjaxQueryPages.call(" + offset + "," + limit + ")");
	}
}

wgAjaxQueryPages.call = function( offset, limit) {
	sajax_do_call(
		"wfAjaxQueryPages",
		[wgCanonicalSpecialPageName, offset, limit],
		wgAjaxQueryPages.processResult
		);

}

html2dom = function( html ) {
	var ret = document.createDocumentFragment();
	var tmp = document.createElement("div");
	tmp.innerHTML = html

	while( tmp.firstChild ) {
		ret.appendChild( tmp.firstChild );
	}
	return ret;
}

wgAjaxQueryPages.processResult = function(request) {
	// convert html to dom, need to merge branches/hashar@21917 to use the responseXML
	var response = html2dom( request.responseText );
	var spcontent = getElementsByClassName(document, "div", "mw-spcontent");
	wgAjaxQueryPages.replacelinks( response.firstChild );

	spcontent[0].innerHTML = response.firstChild.innerHTML ;
}

hookEvent( "load", wgAjaxQueryPages.onLoad );
