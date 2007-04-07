<%--
	Page view action.  Displays the requested page.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="scav" uri="scavenger"
 %>
<%@ include file="header.jsp" %>

<h1 class="title"><c:out value="${title.text}" /></h1>
<c:if test="${!empty formattedText}">
	<div class="sub">
		<scav:page var="diffurl" action="diff" name="${title.text}">
			<scav:param name="r1" value="${viewing.id}"/>
			<scav:param name="r2" value="prev"/>
		</scav:page>
		
		Last edited by <c:out value="${viewing.username}"/>
		on <c:out value="${viewing.timestampString}" />
		(<a href="${diffurl}">diff</a>)
	</div>
</c:if>

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