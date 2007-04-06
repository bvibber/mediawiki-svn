<#--
	Standard page header.
-->
<#escape x as x?html>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="<@s.url value="/static/main.css" />" />
		<title>Scavenger</title>
	</head>
	
	<body>
	
	<#-- Only display the menu if this is a title page -->
	<#if title?has_content>
	<div class="menu">
		<span>
			<@s.url id="viewurl" includeParams="none" action="view"
				title=title.text />
			<a href="${viewurl}">View</a>
		</span>

		<span>
			<@s.url id="editurl" includeParams="none" action="edit"
				title=title.text />
			<a href="<@s.property value="%{editurl}" />">Edit</a>
		</span>
		
		<span>
			<@s.url id="histurl" includeParams="none" action="history"
				title="${title.text}" />
			<a href="<@s.property value="%{histurl}" />">History</a>
		</span>
	</div>
	</#if>
	
	<div class="logo">
		Scavenger
	</div>
	
	<div class="userinfo">
		<@s.url id="login" includeParams="none" action="login" />
		${user.name} - <a href="<@s.property value="%{login}" />">Log in / create account</a>
	</div>
	
</#escape>