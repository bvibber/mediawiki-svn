<%--
	History action.  Shows edit history for a page.
--%>
<%@ taglib prefix="s" uri="/struts-tags" %>

<s:include value="header.jsp" />

<h1 class="title"><s:property value="title.text" /> (edit history)</h1>

<ul>
<s:iterator value="revisions">
<li>
	<s:url action="diff" id="diffurl">
		<s:param name="title" value="title.text" />
		<s:param name="r1" value="id" />
		<s:param name="r2" value="'prev'" />
	</s:url>
	
	<s:url action="view" id="viewurl">
		<s:param name="title" value="title.text" />
		<s:param name="rev" value="id" />
	</s:url>
		
	(<a href="<s:property value='diffurl' escape='false' />">diff</a>)
	
	<a href="<s:property value='viewurl' escape='false'
	/>"><s:property value="timestampString" /></a>
		
	<s:property value="username" />
	
	<s:if test="comment != ''">
		(<span class="comment"><s:property value="comment" /></span>)
	</s:if>
</li>
</s:iterator>
</ul>

<s:include value="footer.jsp" />