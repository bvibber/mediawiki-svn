// http://onlinetools.org/articles/unobtrusivejavascript/chapter4.html
function lqt_add_event(obj, evType, fn){ 
	if (obj.addEventListener){ 
		obj.addEventListener(evType, fn, false); 
		return true; 
	} else if (obj.attachEvent){ 
		var r = obj.attachEvent("on"+evType, fn); 
		return r; 
	} else { 
		return false; 
	} 
}

function lqt_on_load() {
	/* Hide the Go button on the archive widget, because we will
	   automatically go when a selection is made. */
	document.getElementById('lqt_archive_go_button').className = "lqt_hidden";
	
	var dropdown = document.getElementById('lqt_archive_month');
	lqt_add_event(dropdown, 'change', function(){
		document.getElementById('lqt_archive_browser_form').submit();
		});
}

addOnloadHook(lqt_on_load);