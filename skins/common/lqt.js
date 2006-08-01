function lqt_on_load() {
    // Expand a thread initially on request from query string:
    var query_regexp = /.*[?&]lqt_expand=([^&]*).*/;
    var query_val = query_regexp.exec(location.search);
    
    if ( query_val.length == 2 ) {
	var expand_id = 'lqt_thread_' + query_val[1];
	var elem = (document.getElementById) ? document.getElementById(expand_id) : 
               ((document.all) ? document.all(expand_id) : null);
	if(elem) {
	    elem.style.display = "block";
	}
    }
    
}

addOnloadHook(lqt_on_load);

function lqt_hide_show(that_id) {
    var elem = (document.getElementById) ? document.getElementById(that_id) : 
               ((document.all) ? document.all(that_id) : null);
    if (elem) {
        if ( elem.style.display == "none" ) {
            elem.style.display = "block";
        } else {
            elem.style.display = "none";
        }
    }

}
