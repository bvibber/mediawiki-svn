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
	
	http.open('GET', 'extensions/Wikidata/WiktionaryZ/SpecialSuggest.php?search=' + encodeURI(suggestText.value) + '&prefix=' + encodeURI(suggestPrefix) + '&query=' + encodeURI(suggestQuery), true);
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

function stopEventHandling(event) {
	if (event.preventDefault)
		event.preventDefault();
	else
		event.returnValue = false;
}

function suggestLinkClicked(event, suggestLink) {
	var suggestLinkId = suggestLink.id;
	var suggestPrefix = suggestLinkId.substr(0, suggestLinkId.length - 4);

	var suggestDiv = document.getElementById(suggestPrefix + "div");
	var suggestField = document.getElementById(suggestPrefix + "text");
	suggestDiv.style.display = 'block';
	suggestField.focus();
	
	updateSuggestions(suggestPrefix);
	stopEventHandling(event);
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
}

function mouseOverRow(row) {
	row.className = "suggestion-row active";
}

function mouseOutRow(row) {
	row.className = "suggestion-row inactive";
}

function enableChildNodes(node, enabled) {
	if (enabled)
		var disabled = "";
	else
		var disabled = "disabled";	

	childNodes = node.getElementsByTagName('select');

	for (var i = 0; i < childNodes.length; i++)
		childNodes[i].disabled = disabled;
}

function removeClicked(checkBox) {
	var container = checkBox.parentNode.parentNode;
	
	if (checkBox.checked) 
		container.className = "to-be-removed";
	else
		container.className = "";
		
	//enableChildNodes(container, !checkBox.checked);
}

function isFormElement(node) {
	var name = node.nodeName.toLowerCase();
	
	return name == 'select' || name == 'option' || name == 'input' || name == 'textarea' || name == 'button';
}

function toggle(element, event) {
	var source = event.target;
	
	if (!source)
		source = event.srcElement;
	
	if (!isFormElement(source)) {
		var elementName = element.id.substr(9, element.id.length - 9);
		var collapsableNode = document.getElementById('collapsable-' + elementName);
		
		if (collapsableNode.style.display == 'none') {
			collapsableNode.style.display = 'block';
			//var newChar = '&ndash;';
			var newChar = '\u2013';
		}
		else {
			collapsableNode.style.display = 'none';
			var newChar = '+';
		}
		
		var textNode = element.childNodes[0];
		var text = textNode.nodeValue;

		textNode.nodeValue = newChar +  text.substr(1, text.length - 1);
		stopEventHandling(event);
	}
}