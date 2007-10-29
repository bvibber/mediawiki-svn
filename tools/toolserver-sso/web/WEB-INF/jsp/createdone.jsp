<%-- Copyright (c) 2007 River Tarnell <river@wikimedia.org>. --%>
<%--
 Permission is granted to anyone to use this software for any purpose,
 including commercial applications, and to alter it and redistribute it
 freely. This software is provided 'as-is', without any express or implied
 warranty.
--%>
<%@page contentType="text/html"%>
<%@page pageEncoding="UTF-8"%>
<%@taglib uri="http://java.sun.com/jsp/jstl/core" prefix="c"%> 
<%@taglib uri="http://java.sun.com/jsp/jstl/fmt" prefix="fmt" %>
<%@taglib uri="http://java.sun.com/jsp/jstl/functions" prefix="fn" %>
<fmt:setLocale value="${sessionScope['lang']}" />
<fmt:setBundle basename="i18n" />

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><fmt:message key="createdone.title" /></title>
        <link rel="stylesheet" href="<c:url value='/static/main.css'/>" />
    </head>
    <body>

    <h1><fmt:message key="createdone.title" /></h1>
    
    <jsp:include page="banner.jsp" />

    <p>
        <fmt:message key="createdone.text">
            <fmt:param value="${fn:escapeXml(username)}" />
        </fmt:message>
    </p>

    <ul>
        <li><a href="https://jira.ts.wikimedia.org"><fmt:message key="createdone.jira" /></a></li>
        <li><a href="http://fisheye.ts.wikimedia.org"><fmt:message key="createdone.fisheye" /></a></li>
        <li><a href="http://wiki.ts.wikimedia.org"><fmt:message key="createdone.mediawiki" /></a></li>
        <li><a href="http://confluence.ts.wikimedia.org"><fmt:message key="createdone.confluence" /></a></li>
    </ul>
    
        <p>
            <fmt:message key="createdone.manage">
                <fmt:param><c:url value='/secure/profile' /></fmt:param>
            </fmt:message>
        </p>
        
        <%@include file="langselect.jsp" %>

    </body>
</html>
