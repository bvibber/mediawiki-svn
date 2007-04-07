<%--
	Display a list of all pages on the wiki.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="scav" uri="scavenger" %>
<%@ include file="header.jsp" %>

<h1 class="title">All Pages</h1>

<div class="allpages">
	<c:forEach items="${pages}" var="page">
		<div class="page">
			<scav:page var="url" action="view" name="${page.title.text}" />
				
			<a href="${url}"><c:out value="${page.title.text}" /></a>
		</div>
	</c:forEach>
</div>

<%@ include file="footer.jsp" %>
