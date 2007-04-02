<%-- vim:et sw=2 ts=2:
  Six degrees of Wikipedia: JSP front-end.
  This source code is released into the public domain.

  From: @(#)index.jsp	1.19 06/10/16 01:17:11
  $Id$
--%>
<%@ taglib prefix="s" uri="/struts-tags" %>

<%-- epilogue --%>
<div style="width: 50%; margin-left: auto; margin-right: auto; border-top: solid 1px black; margin-top: 3em">

  <div style="text-align: center">
    <strong>hints:</strong>
  </div>

  <ul>
    <li><strong>it says my article doesn't exist?</strong> - this is usually caused by the article being created later the last database update (often several months ago).  alternatively, check your capitalisation.</li>
    <li>redirects ("aliases" from one name to another article) are searched as well as articles</li>
    <li>using a <strong>redirect as the target</strong> will generally produce an inferior result, because articles are not meant to link to redirects. (be careful: "United Kingdom" is not a redirect, but "United kingdom"&mdash;with a lowercase "k"&mdash;is)</li>
    <li>article names are <strong>case sensitive</strong> except for the first letter, which is always capital</li>
    <li>please <strong>do</strong> report any problems with six degrees to me 
    [<tt>river</tt> (at) <tt>attenuate</tt> (dot) <tt>org</tt>].</li>
    <li>six degrees was recently <a href="http://tools.wikimedia.de/~river/pages/six-degrees-ct">mentioned</a> in the
        German computer magazine <i>c't</i>.  fame!  who'd've thought it ;-)</li>
    <li><strong>ignore date and year articles</strong>: at the moment, this only ignores articles like "March 25" and "1995".
        in the future, i might add other articles which summarise years, such as "2005 in music".</li>
  </ul>

  <div style="border-top: solid 1px black; margin-top: 3em">
    <p><strong>six degrees</strong> finds the shortest path from one article to another, using wiki links.
    the <em>shortest path</em> is a problem in computer science: given a list of nodes (articles) and links between them,
    find a route from one node to another such that no shorter route exists.  it's important to realise that there's no
    <em>single</em> shortest path; there may be many routes from one article to another which all traverse four articles,
    for example.  six degrees will find one such route.</p>

    <p>six degrees works on a copy of the English Wikipedia database, and is not updated in real time.  this means
    that if someone adds or removes a link on Wikipedia, the change will not be reflected in six degrees until the next
    database update.  for various reasons, these updates are currently very infrequent, so the path you see here
    may be several months out of date.  the links in the result will take you to the text of the article as
    six degrees saw it, not the current version.</p>

    <p>six degrees will try to show an excerpt of about 100 characters from the article, where the link to the next
    article occurred, so you can see the context of the link.  in some cases this may not be possible: the link
    might be part of a template, or it might be using a form of wiki markup that six degrees doesn't understand.</p>

    <p>six degrees takes its name from <a href="http://en.wikipedia.org/wiki/Six_degrees_of_separation">six degrees
    of separation</a>, the theory that everyone is the world is connected by no more than six degrees.  however,
    this only goes as far as the name.  paths between articles have been found which are nine, ten or more degrees.</p>

    <p>six degrees was written by River Tarnell, [<tt>river</tt> (at) <tt>attenuate</tt> (dot) <tt>org</tt>], and
    incorporates suggestions and bug fixes from numerous users.  please feel free to contact me if you have any
    suggestions for six degrees, or if you've found something that doesn't work right (i try to reply to all my
    mail, but i may take a long time to get back to you).</p>

    <p><strong>technical details</strong>: the core of six degrees is a breadth-first search implemented in 45
    lines of C++ code.  including all of the support infrastructure, six degrees is about 2,000 lines of C++
    and 500 lines of Java.  because the graph being searched is so large, the backend runs as a server process
    called <em>linksd</em>.  linksd accepts network connections from clients, calculates the path, and returns
    it.  the web-based frontend is such a client, implemented as a Java servlet.  (i'm not all that fond of the
    Java language, but for web applications, servlets are a nice environment).  linksd is multi-threaded so 
    it can make the best use of its host system, a 2-CPU Opteron system.  originally, the graph was held entirely
    in memory by linksd; however, not only did reading the graph from disk make startup very slow, it was so
    large that linksd used nearly 1.5GB of memory.  the current version stores the graph on disk, using Oracle
    Berkeley DB.</p>

  </div>
</div>

<%-- footer --%>
<a href="/"><img
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
<span class='version'>Front-end version: $Revision: 20889 $]
</div>
</body>
</html>
