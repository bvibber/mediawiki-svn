<#--
	History action.  Shows edit history for a page.
-->
<#include "header.ftl" />
<#escape x as x?html>

<h1 class="title">${title.text} (edit history)</h1>

<ul>
<#list revisions as rev>
<li>
	<@s.url action="diff" id="diffurl" includeParams="none"
		r1="${rev.id?string}"
		r2="prev"
		title="${title.text}" />

	<@s.url action="view" id="viewurl" includeParams="none"
		rev="${rev.id?string}"
		title="${title.text}" />
		
	<#noescape>(<a href="${diffurl}">diff</a>)</#noescape>
	
	<#noescape><a href="${viewurl}">${rev.timestampString}</a></#noescape>
	${rev.username}	

	<#if comment?has_content>	
		(<span class="comment">${rev.comment}</span>)
	</#if>
</li>
</#list>
</ul>

</#escape>
<#include "footer.ftl" />
