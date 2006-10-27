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

	http.open('GET', 'index.php/Special:Suggest?search-text=' + encodeURI(suggestText.value) + '&prefix=' + encodeURI(suggestPrefix) + '&query=' + encodeURI(suggestQuery), true);
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

function isLink(node) {
	return node.nodeName.toLowerCase() == 'a';
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

function getCollapsableClass(element) {
	if(element) {
		var splitClassNames = element.className.split(' ');
		var index = 0;

		while(splitClassNames[index].indexOf("collapsable") == -1) {
			index++;
		}
		return stripPrefix(splitClassNames[index], "-");
	}
	else
		return "";
}

function stripPrefix(source, delimiter) {
	if(source) {
		var position = source.indexOf(delimiter) + 1;
		return source.substr(position, source.length - position);
	}
	else
		return "";
}

function toggle(element, event) {
	var source = event.target;

	if (!source)
		source = event.srcElement;

	if (!isFormElement(source) && !isLink(source)) {
		var elementName = stripPrefix(element.id, "-");
		var collapsableNode = document.getElementById(getCollapsableId(elementName));
		if (collapsableNode.style.display == 'inline' ||
			(collapsableNode.style.display != 'none' &&
			isCssClassExpanded(getCollapsableClass(element)))) {
			setDefaultCollapsed(getCollapsableClass(element));
			show(element, false);
		}
		else {
			setExpanded(getCollapsableClass(element));
			show(element, true);
		}

		stopEventHandling(event);
	}
}

function show(element, isShown) {
	var elementName = stripPrefix(element.id, "-");
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

function expandEditors(event) {
	var expansionElementTypes = getExpansionElementTypes();
	for(var i=0; i<expansionElementTypes.length; i++)
		if(expansionElementTypes[i].substr(0, 7) == "expand-")
			expandCssClass(expansionElementTypes[i].substr(7), true);
		else
			expandCssClass(expansionElementTypes[i].substr(9), false);
}

/*function shouldExpand(element) {
	var candidateElementType = getTypeOf(element);
	var expansionElementTypes = getExpansionElementTypes();
	for(var i=0; i<expansionElementTypes.length; i++)
		if(expansionElementTypes[i] == "expand-" + candidateElementType)
			return true;

	return false;
}*/

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

function getParentNode(node, nodeName) {
	var result = node.parentNode;
	
	while (result != null && result.tagName.toLowerCase() != nodeName)
		result = result.parentNode;
		
	return result; 
}

function getInnerText(element) {
	if (typeof element == "string") return element;
	if (typeof element == "undefined") { return element };
	if (element.innerText) return element.innerText;
	var str = "";
	
	var cs = element.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		switch (cs[i].nodeType) {
			case 1: //ELEMENT_NODE
				str += getInnerText(cs[i]);
				break;
			case 3:	//TEXT_NODE
				str += cs[i].nodeValue;
				break;
		}
	}
	
	return str;
}

function ColumnSortInformation(index, direction) {
	this.index = index;
	this.direction = direction;
	
	this.toText = function () {
		return this.index + "," + this.direction;
	}
}

function SortOrder() {
	this.columns = new Array();
	this.length = 0;
	
	this.addColumn = function (column) {
		this.columns.push(column);
		this.length++;
	}
	
	this.extractColumn = function (index) {
		var result = this.columns[index];
		this.columns.splice(index, 1);
		this.length--;
		return result;
	}
	
	this.insertColumn = function (column) {
		this.columns.unshift(column);
		this.length++;
	}
	
	this.getColumn = function (index) {
		return this.columns[index];
	}
	
	this.toText = function () {
		var result = new Array();
		
		for (var i = 0; i < this.columns.length; i++)
			result.push(this.columns[i].toText());
			
		return result.join(';');
	}
	
	this.indexOfColumn = function(columnIndex) {
		var result = -1;
		var i = 0;
		
		while (result == -1 && i < this.columns.length)
			if (this.getColumn(i).index == columnIndex)
				result = i;
			else
				i++; 
		
		return result;
	}
	
	this.alterOrder = function (columnIndex) {
		var index = this.indexOfColumn(columnIndex);
		
		if (index == 0)
			this.getColumn(0).direction *= -1;
		else if (index == -1)
			this.insertColumn(new ColumnSortInformation(columnIndex, 1));
		else {
			var column = this.extractColumn(index);
			column.direction = 1;
			
			this.insertColumn(column);
		}
	}
}

function stringToSortOrder(value) {
	var result = new SortOrder();
	var columns = value.split(';');
	
	for (var i = 0; i < columns.length; i++) {
		var fields = columns[i].split(',');
		result.addColumn(new ColumnSortInformation(fields[0], fields[1]));
	}
	
	return result;
}

var sortOrder;

function compareTexts(text1, text2) {
	text1 = text1.toLowerCase();
	text2 = text2.toLowerCase();

	if (text1 == text2)
		return 0;
	else if (text1 > text2)
		return 1;
	else
		return -1;
}

function compareTableRows(row1, row2) {
	var result = 0;
	var i = 0;
	
	while (result == 0 && i < sortOrder.length) {
		var column = sortOrder.getColumn(i);
		result = column.direction * compareTexts(getInnerText(row1.cells[column.index]), getInnerText(row2.cells[column.index]));		
		i++;
	} 

	return result;
}

function sortTable(columnNode, skipRows, columnIndex) {
	var tableNode = getParentNode(columnNode, 'table');
	var rowsToSort = new Array();
	
	for (var i = skipRows; i < tableNode.rows.length; i++)
		rowsToSort.push(tableNode.rows[i]);
	
	var sortAttribute = tableNode.getAttribute('sort-order');	
	
	if (sortAttribute != null)
		sortOrder = stringToSortOrder(sortAttribute);
	else 
		sortOrder = new SortOrder();
		
	sortOrder.alterOrder(columnIndex);
	rowsToSort.sort(compareTableRows);

	for (var i = 0; i < rowsToSort.length; i++)
		tableNode.tBodies[0].appendChild(rowsToSort[i]);	
		
	tableNode.setAttribute('sort-order', sortOrder.toText());
}

function showAttributeToggle(elementId) {
 	if (document.createTextNode) {
	    var linkHolder = document.getElementById('attribute-record-editor-title-' + elementId);
	    if (!linkHolder)
	    	return;
	
	    var outerSpan = document.createElement('span');
	    outerSpan.className = 'attributetoggle';
	
	    var toggleLink = document.createElement('a');
	    toggleLink.id = 'togglelink' + elementId;
	    toggleLink.className = 'internal';
	    toggleLink.href = 'javascript:toggleAttribute("' + elementId + '")';
	    toggleLink.appendChild(document.createTextNode(attributeShowText));
	
	    outerSpan.appendChild(document.createTextNode('['));
	    outerSpan.appendChild(toggleLink);
	    outerSpan.appendChild(document.createTextNode(']'));
	
	    linkHolder.appendChild(document.createTextNode(' '));
	    linkHolder.appendChild(outerSpan);
	
	    var cookiePos = document.cookie.indexOf("hideattribute=");
	    if (cookiePos > -1 && document.cookie.charAt(cookiePos + 8) == 1)
	    	toggleAttribute();
  	}
}

function changeText(el, newText) {
 	if (el.innerText)
    	el.innerText = newText;
	else if (el.firstChild && el.firstChild.nodeValue)
    	el.firstChild.nodeValue = newText;
}

function toggleAttribute(elementId) {
	var attribute = document.getElementById('attribute-record-editor-toggle-' + elementId).getElementsByTagName('div')[0];
  	var toggleLink = document.getElementById('togglelink' + elementId)
  
 	if(attribute && toggleLink && attribute.style.display == 'none') {
    	changeText(toggleLink, attributeHideText);
 		attribute.style.display = 'block';
     	document.cookie = "hideattribute=0";
	} else {
    	changeText(toggleLink, attributeShowText);
		attribute.style.display = 'none';
    	document.cookie = "hideattribute=1";
	}
}