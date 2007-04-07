<%--
	History action.  Shows edit history for a page.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="scav" uri="scavenger" %>
<%@ include file="header.jsp" %>

<h1 class="title"><c:out value="${title.text}" /> (edit history)</h1>

<ul>
<c:forEach items="${revisions}" var="rev">
<li>
	<scav:page var="diffurl" action="diff" name="${title.text}">
		<scav:param name="r1" value="${rev.id}" />
		<scav:param name="r2" value="prev" />
	</scav:page>

	<scav:page action="view" name="${title.text}" var="viewurl">
		<scav:param name="rev" value="${rev.id}" />
	</scav:page>
		
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