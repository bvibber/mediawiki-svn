<%--
	Edit page.  Displays editbox with content of page for user to change.
--%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ include file="header.jsp" %>

<h1 class="title">Editing <c:out value="${title.text}" /></h1>

<c:url var="submit" value="/submit" />

<form method="post" action="${submit}">
	<input type="hidden" name="title" value="<c:out value="${title.text}" />" />
	<textarea name="text" style="width: 100%" rows="30"><c:out value="${pageText}" /></textarea>
	<br />
	
	<label for="comment">Edit summary:</label>
	<input type="text" size="64" maxlength="255" name="comment" id="comment" />
	<br />
	
	<input type="submit" value="Submit" />
</form>

<%@ include file="footer.jsp" %>