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

function lqt_handle_hide_show() {
	var threadid = this.id.replace(/lqt_thread_showhide_/g, "lqt_thread_id_");
	var thread = document.getElementById(threadid);
	if ( thread.style.display == 'none' ) thread.style.display = 'block';
	else thread.style.display = 'none';
}

function lqt_hide_all_hidden_threads() {
	var els = getElementsByClassName(document, 'div', ['lqt_thread_hidden']);
	for (var i in els) {
		e = els[i];
		e.style.display = 'none';
		var link = document.createElement('a');
		link.className = 'lqt_thread_showhide';
		link.id = e.id.replace(/lqt_thread_id_/g, "lqt_thread_showhide_");
		link.innerHTML = 'Show this thread';
		lqt_add_event(link, 'click', lqt_handle_hide_show);
		e.parentNode.insertBefore(link, e);
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
	
	lqt_hide_all_hidden_threads();
}

addOnloadHook(lqt_on_load);