<%--
	recentchanges: Shows recent changes to the wiki.
--%>
<%@ include file="header.jsp" %>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>

<h1 class="title">Recent Changes</h1>

<div class="recentchanges">
<c:forEach items="${changes}" var="change">
	<div class="recentchange">
		<c:url value="/view" var="viewurl">
			<c:param name="title" value="${change.title.text}" />
			<c:param name="rev" value="${change.revision.id}" />
		</c:url>
		<c:url value="/diff" var="diffurl">
			<c:param name="title" value="${change.title.text}" />
			<c:param name="r1" value="${change.revision.id}" />
			<c:param name="r2" value="prev" />
		</c:url>

		<c:out value="${change.revision.timestampString}" />

		<a href="${viewurl}"><c:out value="${change.title.text}" /></a>
		(<a href="${diffurl}">diff</a>)

		<c:out value="${change.revision.username}" />

		<c:if test="${!empty comment}">
			(<span class="comment"><c:out value="${change.revision.comment}" /></span>)
		</c:if>
	</div>
</c:forEach>
</div>

<%@ include file="footer.jsp" %>