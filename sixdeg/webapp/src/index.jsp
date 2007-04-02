<%-- vim:et sw=2 ts=2:
  Six degrees of Wikipedia: JSP front-end.
  This source code is released into the public domain.

  From: @(#)index.jsp	1.19 06/10/16 01:17:11
  $Id$
--%>
<%@ page language="java" contentType="text/html; charset=UTF-8"%>
<%@ taglib prefix="c" uri="http://java.sun.com/jsp/jstl/core" %>
<%
String newURL = "pathfinder.action";
String from = request.getParameter("from");
String to = request.getParameter("to");
String ign = request.getParameter("ign_dates");

if (from != null && to != null)
  newURL = newURL + "?from=" + java.net.URLEncoder.encode(from, "UTF-8")
                  + "&to=" + java.net.URLEncoder.encode(to, "UTF-8");
if (ign != null)
  newURL = newURL + "&ign_dates=" + java.net.URLEncoder.encode(ign, "UTF-8");

response.sendRedirect(newURL);
%>
