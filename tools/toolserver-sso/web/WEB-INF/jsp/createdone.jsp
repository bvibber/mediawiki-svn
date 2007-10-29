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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Account created - Toolserver SSO</title>
        <link rel="stylesheet" href="<c:url value='/static/main.css'/>" />
    </head>
    <body>

    <h1>Account created - Toolserver SSO</h1>
    
    <jsp:include page="banner.jsp" />
    
    <p>Congratulations, your new account <strong><c:out value="${username}" /></strong>
    has been created.  You can now log into:</p>
    <ul>
        <li><a href="https://jira.ts.wikimedia.org">JIRA,</a></li>
        <li><a href="http://fisheye.ts.wikimedia.org">FishEye,</a></li>
        <li><a href="http://wiki.ts.wikimedia.org">MediaWiki,</a></li>
        <li><a href="http://confluence.ts.wikimedia.org">and Confluence.</a></li>
    </ul>
    
        <p>From here, you might want to
            <a href="<c:url value='/secure/profile' />">manage your profile</a>.
        </p>
        
    </body>
</html>
