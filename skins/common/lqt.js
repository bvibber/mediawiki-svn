
function lqt_elem_from_id(id) {
    return (document.getElementById) ? document.getElementById(id) : 
	((document.all) ? document.all(id) : null);
}

var lqt_triangle_closed = new Image();
lqt_triangle_closed.src = "../skins/common/images/Arr_r.png";

var lqt_triangle_open = new Image();
lqt_triangle_open.src = "../skins/common/images/Arr_d.png";

function lqt_set_image(img_name, image) {
    if (document.images) {
	document.images[img_name].src = image.src;
	return true;
    }
    return false;
}

function lqt_on_load() {
    // Expand a thread initially on request from query string:
    var query_regexp = /.*[?&]lqt_expand=([^&]*).*/;
    var query_val = query_regexp.exec(location.search);
    
    if ( query_val.length == 2 ) {
	var expand_id = 'lqt_thread_' + query_val[1];
	var elem = lqt_elem_from_id(expand_id);
	if(elem) {
	    elem.style.display = "block";
	}
    }
    
}

addOnloadHook(lqt_on_load);

function lqt_hide_show(that_id) {
    var elem = lqt_elem_from_id(that_id);
    if (elem) {
        if ( elem.style.display == "none" ) {
            elem.style.display = "block";
        } else {
            elem.style.display = "none";
        }
    }

    return false;
}

function lqt_disclosure(id_number) {
    var triangle_id = "lqt_thread_triangle_" + id_number;
    var div_id = "lqt_thread_" + id_number;
    var div_elem = lqt_elem_from_id( div_id );

    if( !div_elem ) {
	return false;
    }
    
    if( div_elem.style.display == "none" ) {
	div_elem.style.display = "block";
	lqt_set_image( triangle_id, lqt_triangle_open );
    } else {
	div_elem.style.display = "none";
	lqt_set_image( triangle_id, lqt_triangle_closed );
    }
}

var lqt_archive_talk_is_visible = false;

function  lqt_hide_show_archive_toc() {
    var toc_id = "lqt_archive_toc";
    var toggle_link_id = "lqt_archive_hide_show";
    var toc_elem = lqt_elem_from_id( toc_id );
    var toggle_link_elem = lqt_elem_from_id( toggle_link_id );
    
    lqt_archive_talk_is_visible = !lqt_archive_talk_is_visible;

    if ( lqt_archive_talk_is_visible ) {
	toc_elem.style.display = "block";
	var new_txt = document.createTextNode("Hide Archive TOC");
    } else {
	toc_elem.style.display = "none";
	var new_txt = document.createTextNode("Show Archive TOC");
    }

    toggle_link_elem.replaceChild(new_txt, toggle_link_elem.firstChild);

    return false;
}
