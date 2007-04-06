<#--
	Page view action.  Displays the requested page.
-->

<#include "header.ftl" />
<#escape x as x?html>

<h1 class="title">
	${title.text}
</h1>

<div class="pagebody">
	<#if viewing?has_content>
		<#noescape>${formatter.formattedText}</#noescape>
	<#else>
		This page does not exist.
	</#if>
</div>

</#escape>
<#include "footer.ftl" />