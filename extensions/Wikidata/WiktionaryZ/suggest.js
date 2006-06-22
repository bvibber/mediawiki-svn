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

function updateSuggestions(suggestPrefix) {
	var http = getHTTPObject();
	var table = document.getElementById(suggestPrefix + "table");
	var suggestQuery = document.getElementById(suggestPrefix + "query").value;
		
	suggestText = document.getElementById(suggestPrefix + "text");
	suggestText.className = "suggest-loading";
	
	http.open('GET', 'extensions/Wikidata/WiktionaryZ/Suggest.php?search=' + encodeURI(suggestText.value) + '&prefix=' + encodeURI(suggestPrefix) + '&query=' + encodeURI(suggestQuery), true);
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

function scheduleUpdateSuggestions(suggestPrefix) {
	if (suggestionTimeOut != null)
		clearTimeout(suggestionTimeOut);

	suggestionTimeOut = setTimeout("updateSuggestions(\"" + suggestPrefix + "\")", 600);
}

function suggestTextChanged(suggestText) {
	scheduleUpdateSuggestions(getSuggestPrefix(suggestText, "text"));
}

function suggestLinkClicked(event, suggestLink) {
	var suggestLinkId = suggestLink.id;
	var suggestPrefix = suggestLinkId.substr(0, suggestLinkId.length - 4);

	var suggestDiv = document.getElementById(suggestPrefix + "div");
	var suggestField = document.getElementById(suggestPrefix + "text");
	suggestDiv.style.display = 'block';
	suggestField.focus();
	
	updateSuggestions(suggestPrefix);

	if (event.preventDefault)
		event.preventDefault();
	else
		event.returnValue = false;
}

function updateSuggestValue(suggestPrefix, value, displayValue) {
	var suggestLink = document.getElementById(suggestPrefix + "link");
	var suggestDiv = document.getElementById(suggestPrefix + "div");
	var suggestField = document.getElementById(stripSuffix(suggestPrefix, "-suggest-"));
	
	suggestField.value = value;
	
	suggestLink.innerHTML = displayValue;
	suggestDiv.style.display = 'none';
	suggestLink.focus();
}

function suggestClearClicked(suggestClear) {
	updateSuggestValue(getSuggestPrefix(suggestClear, 'clear'), "", "No selection");
}

function suggestCloseClicked(suggestClose) {
	var suggestPrefix = getSuggestPrefix(suggestClose, 'close');
	var suggestDiv = document.getElementById(suggestPrefix + "div");
	suggestDiv.style.display = 'none';
}

function suggestRowClicked(suggestRow) {
	updateSuggestValue(getSuggestPrefix(suggestRow.parentNode.parentNode.parentNode.parentNode, "div"),
						suggestRow.id, suggestRow.getElementsByTagName('td')[0].innerHTML);
/*	var suggestPrefix = getSuggestPrefix(suggestRow.parentNode.parentNode.parentNode.parentNode, "div");
	var suggestLink = document.getElementById(suggestPrefix + "link");
	var suggestDiv = document.getElementById(suggestPrefix + "div");
	var suggestField = document.getElementById(stripSuffix(suggestPrefix, "-suggest-"));
	
	suggestField.value = suggestRow.id;
	
	suggestLink.innerHTML = suggestRow.getElementsByTagName('td')[0].innerHTML;
	suggestDiv.style.display = 'none';
	suggestLink.focus();*/
}

function mouseOverRow(row) {
	row.className = "suggestion-row active";
}

function mouseOutRow(row) {
	row.className = "suggestion-row inactive";
}

function removeClicked(checkBox) {
	var container = checkBox.parentNode.parentNode;
	
	if (checkBox.checked)
		container.className = "to-be-removed";
	else
		container.className = "";
}