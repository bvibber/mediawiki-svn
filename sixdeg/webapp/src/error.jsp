<%-- vim:et sw=2 ts=2:
  Six degrees of Wikipedia: JSP front-end.
  This source code is released into the public domain.

  From: @(#)index.jsp	1.19 06/10/16 01:17:11
  $Id$
--%>
<%@ taglib prefix="s" uri="/struts-tags" %>
<s:include value="/header.jsp" />

<center>
  <div class='error'>
    <span class='error'>error:</span>
    <span class='errtext'><s:property value="error" /></span>
  </div>
</center>

<s:include value="/footer.jsp" />
