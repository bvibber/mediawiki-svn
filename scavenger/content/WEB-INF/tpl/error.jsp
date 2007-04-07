<%--
	Display an error message.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ include file="header.jsp" %>

<h1 class="title">Error</h1>

<div class="pagebody">
	<p class="error">Error: <c:out value="${errormsg}" /></p>
</div>

<%@ include file="footer.jsp" %>