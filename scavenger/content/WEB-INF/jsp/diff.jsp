<%--
	Displays the difference between two pages.
--%>
<%@ taglib prefix="s" uri="/struts-tags" %>
<s:include value="header.jsp" />

<h1 class="title">
	<s:property value="title.text" /> (difference between revisions)
</h1>

<div class="diff">

<s:url id="r1rev" action="view" includeParams="none">
	<s:param name="title" value="title.text" />
	<s:param name="rev" value="r1.id" />
</s:url>

<s:url id="r2rev" action="view" includeParams="none">
	<s:param name="title" value="title.text" />
	<s:param name="rev" value="r2.id" />
</s:url>
	
<p>
Comparing: revision as of <s:property value="r1.timestampString" />
(<a href="<s:property value='r1rev' escape='no' />">view</a>)
<br>
To: revision as of <s:property value="r2.timestampString" />
(<a href="<s:property value='r2rev' escape='no' />">view</a>)
</p>

<table class="diff" cellspacing="0" cellpadding="0">
	<s:iterator value="difflines">
		<tr>
			<td class="line">
				<s:property value="line" />
			</td>
			<td class="text">
				<s:if test="addition">
					<ins><s:property value="text" /></ins>
				</s:if>
				<s:elseif test="deletion">
					<del><s:property value="text" /></del>
				</s:elseif>
				<s:else>
					<s:property value="text" />
				</s:else>
			</td>
		</tr>
	</s:iterator>
</table>
</div>

<h1 class="title">Current revision</h1>

<div class="pagebody">
	<s:property value="r1formatter.formattedText" escape="false" />
</div>

<s:include value="footer.jsp" />