<#--
	Display an error message.
-->
<#include "header.ftl" />
<#escape x as x?html>

<p>Error: ${errormsg}</p>

</#escape>
<#include "footer.ftl" />