<%--
  Six degrees of Wikipedia: JSP front-end.
  This source code is released into the public domain.

  From: @(#)index.jsp	1.19 06/10/16 01:17:11
  $Id$
--%>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<%@ page language="java" contentType="text/html; charset=UTF-8"%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%@ taglib prefix="fn" uri="http://java.sun.com/jsp/jstl/functions" %>
<%
org.wikimedia.links.linksc.PathEntry[] path = null;
String error = null;
String from = request.getParameter("from"), to = request.getParameter("to");
org.wikimedia.links.linksc lc = new org.wikimedia.links.linksc();

if (from != null)
	from = from.trim();
if (to != null) 
	to = to.trim();

String enc = request.getCharacterEncoding();
if (enc == null) enc = "<undefined>";
pageContext.setAttribute("encoding", enc);

if (request.getCharacterEncoding() == null) {
	if (from != null) from = new String(from.getBytes("ISO-8859-1"), "UTF-8");
	if (to != null) to = new String(to.getBytes("ISO-8859-1"), "UTF-8");
}

boolean ign_date = false;
if (from != null && from.length() > 0 && to != null && to.length() > 0) {
	String idp = request.getParameter("ign_dates");
	ign_date = idp != null && idp.equals("1");
	String rfrom = from.substring(0, 1).toUpperCase() + from.substring(1, from.length());
	String rto = to.substring(0, 1).toUpperCase() + to.substring(1, to.length());
	try {
		path = lc.findPath(rfrom.replaceAll(" ", "_"), rto.replaceAll(" ", "_"), ign_date);
	} catch (org.wikimedia.links.ErrorException e) {
		error = e.geterror();
	}
}
if (path != null && path.length == 0)
	error = "No route found after 10 degrees.";

pageContext.setAttribute("error", error);
pageContext.setAttribute("path", path);
pageContext.setAttribute("from", from);
pageContext.setAttribute("to", to);

if (path != null) {
	pageContext.setAttribute("len", Integer.valueOf(path.length - 1));
} else {
	pageContext.setAttribute("len", Integer.valueOf(0));
}
%>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Six degrees of Wikipedia</title>
<meta name="robots" content="index" />
<meta name="robots" content="follow" />
<link rel="stylesheet" href="main.css" />
</head>
<body>
<div style="text-align: right; padding: 0px; margin: 0px"><img src="6deg.png" alt="" /><br/></div>
<div style="text-align: center">
<i>
a <a href="http://en.wikipedia.org/wiki/Shortest_path">shortest path</a>
query solver for the English
<a href="http://en.wikipedia.org/wiki/Main_Page">Wikipedia</a>...</i><br/>
<i>six degrees</i> finds the shortest path between any two Wikipedia articles in the
main namespace using wiki links
</div>

<div style="padding-top: 35px;">
<form method="get" action="index.jsp" accept-charset="UTF-8">
<center>
<strong>find path...</strong>
from: <input type="text" name="from" value="<c:out value="${from}"/>"/>
to: <input type="text" name="to" value="<c:out value="${to}"/>" />
<input type="submit" value="go" />
<br />
<input type="checkbox" name="ign_dates" value="1" 
<% if (ign_date) { %>
checked="checked"
<% } %>
/> ignore date and year articles
</center>
</form>

<% if (error != null) { %>
<center><div class='error'><span class='error'>error:</span><span class='errtext'><c:out value="${error}" /></span></div></center>
<% if (error.equals("No route found after 10 degrees.")) { %>
<p class='return'><a 
href="index.jsp?from=<c:out value='${fn:replace(to, " ", "_")}'/>&amp;to=<c:out value='${fn:replace(from, " ", "_")}'/>"
>Try in the other direction?</a></p>
<% } %>

<%--
	Print path...
--%>
<% } else if (path != null) { %>
<div class='result'>
<div class='answer'><c:out value="${len}"/> degrees of separation</div>

<c:forEach items="${path}" var="hop">
	<span class="art"><a
		href="http://en.wikipedia.org/wiki/<c:out value="${hop}"/>"
	><c:out value='${fn:replace(hop.article, "_", " ")}'/></a></span>
	<br/>
	<span class="context">
		<c:out value='${hop.context}' />
	</span>
	</br>
</c:forEach>

</div>
<p class='return'><a href="index.jsp?from=<c:out value='${fn:replace(to, " ", "_")}'/>&amp;to=<c:out value='${fn:replace(from, " ", "_")}'/>">View the return path?</a></p>
<% } %>
<div style="width: 50%; margin-left: auto; margin-right: auto; border-top: solid 1px black; margin-top: 3em">
<div style="text-align: center">
<strong>hints:</strong>
</div>
<ul>
<li><em>it says my article doesn't exist?</em> - this is usually caused by the article being created later the last database update (often several months ago).  alternatively, check your capitalisation.</li>
<li>please <em>do</em> report any other problems with six degrees to me 
[<tt>river</tt> (at) <tt>attenuate</tt> (dot) <tt>org</tt>].</li>
<li>redirects are searched as well as articles</li>
<li>using a redirect as the target will generally produce an inferior result (be careful: "United Kingdom" is not a redirect, but "United kingdom" is)</li>
<li>article names are case sensitive except for the first letter, which is always capital</li>
<li>six degrees was recently <a href="http://tools.wikimedia.de/~river/pages/six-degrees-ct">mentioned</a> in the
 German computer magazine <i>c't</i>.  fame!  who'd've thought it ;-)</li>
</ul>
</div>
</div>

</a><a href="/"><img
	src="wikimedia-toolserver-button.png" style="float: right"
	alt="Hosted by Wikimedia Toolserver" /></a>
<a href="http://www.sun.com/"><img 
	style="float: right" src="sun.gif"
	alt = "Powered by Sun Microsystems" /></a>
<p>
<a href="http://tools.wikimedia.de/~river/pages/projects/six-degrees">source code</a> |
<a href="mailto:river@attenuate.org">send feedback...</a><br />
i'm poor.  if you like <i>six degrees</i>, feel free to <a href="http://www.paypal.com/"
>PayPal</a> some money to [<tt>river</tt> (at) <tt>attenuate</tt> (dot) <tt>org</tt>].</p>
<span class='version'>Front-end version: $Revision$]
</div>
</body>
</html>
