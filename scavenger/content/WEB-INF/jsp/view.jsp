<%--
	Page view action.  Displays the requested page.
--%>
<%@ taglib prefix="s" uri="/struts-tags" %>

<s:include value="header.jsp" />

<h1 class="title">
	<s:property value="title.text" />
</h1>

<div class="pagebody">
	<s:if test="viewing != null">
		<s:property value="formatter.formattedText" escape="no"/>
	</s:if>
	<s:else>
		This page does not exist.
	</s:else>
</div>
	
<s:include value="footer.jsp" />