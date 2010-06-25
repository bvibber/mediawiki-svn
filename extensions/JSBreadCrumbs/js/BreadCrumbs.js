$j(document).ready( function() {
    // Set defaults if included as a gadget, otherwise they should
    // be defined by the extension.
    if ( typeof wgJSBreadCrumbsMaxCrumbs == "undefined" ) {
        wgJSBreadCrumbsMaxCrumbs = 5;
    }
    if ( typeof wgJSBreadCrumbsSeparator == "undefined" ) {
        wgJSBreadCrumbsSeparator = "»";
    }

    var titleState = ( $j.cookie( 'mwext-bc-title' ) || "" ).split( wgJSBreadCrumbsSeparator );
    var urlState = ( $j.cookie( 'mwext-bc-url' ) || "" ).split( wgJSBreadCrumbsSeparator );
 
    if ( titleState.length >= wgJSBreadCrumbsMaxCrumbs ) {
        titleState = titleState.slice( titleState.length - wgJSBreadCrumbsMaxCrumbs );
        urlState = urlState.slice( urlState.length - wgJSBreadCrumbsMaxCrumbs );
    }
 
    $j( "#top" ).before( '<span id="mwext-bc" class="noprint plainlinks breadcrumbs"></span>' );
    var mwextbc = $j( "#mwext-bc" );
 
    // Remove duplicates
    var matchTitleIndex = $j.inArray( wgPageName, titleState );
    var matchUrlIndex = $j.inArray( location.pathname + location.search, urlState );
    if ( matchTitleIndex != -1 && ( matchUrlIndex == matchTitleIndex ) ) {
        titleState.splice( matchTitleIndex, 1 );
        urlState.splice( matchTitleIndex, 1 );
    }
 
    for ( var i = 0; i < titleState.length; i++ ) {
        mwextbc.append( '<a href="' + urlState[i] + '">' + titleState[i] + '</a> ' + wgJSBreadCrumbsSeparator + ' ' );
    }
 
    mwextbc.append( '<a href="' + location.pathname + location.search + '">' + wgPageName + '</a>' );
 
    titleState.push( wgPageName );
    urlState.push( location.pathname + location.search );
 
    $j.cookie( 'mwext-bc-title', titleState.join( wgJSBreadCrumbsSeparator ), { path: '/' } );
    $j.cookie( 'mwext-bc-url', urlState.join( wgJSBreadCrumbsSeparator ), { path: '/' } );
} );
