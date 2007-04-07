<%--
	Display a list of all pages on the wiki.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ include file="header.jsp" %>

<h1 class="title">All Pages</h1>

<div class="allpages">
	<c:forEach items="${pages}" var="page">
		<div class="page">
			<c:url var="url" value="/view">
				<c:param name="title" value="${page.title.text}" />
			</c:url>
				
			<a href="${url}"><c:out value="${page.title.text}" /></a>
		</div>
	</c:forEach>
</div>

<%@ include file="footer.jsp" %>
