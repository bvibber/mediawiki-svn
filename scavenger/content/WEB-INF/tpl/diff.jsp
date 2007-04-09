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

<table class="diff" cellspacing="0" cellpadding="0">
	<tr>
		<th class="left">
			Revision as of ${r1.timestampString}
			(<a href="${r1rev}">view</a>)
			<br />
			By <c:out value="${r1.username}" />
			<c:if test="${!empty r1.comment}">
				(<span class="comment"><c:out value="${r1.comment}" /></span>)
			</c:if>
		</th>
		<th class="right">
			Revision as of ${r2.timestampString}
			(<a href="${r2rev}">view</a>)
			<br />
			By <c:out value="${r2.username}" />
			<c:if test="${!empty r2.comment}">
				(<span class="comment"><c:out value="${r2.comment}" /></span>)
			</c:if>
		</th>
	</tr>
			
	<c:forEach items="${diffchunks}" var="chunk">
		<tr class="diffloc">
			<td colspan="2">
				Line <c:out value="${chunk.start}" />
			</td>
		</tr>
		
		<tr>		
			<td class="left">
				<c:forEach items="${chunk.left.lines}" var="line">
					<c:choose>
						<c:when test="${line.context}">
							<c:out value="${line.text}" /> <br />
						</c:when>
						
						<c:otherwise>
							<del><c:out value="${line.text}" /></del> <br />
						</c:otherwise>
					</c:choose>
				</c:forEach>
			</td>
			
			<td class="right">
				<c:forEach items="${chunk.right.lines}" var="line">
					<c:choose>
						<c:when test="${line.context}">
							<c:out value="${line.text}" /> <br />
						</c:when>
						
						<c:otherwise>
							<ins><c:out value="${line.text}" /></ins><br />
						</c:otherwise>
					</c:choose>
				</c:forEach>
			</td>
		</tr>
	</c:forEach>
</table>
</div>

<h1 class="title">Current revision</h1>

<div class="pagebody">
	<scav:parse text="${r2.text}" />
</div>

<%@ include file="footer.jsp" %>
