<%--
	Display an error message.
--%>
<%@ taglib prefix="s" uri="/struts-tags" %>
<s:include value="header.jsp" />

<p>Error: <s:property value="errormsg" /></p>

<s:include value="footer.jsp" />