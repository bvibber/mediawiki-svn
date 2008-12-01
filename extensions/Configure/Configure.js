/**
 * Main function
 * create JavaScript buttons to allow to modify the form to have more
 * flexibility
 */

function setupConfigure(){

	// For old versions
	if( typeof getElementsByClassName != "function" )
		return;

	// Tabs and TOC
	// ------------

	var configform = document.getElementById( 'configure' );
	if (!configform || !document.createElement) {
		return;
	}

	configform.className = configform.className + 'jsprefs';
	var sections = [];
	var children = configform.childNodes;
	var hid = 0;
	var toc = document.createElement('ul');
	toc.className = 'configtoc';
	toc.id = 'configtoc';
	toc.subLen = {};
	toc.confSec = 1;
	toc.confSub = -1;
	for( var i = 0; i < children.length; i++ ){
		if (children[i].nodeName.toLowerCase() == 'fieldset') {
			children[i].id = 'config-section-' + i;
			children[i].className = 'configsection';
			var legends = children[i].getElementsByTagName( 'legend' );
			if (legends[0] && legends[0].firstChild.nodeValue) {
				var legend = legends[0].firstChild.nodeValue;
			} else {
				var legend = '# ' + seci;
			}

			var li = document.createElement('li');
			if (i === 0) {
				li.className = 'selected';
			}

			var a = document.createElement('a');
			a.onmousedown = a.onclick = configTocToggleElement;
			a.tocId = i;
			a.collapsed = true;
			a.appendChild( document.createTextNode( '[+]' ) );
			li.appendChild(a);

			var a = document.createElement('a');
			a.href = '#' + children[i].id;
			a.id = 'toc-link-'+children[i].id;
			a.onmousedown = a.onclick = configToggle;
			a.confSec = i;
			a.confSub = -1;
			if (hid != 1) {
				a.className = 'selected';
			}
			a.appendChild( document.createTextNode( legend ) );
			li.appendChild(a);

			var headers = children[i].getElementsByTagName( 'h2' );
			var tables = getElementsByClassName( children[i], 'table', 'configure-table' );
			if( headers.length == tables.length ){
				var len = headers.length;
				toc.subLen[i] = len;
				var span = document.createElement( 'span' );
				span.className = 'config-toc-delimiter';
				li.appendChild( span );
				var ul = document.createElement( 'ul' );
				ul.style.display = "none";
				ul.id = "config-toc-" + i;
				
				for( var subsect = 0; subsect < len; subsect++ ){
					headers[subsect].id = 'config-head-' + i + '-' + subsect;
					tables[subsect].id = 'config-table-' + i + '-' + subsect;
					var a = document.createElement('a');
					a.href = '#' + headers[subsect].id;
					a.onmousedown = a.onclick = configToggle;
					a.confSec = i;
					a.confSub = subsect;
					a.appendChild( document.createTextNode( headers[subsect].firstChild.nodeValue ) );
					var li2 = document.createElement('li');
					li2.appendChild( a );
					ul.appendChild( li2 );
				}
				li.appendChild( ul );
			} else {
				toc.subLen[i] = 0;
			}
			toc.appendChild( li );
			if( hid == 1 ){
				children[i].style.display = 'none';
			}
			hid = 1;
		}
	}

	var toggleToc = document.createElement( 'a' );
	toggleToc.style.align = "right";
	toggleToc.onmousedown = toggleToc.onclick = configTocToggle;
	toggleToc.appendChild( getArrowImg( 'l' ) );

	var par = configform.parentNode;
	var table = document.createElement( 'table' );
	var tr = document.createElement( 'tr' );
	var tdToc = document.createElement( 'td' );
	var tdForm = document.createElement( 'td' );
	tdToc.appendChild( toggleToc );
	tdToc.appendChild( toc );
	tdToc.className = 'config-col-toc';
	tdForm.appendChild( configform );
	tdForm.className = 'config-col-form';
	tr.appendChild( tdToc );
	tr.appendChild( tdForm );
	table.appendChild( tr );
	par.appendChild( table );

	// Associative tables
	// ------------------

	var tables = getElementsByClassName( configform, 'table', 'assoc' );
	var reg = new RegExp( '(^| )disabled($| )' );
	for( var t = 0; t < tables.length ; t++ ){
		table = tables[t];
  		if( reg.test( table.className ) )
  			continue;
		// Button "remove this row"
		var trs = table.getElementsByTagName( 'tr' );
		for( var r = 0; r < trs.length; r++ ){
			tr = trs[r];
			if( r == 0 ){ // header
				var th = document.createElement( 'th' );
				th.appendChild( document.createTextNode( wgConfigureRemove ) );
				tr.appendChild( th );
			} else {
				var td = document.createElement( 'td' );
				td.className = 'button';
				var button = document.createElement( 'input' );
				button.type = 'button';
				button.value = wgConfigureRemoveRow;
				button.onclick = removeAssocCallback( table, r );
				td.appendChild( button );
				tr.appendChild( td );
			}
		}
		// Button "add a new row"
		var button = document.createElement( 'input' );
		button.type = 'button';
		button.className = 'button-add';
		button.value = wgConfigureAdd;
		button.onclick = createAssocCallback( table );
		table.parentNode.appendChild( button );
	}

	var thumbs = getElementsByClassName( configform, 'input', 'image-selector' );
	for( var t = 0; t < thumbs.length; t++ ){
		var textbox = thumbs[t];
		var conf = textbox.id.substr( 18 );
		var img = document.getElementById( 'image-url-preview-'+conf );

		var button = document.createElement( 'input' );
		button.type = 'button';
		button.className = 'mw-button-get-image-url';
		button.value = wgConfigureGetImageUrl;
		button.onclick = createImageUrlCallback( textbox, img );

		textbox.parentNode.insertBefore( button, img );
		textbox.parentNode.insertBefore( document.createTextNode( '\u00A0' ), button ); // nbsp
		img.parentNode.insertBefore( document.createTextNode( '\u00A0' ), img ); // nbsp
	}

	// $wgGroupPermissions stuff, only if ajax is enabled
	// --------------------------------------------------

	if( wgConfigureUseAjax ){
		var tables = getElementsByClassName( configform, 'table', 'group-bool' );
		for( var t = 0; t < tables.length ; t++ ){
			table = tables[t];
			// Button "remove this row"
			var trs = table.getElementsByTagName( 'tr' );
			for( var r = 0; r < trs.length; r++ ){
				tr = trs[r];
				if( r == 0 ){ // header
					var th = document.createElement( 'th' );
					th.appendChild( document.createTextNode( wgConfigureRemove ) );
					tr.appendChild( th );
				} else {
					var td = document.createElement( 'td' );
					td.className = 'button';
					var button = document.createElement( 'input' );
					button.type = 'button';
					button.value = wgConfigureRemoveRow;
					button.onclick = removeGroupBoolCallback( table, r );
					td.appendChild( button );
					tr.appendChild( td );
				}
			}
			// Button "add a new row"
			var button = document.createElement( 'input' );
			button.type = 'button';
			button.className = 'button-add';
			button.value = wgConfigureAdd;
			button.onclick = createGroupBoolCallback( table );
			table.parentNode.appendChild( button );
		}

		document.getElementById( 'configure-form' ).onsubmit = function(){
			var tables = getElementsByClassName( configform, 'table', 'group-bool' );
			for( var t = 0; t < tables.length ; t++ ){
				var table = tables[t];
				var id = table.id;
				var cont = '';
				var trs = table.getElementsByTagName( 'tr' );
				for( var r = 1; r < trs.length; r++ ){
					var tr = trs[r];
					if( cont != '' ) cont += "\n";
					cont += tr.id;
				}
				var input = document.createElement( 'input' );
				input.type = 'hidden';
				input.name = 'wp' + id + '-vals';
				input.value = cont;
				table.parentNode.appendChild( input );
			}
		}
	}
	
	/** Collapsible big lists */
	var biglists = getElementsByClassName( configform, '*', 'configure-biglist' );
	
	for(  var l = 0; l < biglists.length; l++ ) {
		var list = biglists[l];
		
		list.id = 'configure-biglist-content-'+l;
		list.style.display = 'none';
		
		var tn = document.createTextNode( wgConfigureBiglistHidden );
		var div = document.createElement( 'div' );
		var toggleLink = document.createElement( 'a' );
		
		toggleLink.appendChild( document.createTextNode( wgConfigureBiglistShow ) );
		toggleLink.className = 'configure-biglist-toggle-link';
		toggleLink.onclick = createToggleCallback( l );
		toggleLink.id = 'configure-biglist-link-'+l;
		toggleLink.href = 'javascript:';
		
		div.id = 'configure-biglist-placeholder-'+l;
		div.className = 'configure-biglist-placeholder';
		div.appendChild( tn );
		div.insertBefore( toggleLink, div.childNodes[0] );
		list.parentNode.insertBefore( div, list );
	}
}

// Collapsible stuff
function createToggleCallback( id ){
	return function(){
		var content = document.getElementById( 'configure-biglist-content-'+id );
		var toggleLink = document.getElementById( 'configure-biglist-link-'+id );
		var div = document.getElementById( 'configure-biglist-placeholder-'+id );
		var act;
		var newLinkText;
		var newPlaceholderText;
		
		if (toggleLink.firstChild.nodeValue == wgConfigureBiglistShow) {
			act = 'show';
			newLinkText = wgConfigureBiglistHide;
			content.style.display = 'block';
			newPlaceholderText = wgConfigureBiglistShown;
		} else {
			act = 'hide';
			newLinkText = wgConfigureBiglistShow;
			content.style.display = 'none';
			newPlaceholderText = wgConfigureBiglistHidden
		}
		
		toggleLink.removeChild( toggleLink.firstChild );
		toggleLink.appendChild( document.createTextNode( newLinkText ) );
		
		div.removeChild( div.childNodes[1] );
		div.appendChild( document.createTextNode( newPlaceholderText ) );
	}
}

// ------------------
// Assoc tables stuff
// ------------------

/**
 * This is actually a damn hack to break the reference to table variable when
 * used directly
 *
 * @param Dom object representing a table
 */
function createAssocCallback( table ){
	return function(){
		addAssocRow( table );
	}
}

/**
 * same as before
 *
 * @param Dom object representing a table
 */
function removeAssocCallback( table, r ){
	return function(){
		removeAssocRow( table, r );
	}
}

/**
 * Add a new row in a associative table
 *
 * @param Dom object representing a table
 */
function addAssocRow( table ){
	var r = table.getElementsByTagName( 'tr' ).length;
	var startName = 'wp' + table.id;
	var tr = document.createElement( 'tr' );

	var td1 = document.createElement( 'td' );
	var key = document.createElement( 'input' );
	key.type = 'text';
	key.name = startName + '-key-' + (r - 1);
	td1.appendChild( key );

	var td2 = document.createElement( 'td' );
	var val = document.createElement( 'input' );
	val.type = 'text';
	val.name = startName + '-val-' + (r - 1);
	td2.appendChild( val );

	var td3 = document.createElement( 'td' );
	td3.className = 'button';
	var button = document.createElement( 'input' );
	button.type = 'button';
	button.className = 'button-add';
	button.value = wgConfigureRemoveRow;
	button.onclick = removeAssocCallback( table, r );
	td3.appendChild( button );

	tr.appendChild( td1 );
	tr.appendChild( td2 );
	tr.appendChild( td3 );
	table.appendChild( tr );
}

/**
 * Remove a new row in a associative
 *
 * @param Dom object representing a table
 * @param integer
 */
function removeAssocRow( table, r ){
	var trs = table.getElementsByTagName( 'tr' );
	var tr = trs[r];
	tr.parentNode.removeChild( tr );
	fixAssocTable( table );
}

/**
 * Fix an associative table
 *
 * @param Dom object representing a table
 */
function fixAssocTable( table ){
	var startName = 'wp' + table.id;
	var trs = table.getElementsByTagName( 'tr' );
	for( var r = 1; r < trs.length; r++ ){
		var tr = trs[r];
		var inputs = tr.getElementsByTagName( 'input' );
		inputs[0].name = startName + '-key-' + (r - 1);
		inputs[1].name = startName + '-val-' + (r - 1);
		inputs[2].onclick = removeAssocCallback( table, r );
	}
}

// ----------------------
// Group bool table stuff
// ----------------------

/**
 * This is actually a damn hack to break the reference to table variable when
 * used directly
 *
 * @param Dom object representing a table
 */
function createGroupBoolCallback( table ){
	return function(){
		addGroupBoolRow( table );
	}
}

/**
 * same as before
 *
 * @param Dom object representing a table
 */
function removeGroupBoolCallback( table, r ){
	return function(){
		removeGroupBoolRow( table, r );
	}
}

/**
 * Add a new row in a "group-bool" table
 *
 * @param Dom object representing a table
 */
function addGroupBoolRow( table ){
	r = table.getElementsByTagName( 'tr' ).length;
	startName = 'wp' + table.id;
	var groupname = prompt( wgConfigurePromptGroup );
	if( groupname == null )
		return;

	var tr = document.createElement( 'tr' );
	tr.id = startName + '-' + groupname;

	var td1 = document.createElement( 'td' );
	td1.appendChild( document.createTextNode( groupname ) );

	var td2 = document.createElement( 'td' );
    error = false;
	sajax_do_call( 'efConfigureAjax', [ groupname ], function( x ){
		var resp = x.responseText;
		if( resp == '<err#>' || x.status != 200 )
			error = true;
		td2.innerHTML = resp;
	} );
	if( error ){
		alert( wgConfigureGroupExists );
		return;
	}

	var td3 = document.createElement( 'td' );
	td3.className = 'button';
	var button = document.createElement( 'input' );
	button.type = 'button';
	button.className = 'button-add';
	button.value = wgConfigureRemoveRow;
	button.onclick = removeAssocCallback( table, r );
	td3.appendChild( button );

	tr.appendChild( td1 );
	tr.appendChild( td2 );
	tr.appendChild( td3 );
	table.appendChild( tr );
}

/**
 * Remove a new row in a "group-bool" table
 *
 * @param Dom object representing a table
 * @param integer
 */
function removeGroupBoolRow( table, r ){
	var trs = table.getElementsByTagName( 'tr' );
	var tr = trs[r];
	table.removeChild( tr );
}

/**
 * Fix an "group-bool" table
 *
 * @param Dom object representing a table
 */
function fixAssocTable( table ){
	var startName = 'wp' + table.id;
	var trs = table.getElementsByTagName( 'tr' );
	for( var r = 1; r < trs.length; r++ ){
		var tr = trs[r];
		var inputs = tr.getElementsByTagName( 'input' );
		inputs[inputs.length - 1].onclick = removeGroupBoolCallback( table, r );
	}
}

// ---------
// TOC stuff
// ---------

/**
 * Helper for TOC
 */
function configToggle() {
	var oldsecid = this.parentNode.parentNode.selectedid;
	var confSec = this.confSec;
	var confSub = this.confSub;
	if( confSub == -1 ){
		var toc = this.parentNode.parentNode;
	} else {
		var toc = this.parentNode.parentNode.parentNode.parentNode;
	}
	var oldSec = toc.confSec;
	var oldId = 'config-section-' + oldSec;
	document.getElementById( oldId ).style.display = "none";
	document.getElementById( 'toc-link-'+oldId ).className = '';
	var newId = 'config-section-' + confSec;
	document.getElementById( newId ).style.display = "block";
	document.getElementById( 'toc-link-'+newId ).className = 'selected';

	for( var i = 0; i < toc.subLen[confSec]; i++ ){
		var headId = 'config-head-' + confSec + '-' + i;
		var tableId = 'config-table-' + confSec + '-' + i;
		var head = document.getElementById( headId );
		head.style.display = ( confSub == -1 || confSub == i ) ? "block" : "none";
		var table = document.getElementById( tableId );
		table.style.display = ( confSub == -1 || confSub == i ) ? "block" : "none";
	}
	toc.confSec = confSec;
	toc.confSub = confSub;
	return false;
}

/**
 * Toggle the TOC
 */
function configTocToggleElement(){
	var id = this.tocId;
	var tocId = "config-toc-" + id;
	var toc = document.getElementById( tocId );
	if( this.collapsed ){
		toc.style.display = "block";
		this.removeChild( this.firstChild );
		this.appendChild( document.createTextNode( '[−]' ) );
		this.collapsed = false;
	} else {
		toc.style.display = "none";
		this.removeChild( this.firstChild );
		this.appendChild( document.createTextNode( '[+]' ) );
		this.collapsed = true;
	}
}

/**
 * Toggle the entire TOC
 */
function configTocToggle(){
	var toc = document.getElementById( 'configtoc' );
	if( toc.style.display == "none" ){
		toc.parentNode.className = 'config-col-toc';
		toc.style.display = "block";
		this.removeChild( this.firstChild );
		this.appendChild( getArrowImg( 'l' ) );
	} else {
		toc.parentNode.className = 'config-col-toc-hidden';
		toc.style.display = "none";
		this.removeChild( this.firstChild );
		this.appendChild( getArrowImg( 'r' ) );
	}
}

/**
 * Handle [Get thumbnail URL] button clicks
 */
function createImageUrlCallback( textbox, img ) {
	return function() {
		sajax_do_call( 'wfAjaxGetThumbnailUrl',
			[textbox.value, 130, 130], // FIXME hard-coded.
			function(response) {
				var text = response.responseText;
				// basic error handling
				if( text.substr( 0, 9 ) == "<!DOCTYPE" ) {
					alert( wgConfigureImageError );
				} else {
					textbox.value = response.responseText;
					img.src = response.responseText;
				}
			}
		);
	}
}

/**
 * Get an image object representing an arrow
 * @param dir String: arrow direction, one of the following strings:
 *            - u: up
 *            - d: down
 *            - l: left
 *            - r: right
 */
function getArrowImg( dir ){
	var img = document.createElement( 'img' );
	img.src = stylepath + "/common/images/Arr_" + dir + ".png";
	return img;
}

hookEvent( "load", setupConfigure );
