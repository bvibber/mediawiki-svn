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
	event.cancelBubble = true;

	if (event.stopPropagation)
		event.stopPropagation();

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
	var suggestValue = document.getElementById(suggestPrefix + "value");
	var suggestDiv = document.getElementById(suggestPrefix + "div");
	var suggestField = document.getElementById(stripSuffix(suggestPrefix, "-suggest-"));

	suggestField.value = value;

	suggestLink.innerHTML = displayValue;
	suggestDiv.style.display = 'none';
	suggestLink.focus();
}

function suggestClearClicked(event, suggestClear) {
	updateSuggestValue(getSuggestPrefix(suggestClear, 'clear'), "", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
	stopEventHandling(event);
}

function suggestCloseClicked(event, suggestClose) {
	var suggestPrefix = getSuggestPrefix(suggestClose, 'close');
	var suggestDiv = document.getElementById(suggestPrefix + "div");
	suggestDiv.style.display = 'none';
	stopEventHandling(event);
}

function suggestRowClicked(event, suggestRow) {
	updateSuggestValue(getSuggestPrefix(suggestRow.parentNode.parentNode.parentNode.parentNode, "div"),
						suggestRow.id, suggestRow.getElementsByTagName('td')[0].innerHTML);
	stopEventHandling(event);
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

function getExpansionElementTypes() {
	var cookies = document.cookie.split(';');
	for(var i=0;i < cookies.length;i++) {
		var cookie = cookies[i];
		while(cookie.charAt(0)==' ')
			cookie = cookie.substring(1,cookie.length);
		if(cookie.indexOf("expansion=") == 0) {
			var expansionElementTypesStr = cookie.substring(10,cookie.length);
			var elementTypes = expansionElementTypesStr.split('|');
			if(elementTypes[0] == "")
				elementTypes.splice(0,1);
			return elementTypes;
		}
	}
	return new Array();
}

function setExpanded(elementType) {
	// Ensure the element type isn't yet set to expand.
	// This could be more efficient by avoiding the clear/rewrite.
	var expansionElementTypes = getExpansionElementTypes();
	for(var i=0;i<expansionElementTypes.length;i++) {
		if(expansionElementTypes[i]=="expand-" + elementType)
			return;
		else if(expansionElementTypes[i]=="collapse-" + elementType) {
			expansionElementTypes[i] = "expand-" + elementType;
			document.cookie = "expansion=" + expansionElementTypes.join("|");
			return;
		}
	}

	expansionElementTypes[expansionElementTypes.length] = "expand-" + elementType;
	document.cookie = "expansion=" + expansionElementTypes.join("|");
}

function setDefaultCollapsed(elementType) {
	// Ensure the element type isn't yet set to collapse.
	// This could be more efficient by avoiding the clear/rewrite.
	var expansionElementTypes = getExpansionElementTypes();
	for(var i=0;i<expansionElementTypes.length;i++) {
		if(expansionElementTypes[i]=="collapse-" + elementType)
			return;
		else if(expansionElementTypes[i]=="expand-" + elementType) {
			expansionElementTypes[i] = "collapse-" + elementType;
			document.cookie = "expansion=" + expansionElementTypes.join("|");
			return;
		}
	}
	expansionElementTypes[expansionElementTypes.length] = "collapse-" + elementType;
	document.cookie = "expansion=" + expansionElementTypes.join("|");
}

function getCollapsableId(elementName) {
	return 'collapsable-' + elementName;
}

function getNameOf(element) {
	if(element.id)
		return element.id.substr(9, element.id.length - 9);
	else
		return "";
}

function getTypeOf(element) {
	return getNameOf(element).replace(/-\d+/g, "");
}

function toggle(element, event) {
	var source = event.target;

	if (!source)
		source = event.srcElement;

	if (!isFormElement(source)) {
		var elementName = getNameOf(element);
		var collapsableNode = document.getElementById(getCollapsableId(elementName));
		if (collapsableNode.style.display == 'inline' ||
			(collapsableNode.style.display != 'none' &&
			isCssClassExpanded(getTypeOf(element)))) {
			setDefaultCollapsed(getTypeOf(element));
			show(element, false);
		}
		else {
			setExpanded(getTypeOf(element));
			show(element, true);
		}

		stopEventHandling(event);
	}
}

function show(element, isShown) {
	var elementName = getNameOf(element);
	var collapsableNode = document.getElementById(getCollapsableId(elementName));
	var expandedPrefixNode = getExpandedPrefix(element);
	var collapsedPrefixNode = getCollapsedPrefix(element);

	if(isShown) {
		collapsableNode.style.display = 'inline';
		expandedPrefixNode.style.display = 'inline';
		collapsedPrefixNode.style.display = 'none';
	}
	else {
		collapsableNode.style.display = 'none';
		expandedPrefixNode.style.display = 'none';
		collapsedPrefixNode.style.display = 'inline';
	}
}

function getExpandedPrefix(element) {
	return document.getElementById(element.id.replace('collapse-', 'prefix-expanded-'));
}

function getCollapsedPrefix(element) {
	return document.getElementById(element.id.replace('collapse-', 'prefix-collapsed-'));
}

function shouldExpand(element) {
	var candidateElementType = getTypeOf(element);
	var expansionElementTypes = getExpansionElementTypes();
	for(var i=0; i<expansionElementTypes.length; i++)
		if(expansionElementTypes[i] == "expand-" + candidateElementType)
			return true;

	return false;
}

function expandEditors(event) {
	var expansionElementTypes = getExpansionElementTypes();
	for(var i=0; i<expansionElementTypes.length; i++)
		if(expansionElementTypes[i].substr(0, 7) == "expand-")
			expandCssClass(expansionElementTypes[i].substr(7), true);
		else
			expandCssClass(expansionElementTypes[i].substr(9), false);
}

function expandCssClass(cssClass, isExpanded) {
	var rulesKey;
	var is_opera = (navigator.userAgent.toLowerCase().indexOf("opera") != -1);

	if (document.all && !is_opera)
		rulesKey = 'rules';
	else
		rulesKey = 'cssRules';
	for(var sheet=0; sheet<document.styleSheets.length; sheet++)
		for(var rule=0; rule<document.styleSheets[sheet][rulesKey].length; rule++) {
			if(document.styleSheets[sheet][rulesKey][rule].selectorText == '.expand-'+cssClass)
				document.styleSheets[sheet][rulesKey][rule].style['display'] = (isExpanded?'inline':'none');
			else if(document.styleSheets[sheet][rulesKey][rule].selectorText == '.collapse-'+cssClass)
				document.styleSheets[sheet][rulesKey][rule].style['display'] = (isExpanded?'none':'inline');
		}
}

function isCssClassExpanded(cssClass) {
	var rulesKey;
	var is_opera = (navigator.userAgent.toLowerCase().indexOf("opera") != -1);

	if (document.all && !is_opera)
		rulesKey = 'rules';
	else
		rulesKey = 'cssRules';

	for(var sheet=0; sheet<document.styleSheets.length; sheet++) {
		for(var rule=0; rule<document.styleSheets[sheet][rulesKey].length; rule++) {
			if(document.styleSheets[sheet][rulesKey][rule].selectorText == '.expand-' + cssClass)
				return document.styleSheets[sheet][rulesKey][rule].style['display'] == 'inline';
		}
	}

	return false;
}
