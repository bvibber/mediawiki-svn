<%--
	Displays the difference between two pages.
--%>
<%@ include file="header.jsp" %>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="scav" uri="scavenger" %>

<h1 class="title"><c:out value="${title.text}" /> (difference between revisions)</h1>

<div class="diff">

<scav:page var="r1rev" name="${title.text}" action="view">
	<scav:param name="rev" value="${r1.id}" />
</scav:page>
<scav:page var="r2rev" name="${title.text}" action="view">
	<scav:param name="rev" value="${r2.id}" />
</scav:page>

<p>
Comparing: revision as of ${r1.timestampString}
(<a href="${r1rev}">view</a>)
<br>
To: revision as of ${r2.timestampString}
(<a href="${r2rev}">view</a>)
</p>

<table class="diff" cellspacing="0" cellpadding="0">
	<c:forEach items="${difflines}" var="line">
		<tr>
			<td class="line">
				<c:out value="${line.line}" />
			</td>
			<td class="text">
				<c:choose>
					<c:when test="${line.addition}">
						<ins><c:out value="${line.text}" /></ins>
					</c:when>

					<c:when test="${line.deletion}">
						<del><c:out value="${line.text}" /></del>
					</c:when>

					<c:otherwise>
						<c:out value="${line.text}" />
					</c:otherwise>
				</c:choose>
			</td>
		</tr>
	</c:forEach>
</table>
</div>

<h1 class="title">Current revision</h1>

<div class="pagebody">
	${r1text}
</div>

<%@ include file="footer.jsp" %>
