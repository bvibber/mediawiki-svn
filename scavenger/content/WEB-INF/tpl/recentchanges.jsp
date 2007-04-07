<%--
	recentchanges: Shows recent changes to the wiki.
--%>
<%@ include file="header.jsp" %>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="scav" uri="scavenger" %>

<h1 class="title">Recent Changes</h1>

<div class="recentchanges">
<c:forEach items="${changes}" var="change">
	<div class="recentchange">
		<scav:page action="view" var="viewurl" name="${change.title.text}" />
		<scav:page action="view" var="oldurl" name="${change.title.text}">
			<scav:param name="rev" value="${change.revision.id}" />
		</scav:page>
		
		<scav:page action="diff" var="diffurl" name="${change.title.text}">
			<scav:param name="r1" value="${change.revision.id}" />
			<scav:param name="r2" value="prev" />
		</scav:page>

		<c:out value="${change.revision.timestampString}" />

		<a href="${viewurl}"><c:out value="${change.title.text}" /></a>
		(<a href="${oldurl}">view</a>)
		(<a href="${diffurl}">diff</a>)

		<c:out value="${change.revision.username}" />

		<c:if test="${!empty comment}">
			(<span class="comment"><c:out value="${change.revision.comment}" /></span>)
		</c:if>
	</div>
</c:forEach>
</div>

<%@ include file="footer.jsp" %>