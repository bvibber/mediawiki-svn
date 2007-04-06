<#--
	Display an error message.
-->
<#include "header.ftl" />
<#escape x as x?html>

<h1 class="title">Error</h1>

<div class="pagebody">
	<p class="error">Error: ${errormsg}</p>
</div>

</#escape>
<#include "footer.ftl" />