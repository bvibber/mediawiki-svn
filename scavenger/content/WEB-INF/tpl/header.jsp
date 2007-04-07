<%--
	Standard page header.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="fn" uri="http://java.sun.com/jsp/jstl/functions" %>
<%@ taglib prefix="scav" uri="scavenger" %>

<html>
	<head>
		<c:url var="css" value="/static/main.css" />
		<c:url var="js" value="/static/main.js" />
		<c:url var="lsurl" value="/livesearch" />

		<link rel="stylesheet" type="text/css" href="${css}" />
		<script type="text/javascript" src="${js}"></script>
		<script type="text/javascript">
			var lsurl = "<c:out value="${lsurl}" />";
		</script>
		<title>Scavenger</title>
	</head>
	
	<body>

	<form id="search" method="get" action="${surl}">
	
	<div class="header">
		<div class="menu">
			<%-- Only display the menu actions if this is a title page --%>
			<c:url var="surl" value="/search" />

			<c:if test="${!empty title}">
				<scav:page var="viewurl" action="view" name="${title.text}" />
				<scav:page var="editurl" action="edit" name="${title.text}" />
				<scav:page var="histurl" action="history" name="${title.text}" />
		
				<span><a href="${viewurl}">View</a></span>
				<span><a href="${editurl}">Edit</a></span>
				<span><a href="${histurl}">History</a></span>
			</c:if>

			<input type="text" name="q" width="15" id="searchfield" autocomplete="off"
					value="<c:out value='${param["q"]}' />" />
			<input type="submit" value="Go" />
		</div>
		<div class="logo">Scavenger</div>
	</div>

	</form>
	
	<div class="userinfo">
		<c:url var="login" value="/login" />
		<c:out value="${user.name}" /> - <a href="${login}">Log in / create account</a>
	</div>
	
	<c:if test="${fn:contains(title.text, '/')}">
		<c:set var="sofar" value="" />
		
		<div class="breadcrumbs">
		
		<c:forTokens items="${title.text}" delims="/" var="part" varStatus="status">
			<c:choose>
				<c:when test="${status.first}">
					<c:set var="sofar" value="${part}" />
				</c:when>
				<c:otherwise>
					<c:set var="sofar" value="${sofar}/${part}" />
				</c:otherwise>
			</c:choose>
			
			<scav:page action="view" name="${sofar}" var="url" />
			<a href="${url}"><c:out value="${part}" /></a>
			
			<c:if test="${!status.last}">
				&gt;
			</c:if>
		</c:forTokens>
		
		</div>
	</c:if>

	<div id="livesearch" class="livesearch lshidden">
		<div class="lsheader">Title matches: <span id="lsclose">[x]</span></div>
		<div id="lsbody"></div>
	</div>