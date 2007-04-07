var dols = true;

function startAsyncRequest(url, cb) 
{	
	var req;
	
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest();
        req.onreadystatechange = function() {
        	cb(req);
        }
        req.open("GET", url, true);
        req.send(null);
        return req;
    } else if (window.ActiveXObject) {
        req = new ActiveXObject("Microsoft.XMLHTTP");
        if (req) {
            req.onreadystatechange = function() {
            	cb(req);
            }
            req.open("GET", url, true);
            req.send();
            return req;
        }
    }
}

function setLiveSearch(text) {
	var ls = document.getElementById("livesearch");
	var lsbody = document.getElementById("lsbody");

	lsbody.innerHTML = text;
	
	if (text == "")
		ls.setAttribute("class", "lshidden");
	else
		ls.setAttribute("class", "lsshown");
}

function searchComplete(req) {
	if (req.readyState != 4)
		return;
		
	var items = req.responseXML.documentElement;
	var matches = items.getElementsByTagName("match");

	for (var i = 0; i < matches.length; ++i) {
		/* For each <match> */
		var lsbody = document.getElementById("lsbody");
		var title = matches[i].getElementsByTagName("title")[0].firstChild.data;
		var url = matches[i].getElementsByTagName("url")[0].firstChild.data;
		var a = document.createElement("a");
		a.href = url;
		a.innerHTML = title.replace("<", "&lt;")
						.replace(">", "&gt;")
						.replace("&", "&amp;");
		lsbody.appendChild(a);
		lsbody.appendChild(document.createElement("br"));
	}
	document.getElementById("livesearch").setAttribute("class", "lsshown");
}

function closeLiveSearch() {
	document.getElementById("livesearch").setAttribute("class", "lshidden");
	dols = false;
}

function startSearch(term) {
	if (!dols)
		return false;
	
	if (term == "") {
		setLiveSearch("");
		return;
	}
	
	var url = lsurl + "?q=" + encodeURI(term);
	startAsyncRequest(url, searchComplete);
}

function init() {
	var field = document.getElementById("searchfield");
	field.addEventListener('keyup', 
		function(e) {
			startSearch(field.value);
		},
		false);
	document.getElementById("lsclose")
		.addEventListener("click", closeLiveSearch, false);
}

window.onload = init;