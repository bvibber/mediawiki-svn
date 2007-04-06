<#--
	Displays the difference between two pages.
-->
<#include "header.ftl" />
<#escape x as x?html>

<h1 class="title">${title.text} (difference between revisions)</h1>

<div class="diff">

<@s.url id="r1rev" action="view" includeParams="none"
	title="${title.text}"
	rev="${r1.id}" />
<@s.url id="r2rev" action="view" includeParams="none"
	title="${title.text}"
	rev="${r2.id}" />
	
<p>
Comparing: revision as of ${r1.timestampString}
(<a href="<#noescape>${r1rev}</#noescape>">view</a>)
<br>
To: revision as of ${r2.timestampString}
(<a href="<#noescape>${r2rev}</#noescape>">view</a>)
</p>

<table class="diff" cellspacing="0" cellpadding="0">
	<#list difflines as line>
		<tr>
			<td class="line">
				${line.line}
			</td>
			<td class="text">
				<#if line.addition>
					<ins>${line.text}</ins>
				<#elseif line.deletion>
					<del>${line.text}</del>
				<#else>
					${line.text}
				</#if>
			</td>
		</tr>
	</#list>
</table>
</div>

<h1 class="title">Current revision</h1>

<div class="pagebody">
	<#noescape>${r1formatter.formattedText}</#noescape>
</div>

</#escape>
<#include "footer.ftl" />
