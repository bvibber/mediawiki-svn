<%--
	Standard page header.
--%>
<%@ taglib prefix="s" uri="/struts-tags" %>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="<s:url value="/static/main.css" />" />
		<title>Scavenger</title>
	</head>
	
	<body>
	
	<div class="menu">
		<span>
			<s:url id="viewurl" includeParams="none" action="view">
				<s:param name="title" value="title.key" />
			</s:url>
				
			<s:a href="%{viewurl}">View</s:a>
		</span>

		<span>
			<s:url id="editurl" includeParams="none" action="edit">
				<s:param name="title" value="title.key" />
			</s:url>
				
			<s:a href="%{editurl}">Edit</s:a>
		</span>
		
		<span>
			<s:url id="histurl" includeParams="none" action="/history">
				<s:param name="title" value="title.key" />
			</s:url>
				
			<s:a href="%{histurl}">History</s:a>
		</span>
	</div>
	
	<div class="logo">
		Scavenger
	</div>
	
	<div class="userinfo">
		<s:url id="login" includeParams="none" action="login" />
		<s:property value="user.name" />
		- <a href='<s:property value="login" escape="no" />'>Log in / create account</a>
	</div>