<%-- vim:et sw=2 ts=2:
  Six degrees of Wikipedia: JSP front-end.
  This source code is released into the public domain.

  From: @(#)index.jsp	1.19 06/10/16 01:17:11
  $Id$
--%>
<%@ taglib prefix="s" uri="/struts-tags" %>

<s:include value="/header.jsp" />

<div class='result'>
  <div class='answer'>
    <s:property value="path.length" /> degrees of separation
  </div>

  <%-- for each hop... --%>
  <s:iterator value="path">
    <s:url includeParams="none" id="articleurl" value="http://en.wikipedia.org/w/index.php">
      <s:param name="title" value="article" />
      <s:param name="oldid" value="id"/>
    </s:url>

    <span class="art">
      <s:a href="%{articleurl}"><s:property value="article" /></s:a>
    </span>
    <br/>
    <span class="context">
      <s:property value="context" />
    </span>
    </br>
  </s:iterator>

</div>

<%-- Return path link --%>
<p class='return'>
  <s:a href="%{reverse}">View the return path?</s:a>
</p>

<s:include value="/footer.jsp" />
