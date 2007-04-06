<#--
	Standard page footer.
-->
<#escape x as x?html>

<@s.url action="recentchanges" id="rcurl" />
<@s.url action="allpages" id="allpagesurl" />

<div class="footer">
	<div class="footermenu">
		<span class="item">
			<a href="<#noescape>${rcurl}</#noescape>">Recent Changes</a>
		</span>
		
		<span class="item">
			<a href="<#noescape>${allpagesurl}</#noescape>">All Pages</a>
		</span>
	</div>
</div>

</body>
</html>
</#escape>