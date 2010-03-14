
function showHelp(idx, show) {
	var showDiv = document.getElementById( 'config-show-help-' + idx );
	var hideDiv = document.getElementById( 'config-hide-help-' + idx );
	var msgDiv = document.getElementById( 'config-help-message-' + idx );
	if ( !showDiv || !hideDiv || !msgDiv ) return;
	if ( show ) {
		showDiv.style.display = 'none';
		hideDiv.style.display = 'block';
		msgDiv.style.display = 'block';
	} else {
		showDiv.style.display = 'block';
		hideDiv.style.display = 'none';
		msgDiv.style.display = 'none';
	}
}

function hideAllDBs() {
	for ( var i = 0; i < dbTypes.length; i++ ) {
		elt = document.getElementById( 'DB_wrapper_' + dbTypes[i] );
		if ( elt ) elt.style.display = 'none';
	}
}
function showDBArea(type) {
	hideAllDBs();
	var div = document.getElementById('DB_wrapper_' + type);
	if (div) div.style.display = 'block';
}
function resetDBArea() {
	for ( var i = 0; i < dbTypes.length; i++ ) {
		input = document.getElementById('DBType_' + dbTypes[i]);
		if ( input && input.checked ) {
			showDBArea( dbTypes[i] );
			return;
		}
	}
}
function enableOrDisableControlArray( sourceID, targetIDs, enable ) {
	var source = document.getElementById( sourceID );
	var disabled = !!source.checked == enable ? '' : '1';
	if ( !source ) {
		return;
	}
	for ( var i = 0; i < targetIDs.length; i++ ) {
		var elt = document.getElementById( targetIDs[i] );
		if ( elt ) elt.disabled = disabled;
	}
}
function enableControlArray( sourceID, targetIDs, enable ) {
	enableOrDisableControlArray( sourceID, targetIDs, true );
}
function disableControlArray( sourceID, targetIDs ) {
	enableOrDisableControlArray( sourceID, targetIDs, false );
}

function showControlArray( sourceID, targetIDs ) {
	var source = document.getElementById( sourceID );
	var show = !!source.checked == false ? '' : '1';
	if ( !source ) {
		return;
	}
	for ( var i = 0; i < targetIDs.length; i++ ) {
		var elt = document.getElementById( targetIDs[i] );
		if ( !elt ) continue;
		if ( show ) {
			elt.style.display = 'block';
		} else {
			elt.style.display = 'none';
		}
	}
}
wgSameNamespacePrefix = null;

function setProjectNamespace() {
	var radio = document.getElementById( 'config__NamespaceType_site-name' );
	if ( radio == null ) return;
	var li = radio.parentNode;
	var labels = li.getElementsByTagName( 'label' );
	if ( labels.length == 0 ) return;
	var label = labels.item( 0 );

	if ( wgSameNamespacePrefix == null ) {
		wgSameNamespacePrefix = label.innerHTML;
	}
	var input = document.getElementById( 'config_wgSitename' );
	if ( input == null ) return;
	var value = input.value;
	value = value.replace(/[\[\]\{\}|#<>%+? ]/g, '_');
	value = value.replace(/&/, '&amp;');
	value = value.replace(/__+/g, '_');
	value = value.replace(/^_+/, '').replace(/_+$/, '');
	value = value.substr(0, 1).toUpperCase() 
		+ value.substr(1);
	label.innerHTML = wgSameNamespacePrefix.replace('$1', value);
}

