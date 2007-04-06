<#--
	Display a list of all pages on the wiki.
-->
<#include "header.ftl" />
<#escape x as x?html>

<h1 class="title">All Pages</h1>

<div class="allpages">
	<#list pages as page>
		<div class="page">
			<@s.url id="url" includeParams="none" action="view"
				title=page.title.text />
				
			<a href="<@s.property value="%{url}" />">${page.title.text}</a>
		</div>
	</#list>
</div>

</#escape>
<#include "footer.ftl" />