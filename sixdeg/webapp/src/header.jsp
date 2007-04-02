<%-- vim:et sw=2 ts=2:
  Six degrees of Wikipedia: JSP front-end.
  This source code is released into the public domain.

  From: @(#)index.jsp	1.19 06/10/16 01:17:11
  $Id$
--%>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<%@ taglib prefix="s" uri="/struts-tags" %>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Six degrees of Wikipedia</title>

    <meta name="robots" content="index" />
    <meta name="robots" content="follow" />
    <link rel="stylesheet" href="main.css" />
  </head>

<body>

<%-- logo / preamble --%>
<div style="text-align: right; padding: 0px; margin: 0px"><img
  src="6deg.png" alt="" /><br/></div>

<div style="text-align: center">
<i>a <a href="http://en.wikipedia.org/wiki/Shortest_path">shortest path</a>
query solver for the English
<a href="http://en.wikipedia.org/wiki/Main_Page">Wikipedia</a>...</i><br/>
<i>six degrees</i> finds the shortest path between any two Wikipedia articles in the
main namespace using wiki links
</div>

<%-- input form --%>
<div style="padding-top: 35px;">

  <div style="width: 40%; margin-left: auto; margin-right: auto;">
    <s:form cssClass="pathfinder" action="pathfinder" method="get" acceptcharset="UTF-8">
      <strong>find path...</strong>
      <s:textfield label="from" name="from" value="%{#parameters.from}" />
      <s:textfield label="to" name="to" value="%{#parameters.to}" />
      <s:select label="wiki" name="wiki" list="wikimap" value="%{(#parameters.wiki == null) ? 'enwiki_p' : #parameters.wiki[0]}" />
      <s:checkbox label="ignore date and year articles" name="ign_dates" />
      <s:submit value="go" />
    </s:form>
  </div>

  <s:url id="reverse" action="pathfinder">
    <s:param name="from" value="%{#parameters.from}" />
    <s:param name="to" value="%{#parameters.to}" />
    <s:param name="ign_dates" value="%{#parameters.ign_dates}" />
  </s:url>
