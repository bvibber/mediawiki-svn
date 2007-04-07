<%--
	Standard page footer.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<c:url value="/recentchanges" var="rcurl" />
<c:url value="/allpages" var="allpagesurl" />

<div class="footer">
	<div class="footermenu">
		<span class="item">
			<a href="${rcurl}">Recent Changes</a>
		</span>
		
		<span class="item">
			<a href="${allpagesurl}">All Pages</a>
		</span>
	</div>
</div>

</body>
</html>
