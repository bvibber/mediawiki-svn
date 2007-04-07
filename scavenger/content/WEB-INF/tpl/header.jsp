<%--
	Standard page header.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="scav" uri="scavenger" %>

<html>
	<head>
		<c:url var="css" value="/static/main.css" />
		<link rel="stylesheet" type="text/css" href="${css}" />
		<title>Scavenger</title>
	</head>
	
	<body>
	
	<%-- Only display the menu if this is a title page --%>
	<c:if test="${!empty title}">
	<div class="menu">
		<scav:page var="viewurl" action="view" name="${title.text}" />
		<scav:page var="editurl" action="edit" name="${title.text}" />
		<scav:page var="histurl" action="history" name="${title.text}" />
		
		<span>
			<a href="${viewurl}">View</a>
		</span>

		<span>
			<a href="${editurl}">Edit</a>
		</span>
		
		<span>
			<a href="${histurl}">History</a>
		</span>
	</div>
	</c:if>
	
	<div class="logo">
		Scavenger
	</div>
	
	<div class="userinfo">
		<c:url var="login" value="/login" />
		<c:out value="${user.name}" /> - <a href="${login}">Log in / create account</a>
	</div>