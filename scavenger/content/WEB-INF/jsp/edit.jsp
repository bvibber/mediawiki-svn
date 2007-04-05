<%--
	Edit page.  Displays editbox with content of page for user to change.
--%>
<%@ taglib prefix="s" uri="/struts-tags" %>
<s:include value="header.jsp" />

<h1>Editing <s:property value="title.text" /></h1>

<form method="post" action="submit.action">
	<input type="hidden" name="title" value="<s:property value="title.text" />" />
	<textarea name="text" style="width: 100%" rows="30"><s:property value="pageText" /></textarea>
	<br />
	
	<label for="comment">Edit summary:</label>
	<input type="text" size="64" maxlength="255" name="comment" id="comment" />
	<br />
	
	<input type="submit" value="Submit" />
</form>

<s:include value="footer.jsp" />