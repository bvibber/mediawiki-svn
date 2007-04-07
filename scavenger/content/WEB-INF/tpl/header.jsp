<%--
	Standard page header.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
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
		<span>
			<c:url var="viewurl" value="/view">
				<c:param name="title" value="${title.text}" />
			</c:url>
			<a href="${viewurl}">View</a>
		</span>

		<span>
			<c:url var="editurl" value="/edit">
				<c:param name="title" value="${title.text}" />
			</c:url>
			<a href="${editurl}">Edit</a>
		</span>
		
		<span>
			<c:url var="histurl" value="/history">
				<c:param name="title" value="${title.text}" />
			</c:url>
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