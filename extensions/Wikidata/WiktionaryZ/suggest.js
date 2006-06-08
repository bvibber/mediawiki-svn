function getHTTPObject() {
	var xmlhttp;

	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {
			try {
				xmlhttp = new XMLHttpRequest();
			} catch (e) {
				xmlhttp = false;
			}
		}
	}

	return xmlhttp;
}

function stripSuffix(source, suffix) {
	return source.substr(0, source.length - suffix.length);
}

function getSuggestPrefix(node, postFix) {
	var nodeId = node.id;
	return stripSuffix(nodeId, postFix);
}

function updateSuggestions(suggestPrefix, suggestTextId, suggestQuery) {
	var http = getHTTPObject();
	var table = document.getElementById(suggestPrefix + "table");
	
	suggestText = document.getElementById(suggestTextId);
	suggestText.className = "suggest-loading";

	http.open('GET', 'extensions/Wikidata/WiktionaryZ/Suggest.php?search=' + escape(suggestText.value) + '&prefix=' + escape(suggestPrefix) + '&query=' + escape(suggestQuery), true);
	http.onreadystatechange = function() {
		if (http.readyState == 4) {
			var newTable = document.createElement('div');
	
			if (http.responseText != '') {
				newTable.innerHTML = http.responseText;
				table.parentNode.replaceChild(newTable.firstChild, table);
			}

			suggestText.className = "";
		}
	};
		
	http.send(null);
}

var suggestionTimeOut = null;

function suggestTextChanged(suggestText) {
	if (suggestionTimeOut != null)
		clearTimeout(suggestionTimeOut);

	var suggestPrefix = getSuggestPrefix(suggestText, "text");		
	var suggestQuery = document.getElementById(suggestPrefix + "query").value;
	suggestionTimeOut = setTimeout("updateSuggestions(\"" + suggestPrefix + "\", \"" + suggestText.id + "\", \"" + suggestQuery + "\")", 600);
}

function suggestLinkClicked(event, suggestLink) {
	var suggestLinkId = suggestLink.id;
	var suggestPrefix = suggestLinkId.substr(0, suggestLinkId.length - 4);

	var suggestDiv = document.getElementById(suggestPrefix + "div");
	var suggestField = document.getElementById(suggestPrefix + "text");
	suggestDiv.style.display = 'block';
	suggestField.focus();
	
	if (event.preventDefault)
		event.preventDefault();
	else
		event.returnValue = false;
}

function suggestCloseClicked(suggestClose) {
	var suggestPrefix = getSuggestPrefix(suggestClose, 'close');
	var suggestDiv = document.getElementById(suggestPrefix + "div");
	suggestDiv.style.display = 'none';
}

function suggestRowClicked(suggestRow) {
	var suggestPrefix = getSuggestPrefix(suggestRow.parentNode.parentNode.parentNode.parentNode, "div");
	var suggestLink = document.getElementById(suggestPrefix + "link");
	var suggestDiv = document.getElementById(suggestPrefix + "div");
	var suggestField = document.getElementById(stripSuffix(suggestPrefix, "-suggest-"));
	
	suggestField.value = suggestRow.id;
	
	suggestLink.innerHTML = suggestRow.getElementsByTagName('td')[0].innerHTML;
	suggestDiv.style.display = 'none';
	suggestLink.focus();
}

function mouseOverRow(row) {
	row.className = "suggestion-row active";
}

function mouseOutRow(row) {
	row.className = "suggestion-row inactive";
}