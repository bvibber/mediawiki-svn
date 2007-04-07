<%--
	Page view action.  Displays the requested page.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>

<%@ include file="header.jsp" %>

<h1 class="title"><c:out value="${title.text}" /></h1>

<div class="pagebody">
	<c:choose>
		<c:when test="${!empty formattedText}">
			<c:out value="${formattedText}" escapeXml="false" />
		</c:when>
		
		<c:otherwise>
			This page does not exist.
		</c:otherwise>
	</c:choose>
</div>

<%@ include file="footer.jsp" %>