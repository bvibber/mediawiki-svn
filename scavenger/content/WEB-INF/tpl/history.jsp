<%--
	History action.  Shows edit history for a page.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ include file="header.jsp" %>

<h1 class="title"><c:out value="${title.text}" /> (edit history)</h1>

<ul>
<c:forEach items="${revisions}" var="rev">
<li>
	<c:url value="/diff" var="diffurl">
		<c:param name="r1" value="${rev.id}" />
		<c:param name="r2" value="prev" />
		<c:param name="title" value="${title.text}" />
	</c:url>

	<c:url value="/view" var="viewurl">
		<c:param name="rev" value="${rev.id}" />
		<c:param name="title" value="${title.text}" />
	</c:url>
		
	(<a href="${diffurl}">diff</a>)
	
	<a href="${viewurl}"><c:out value="${rev.timestampString}" /></a>
	<c:out value="${rev.username}" />

	<c:if test="${!empty rev.comment}">
		(<span class="comment"><c:out value="${rev.comment}" /></span>)
	</c:if>
</li>
</c:forEach>
</ul>

<%@ include file="footer.jsp" %>