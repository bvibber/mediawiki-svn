// search_box_id -> Results object 
var ls_map = {};

// global variables for lsearch_keypress
var ls_cur_keypressed = 0;
var ls_last_keypress = 0;
var ls_keypressed_count = 0;
// query -> json_text
var ls_cache = {};
// type: Timer
var ls_timer = null;
// tie mousedown/up events
var ls_mouse_pressed = false;
var ls_mouse_num = -1;
// delay between keypress and suggestion (in ms)
var ls_search_timeout = 200;
// add to these arrays any new searchboxes
var ls_search_boxes = new Array('searchInput', 'lsearchbox');
var ls_search_forms = new Array('searchform', 'search');
// if we stopped the service
var ls_is_stopped = false;
// max lines to show in suggest table
var ls_max_lines_per_suggest = 7;
// if we are about to focus the searchbox for the first time
var ls_first_focus = true;

/** Timeout timer that will fetch the results */ 
function ls_Timer(id,r,query){
	this.id = id;
	this.r = r;
	this.query = query;	
}

/** Properties for single search box */
function ls_Results(name, formname){	
	this.searchform = formname; // id of the searchform
	this.searchbox = name; // id of the searchbox
	this.container = name+"Suggest"; // div that holds results
	this.resultTable = name+"Result"; // id base for the result table (+num for table rows)
	this.resultText = name+"ResultText"; // id base for the spans within result tables (+num)
	this.query = null; // last processed query
	this.results = null;  // parsed titles
	this.resultCount = 0; // number of results
	this.original = null; // query that user entered 
	this.selected = -1; // which result is selected
	this.containerCount = 0; // number of results visible in container 
	this.containerRow = 0; // height of result field in the container
	this.containerTotal = 0; // total height of the container will all results
	this.visible = false; // if container is visible
}

function ls_operaWidthFix(x){
	if(is_opera || is_khtml){
		return x - 20; // opera&konqueror don't understand overflow-x, estimate scrollbar width
	}	
	return x;
}

function ls_encodeQuery(value){
  if (encodeURIComponent) {
    return encodeURIComponent(value);
  }
  if(escape) {
    return escape(value);
  }
}
function ls_decodeValue(value){
  if (decodeURIComponent) {
    return decodeURIComponent(value);
  } 
  if(unescape){
  	return unescape(value);
  }
}

/** Brower-dependent functions to find window inner size, and scroll status */
function f_clientWidth() {
	return f_filterResults (
		window.innerWidth ? window.innerWidth : 0,
		document.documentElement ? document.documentElement.clientWidth : 0,
		document.body ? document.body.clientWidth : 0
	);
}
function f_clientHeight() {
	return f_filterResults (
		window.innerHeight ? window.innerHeight : 0,
		document.documentElement ? document.documentElement.clientHeight : 0,
		document.body ? document.body.clientHeight : 0
	);
}
function f_scrollLeft() {
	return f_filterResults (
		window.pageXOffset ? window.pageXOffset : 0,
		document.documentElement ? document.documentElement.scrollLeft : 0,
		document.body ? document.body.scrollLeft : 0
	);
}
function f_scrollTop() {
	return f_filterResults (
		window.pageYOffset ? window.pageYOffset : 0,
		document.documentElement ? document.documentElement.scrollTop : 0,
		document.body ? document.body.scrollTop : 0
	);
}
function f_filterResults(n_win, n_docel, n_body) {
	var n_result = n_win ? n_win : 0;
	if (n_docel && (!n_result || (n_result > n_docel)))
		n_result = n_docel;
	return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
}

/** Get the height available for the results container */
function ls_availableHeight(r){
	var absTop = document.getElementById(r.container).style.top;
	var px = absTop.lastIndexOf("px");
	if(px > 0)
		absTop = absTop.substring(0,px);
	return f_clientHeight() - (absTop - f_scrollTop());
}


/** Get element absolute position {left,top} */
function ls_getElementPosition(elemID){
	var offsetTrail = document.getElementById(elemID);
	var offsetLeft = 0;
	var offsetTop = 0;
	while (offsetTrail){
		offsetLeft += offsetTrail.offsetLeft;
		offsetTop += offsetTrail.offsetTop;
		offsetTrail = offsetTrail.offsetParent;
	}
	if (navigator.userAgent.indexOf('Mac') != -1 && typeof document.body.leftMargin != 'undefined'){
		offsetLeft += document.body.leftMargin;
		offsetTop += document.body.topMargin;
	}
	return {left:offsetLeft,top:offsetTop};
}

/** Create the container div that will hold the suggested titles */
function ls_createContainer(r){
	var c = document.createElement("div");
	var s = document.getElementById(r.searchbox);
	var pos = ls_getElementPosition(r.searchbox);	
	var left = pos.left;
	var top = pos.top + s.offsetHeight;
	var body = document.getElementById("globalWrapper");
	c.className = "lsearchSuggest";
	c.setAttribute("id", r.container);	
	body.appendChild(c); 
	
	// dynamically generated style params	
	// IE workaround, cannot explicitely set "style" attribute
	c = document.getElementById(r.container);
	c.style.top = top+"px";
	c.style.left = left+"px";
	c.style.width = s.offsetWidth+"px";
	
	// mouse event handlers
	c.onmouseover = function(event) { ls_lsearch_mouseover(r.searchbox, event); };
	c.onmousedown = function(event) { return ls_lsearch_mousedown(r.searchbox, event); };
	c.onmouseup = function(event) { ls_lsearch_mouseup(r.searchbox, event); };
	return c;
}
/** Hide results div */
function ls_hideResults(r){
	var c = document.getElementById(r.container);
	if(c != null)
		c.style.visibility = "hidden";
	r.visible = false;
	r.selected = -1;
}

/** Show results div */
function ls_showResults(r){
	if(ls_is_stopped)
		return;
	ls_fitContainer(r);
	var c = document.getElementById(r.container);
	if(c != null){
		c.scrollTop = 0;
		c.style.visibility = "visible";
		r.visible = true;
	}
	r.selected = -1;
}

/** change container height to fit to screen */
function ls_fitContainer(r){	
	var c = document.getElementById(r.container);
	var h = ls_availableHeight(r) - 20;
	var inc = r.containerRow;
	h = parseInt(h/inc) * inc;
	if(h < (2 * inc) && r.resultCount > 1) // min: two results
		h = 2 * inc;	
	if((h/inc) > ls_max_lines_per_suggest )
		h = inc * ls_max_lines_per_suggest;
	if(h < r.containerTotal){
		c.style.height = h +"px";
		r.containerCount = parseInt(Math.round(h/inc));
	} else{
		c.style.height = r.containerTotal+"px";
		r.containerCount = r.resultCount;
	}
}
/** If some entries are longer than the box, replace text with "..." */
function ls_trimResultText(r){
	var w = document.getElementById(r.container).offsetWidth;
	if(r.containerCount < r.resultCount){		
		w -= 20; // give 20px for scrollbar		
	} else
		w = ls_operaWidthFix(w);
	if(w < 10)
		return;
	for(var i=0;i<r.resultCount;i++){
		var e = document.getElementById(r.resultText+i);
		var replace = 1;
		var lastW = e.offsetWidth+1;
		var iteration = 0;
		var changedText = false;
		while(e.offsetWidth > w && (e.offsetWidth < lastW || iteration<2)){
			changedText = true;
			lastW = e.offsetWidth;
			var l = e.innerHTML;			
			e.innerHTML = l.substring(0,l.length-replace)+"...";
			iteration++;
			replace = 4; // how many chars to replace
		}
		if(changedText){
			// show hint for trimmed titles
			document.getElementById(r.resultTable+i).setAttribute("title",r.results[i]);
		}
	}
}

/** Handles data from XMLHttpRequest, and updates the suggest results */
function ls_updateResults(r, query, text){
	ls_cache[query] = text;
	r.query = query;
	r.original = query;
	if(text == ""){
		r.results = null;
		r.resultCount = 0;
		ls_hideResults(r);
	} else{		
		try {
			var p = eval('('+text+')'); // simple json parse, could do a safer one
			if(p.results.length == 0){
				r.results = null;
				r.resultCount = 0;
				ls_hideResults(r);
				return;
			}		
			var c = document.getElementById(r.container);
			if(c == null)
				c = ls_createContainer(r);			
			c.innerHTML = ls_createResultTable(r,p.results);
			// init container table sizes
			var t = document.getElementById(r.resultTable);		
			r.containerTotal = t.offsetHeight;	
			r.containerRow = t.offsetHeight / r.resultCount;
			ls_trimResultText(r);				
			ls_showResults(r);
		} catch(e){
			// bad response from server or such
			ls_hideResults(r);			
			ls_cache[query] = null;
		}
	}	
}

/** Create the result table, to placed in the container div */
function ls_createResultTable(r, results){
	var c = document.getElementById(r.container);
	var width = ls_operaWidthFix(c.offsetWidth);	
	var html = "<table class=\"lsSuggestResults\" id=\""+r.resultTable+"\" style=\"width: "+width+"px;\">";
	r.results = new Array();
	r.resultCount = results.length;
	for(i=0;i<results.length;i++){
		var title = ls_decodeValue(results[i]);
		r.results[i] = title;
		html += "<tr><td class=\"lsSuggestResult\" id=\""+r.resultTable+i+"\"><span id=\""+r.resultText+i+"\">"+title+"</span></td></tr>";
	}
	html+="</table>"
	return html;
}

/** Fetch results after some timeout */
function ls_delayedFetch(){
	if(ls_timer == null)
		return;
	var r = ls_timer.r;
	var query = ls_timer.query;
	ls_timer = null;
	var xmlhttp = sajax_init_object();
	if(xmlhttp){
		try {
			xmlhttp.open("GET", wgLuceneAjaxSuggestWrapper+"?query="+ls_encodeQuery(query)+"&dbname="+wgDBname,true);
			xmlhttp.onreadystatechange=function(){
	        	if (xmlhttp.readyState==4) {	        		
	        		var t = document.getElementById(r.searchbox);
	        		if(t != null && t.value == query){ // check if response is still relevant	        			
	        			ls_updateResults(r, query, xmlhttp.responseText);
	        		}
	        		r.query = query;
        		}
      		};
     		xmlhttp.send(null);     	
     	} catch (e) {
			if (window.location.hostname == "localhost") {
				alert("Your browser blocks XMLHttpRequest to 'localhost', try using a real hostname for development/testing.");
			}
			throw e;
		}
	}
}

/** Set timed update on updateResults() via delayedUpdate() */
function ls_fetchResults(r, query, timeout){
	if(query == ""){
		ls_hideResults(r);
		return;
	} else if(query == r.query)
		return; // no change
	
	ls_is_stopped = false; // make sure we're running
	
	var cached = ls_cache[query];
	if(cached != null){
		ls_updateResults(r,query,cached);
		return;
	}
	
	// cancel any pending fetches
	if(ls_timer != null && ls_timer.id != null)
		clearTimeout(ls_timer.id);
	// schedule delayed fetching of results	
	if(timeout != 0){
		ls_timer = new ls_Timer(setTimeout("ls_delayedFetch()",timeout),r,query);
	} else{		
		ls_timer = new ls_Timer(null,r,query);
		ls_delayedFetch(); // do it now!
	}

}
/** Change the highlighted result, from position cur to next */
function ls_changeHighlight(r, cur, next, updateSearchBox){
	if (next >= r.resultCount)
		next = r.resultCount-1;
	if (next < -1)
		next = -1;   
	r.selected = next;
   	if (cur == next)
    	return; // nothing to do.
    
    if(cur >= 0){
    	var curRow = document.getElementById(r.resultTable + cur);
    	if(curRow != null)
    		curRow.className = "lsSuggestResult";
    }
    var newText;
    if(next >= 0){
    	var nextRow = document.getElementById(r.resultTable + next);
    	if(nextRow != null)
    		nextRow.className = "lsSuggestResultHl";
    	newText = r.results[next];
    } else
    	newText = r.original;
    	
    // adjust the scrollbar if any
    if(r.containerCount < r.resultCount){
    	var c = document.getElementById(r.container);
    	var vStart = c.scrollTop / r.containerRow;
    	var vEnd = vStart + r.containerCount;
    	if(next < vStart)
    		c.scrollTop = next * r.containerRow;
    	else if(next >= vEnd)
    		c.scrollTop = (next - r.containerCount + 1) * r.containerRow;
    }
    	
    // update the contents of the search box
    if(updateSearchBox){
    	ls_updateSearchQuery(r,newText);	
    }
}

function ls_updateSearchQuery(r,newText){
	document.getElementById(r.searchbox).value = newText;
    r.query = newText;
}

/** Event handler that will fetch results on keyup */
function ls_lsearch_keyup(e){
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	var r = ls_map[targ.id];
	if(r == null)
		return; // not our event
		
	// some browsers won't generate keypressed for arrow keys, catch it 
	if(ls_keypressed_count == 0){
		ls_processKey(r,ls_cur_keypressed);
	}
	var query = targ.value;
	ls_fetchResults(r,query,ls_search_timeout);
}

/** do something when key is pressed */
function ls_processKey(r,keypressed){
	if (keypressed == 40){ // Arrow Down
    	if (r.visible) {      		
      		ls_changeHighlight(r, r.selected, r.selected+1, true);      		
    	} else if(ls_timer == null){
    		// user wants to get suggestions now
    		r.query = "";
			ls_fetchResults(r,targ.value,0);
    	}
  	} else if (keypressed == 38){ // Arrow Up
  		if (r.visible){
  			ls_changeHighlight(r, r.selected, r.selected-1, true);
  		}
  	} else if(keypressed == 27){ // Escape
  		document.getElementById(r.searchbox).value = r.original;
  		r.query = r.original;
  		ls_hideResults(r);
  	} else if(r.query != document.getElementById(r.searchbox).value){
  		// ls_hideResults(r); // don't show old suggestions
  	}
}

/** Browse through search results, works on interval timer */
function ls_lsearch_keypress(e){	
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	var r = ls_map[targ.id];
	if(r == null)
		return; // not our event
		
	var keypressed = ls_cur_keypressed;
	if(keypressed == 38 || keypressed == 40){
		var d = new Date()
		var now = d.getTime();
		if(now - ls_last_keypress < 120){
			ls_last_keypress = now;
			return;
		}
	}
	
	ls_keypressed_count++;
	ls_processKey(r,keypressed);
}

/** Catch the key code (Firefox bug)  */
function ls_lsearch_keydown(e){
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	var r = ls_map[targ.id];
	if(r == null)
		return; // not our event
		
	if(ls_first_focus){
		// firefox bug, focus&defocus to make autocomplete=off valid
		targ.blur(); targ.focus();
		ls_first_focus = false;
	}

	ls_cur_keypressed = (window.Event) ? e.which : e.keyCode;
	ls_last_keypress = 0;
	ls_keypressed_count = 0;
}

/** Event: loss of focus */
function ls_lsearch_blur(e){	
	if(ls_first_focus)
		return; // we are focusing/defocusing
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	var r = ls_map[targ.id];
	if(r == null)
		return; // not our event
	if(!ls_mouse_pressed)	
		ls_hideResults(r);
}

/** get a suffix from ids */
function ls_getNumberSuffix(id){
	var num = id.substring(id.length-2);
	if( ! (num.charAt(0) >= '0' && num.charAt(0) <= '9') )
		num = num.substring(1);
	return num;
}

/** Mouseover the container */
function ls_lsearch_mouseover(srcId, e){
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	var r = ls_map[srcId];
	if(r == null)
		return; // not our event
	var num = ls_getNumberSuffix(targ.id);
	if(ls_isNumber(num))
		ls_changeHighlight(r,r.selected,num,false);
					
}

/** mouse_down  */
function ls_lsearch_mousedown(srcId, e){
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	var r = ls_map[srcId];
	if(r == null)
		return; // not our event
	var num = ls_getNumberSuffix(targ.id);
	
	ls_mouse_pressed = true;
	if(ls_isNumber(num)){
		ls_mouse_num = num;
		// ls_updateSearchQuery(r,r.results[num]);
	}
	// keep the focus on the search field
	document.getElementById(r.searchbox).focus();
	
	return false; // prevents selection
}

/** mouse_up  */
function ls_lsearch_mouseup(srcId, e){
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
	var r = ls_map[srcId];
	if(r == null)
		return; // not our event
	var num = ls_getNumberSuffix(targ.id);
		
	if(ls_isNumber(num) && ls_mouse_num == num){
		ls_updateSearchQuery(r,r.results[num]);
		ls_hideResults(r);
		document.getElementById(r.searchform).submit();
	}
	ls_mouse_pressed = false;
	// keep the focus on the search field
	document.getElementById(r.searchbox).focus();
}

/** Check if x is a valid integer */
function ls_isNumber(x){
	if(x == "")
		return false;
	for(var i=0;i<x.length;i++){
		var c = x.charAt(i);
		if( ! (c >= '0' && c <= '9') )
			return false;
	}
	return true;
}


/** When the form is submitted hide everything, cancel updates... */
function ls_lsearch_onsubmit(e){
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;

	ls_is_stopped = true;
	// kill timed requests
	if(ls_timer != null && ls_timer.id != null){
		clearTimeout(ls_timer.id);
		ls_timer = null;
	}
	// Hide all suggestions
	for(i=0;i<ls_search_boxes.length;i++){
		var r = ls_map[ls_search_boxes[i]];
		if(r != null){
			var b = document.getElementById(r.searchform);
			if(b != null && b == targ){ 
				// set query value so the handler won't try to fetch additional results
				r.query = document.getElementById(r.searchbox).value;
			}			
			ls_hideResults(r);
		}
	}
	return true;
}

/** Init Result objects and event handlers */
function ls_initHandlers(name, formname, element){
	var r = new ls_Results(name, formname);	
	// event handler
	element.onkeyup = function(event) { ls_lsearch_keyup(event); };
	element.onkeydown = function(event) { ls_lsearch_keydown(event); };
	element.onkeypress = function(event) { ls_lsearch_keypress(event); };
	element.onblur = function(event) { ls_lsearch_blur(event); };
	element.setAttribute("autocomplete","off");
	// stopping handler
	document.getElementById(formname).onsubmit = function(event){ return ls_lsearch_onsubmit(event); };
	ls_map[name] = r; 
}

/** Initialize, call upon page onload */
function ls_lsearchAjaxInit() {
	for(i=0;i<ls_search_boxes.length;i++){
		var id = ls_search_boxes[i];
		var form = ls_search_forms[i];
		element = document.getElementById( id );
		if(element != null)
			ls_initHandlers(id,form,element);
	}	
}

hookEvent("load", ls_lsearchAjaxInit);
