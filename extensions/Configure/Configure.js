/**
 * Main function
 * create JavaScript buttons to allow to modify the form to have more
 * flexibility
 */
function setupConfigure(){
	// Associative tables
	var tables = getElementsByClassName( document.getElementById( 'preferences' ), 'table', 'assoc' );
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
	
	// $wgGroupPermissions stuff, only if ajax is enabled
	if( wgConfigureUseAjax ){
		var tables = getElementsByClassName( document.getElementById( 'preferences' ), 'table', 'group-bool' );
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
			var tables = getElementsByClassName( document.getElementById( 'preferences' ), 'table', 'group-bool' );
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
	table.removeChild( tr );
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

hookEvent( "load", setupConfigure );
