<%--
	History action.  Shows edit history for a page.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="scav" uri="scavenger" %>
<%@ include file="header.jsp" %>

<h1 class="title"><c:out value="${title.text}" /> (edit history)</h1>

<scav:page name="${title.text}" action="diff" var="diffform" />
<form method="get" action="${diffform}">

<input type="submit" value="Compare selected revisions" />

<ul>
<c:forEach items="${revisions}" var="rev" varStatus="status">
<li>
	<scav:page var="diffurl" action="diff" name="${title.text}">
		<scav:param name="r1" value="${rev.id}" />
		<scav:param name="r2" value="prev" />
	</scav:page>

	<scav:page action="view" name="${title.text}" var="viewurl">
		<scav:param name="rev" value="${rev.id}" />
	</scav:page>
	
	<input type="radio" name="r1" value="${rev.id}"/>
	<input type="radio" name="r2" value="${rev.id}"/>
	
	<c:choose>
		<c:when test="${!status.last}">
			(<a href="${diffurl}">diff</a>)
		</c:when>
		<c:otherwise>
			(diff)
		</c:otherwise>
	</c:choose>
	
	<a href="${viewurl}"><c:out value="${rev.timestampString}" /></a>
	<c:out value="${rev.username}" />

	<c:if test="${!empty rev.comment}">
		(<span class="comment"><c:out value="${rev.comment}" /></span>)
	</c:if>
</li>
</c:forEach>
</ul>

<input type="submit" value="Compare selected revisions" />

</form>

<%@ include file="footer.jsp" %>