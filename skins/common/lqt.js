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
	if(!document.getElementById) return;
	
	// Hide the Go button and turn on automatic submission on the archive browser.
	
	var dropdown = document.getElementById('lqt_archive_month');
	var success = lqt_add_event(dropdown, 'change', function(){
		document.getElementById('lqt_archive_browser_form').submit();
		});
	if (success) {
		document.getElementById('lqt_archive_go_button').className = "lqt_hidden";
	}
}

addOnloadHook(lqt_on_load);