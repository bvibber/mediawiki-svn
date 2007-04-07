<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<%@ page contentType="text/xml" %>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="fn" uri="http://java.sun.com/jsp/jstl/functions" %>
<%@ taglib prefix="scav" uri="scavenger" %>

<livesearch>
	<c:forEach items="${matches}" var="match">
		<match>
			<title><c:out value="${match}"/></title>
			<url><scav:page action="view" name="${match}"/></url>
		</match>
	</c:forEach>
</livesearch>
