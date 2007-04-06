<#--
	recentchanges: Shows recent changes to the wiki.
-->
<#include "header.ftl" />
<#escape x as x?html>

<h1 class="title">Recent Changes</h1>

<div class="recentchanges">
<#list changes as change>
	<div class="recentchange">
		<@s.url action="view" includeParams="none" id="viewurl"
			title="${change.title.text}"
			rev="${change.revision.id}" />
		<@s.url action="diff" includeParams="none" id="diffurl"
			title="${change.title.text}"
			r1="${change.revision.id}"
			r2="prev" />

		${change.revision.timestampString}

		<a href="<#noescape>${viewurl}</#noescape>">${change.title.text}</a>
		(<a href="<#noescape>${diffurl}</#noescape>">diff</a>)
		${change.revision.username}

		<#if change.revision.comment?has_content>
			(<span class="comment">${change.revision.comment}</span>)
		</#if>
	</div>
</#list>
</div>

</#escape>
<#include "footer.ftl" />