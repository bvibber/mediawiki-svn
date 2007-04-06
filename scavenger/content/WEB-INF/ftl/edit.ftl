<#--
	Edit page.  Displays editbox with content of page for user to change.
-->
<#include "header.ftl" />
<#escape x as x?html>

<h1 class="title">Editing ${title.text}</h1>

<form method="post" action="submit.action">
	<input type="hidden" name="title" value="${title.text}" />
	<textarea name="text" style="width: 100%" rows="30">${pageText!""}</textarea>
	<br />
	
	<label for="comment">Edit summary:</label>
	<input type="text" size="64" maxlength="255" name="comment" id="comment" />
	<br />
	
	<input type="submit" value="Submit" />
</form>

</#escape>
<#include "footer.ftl" />
