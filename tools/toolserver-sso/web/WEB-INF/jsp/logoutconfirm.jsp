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
        <title>Log out - Toolserver SSO</title>
        <link rel="stylesheet" href="<c:url value='/static/main.css' />" />
    </head>
    <body>

    <h1>Log out</h1>
    
    <jsp:include page="banner.jsp" />
    
    <form method="post" action="<c:url value='/secure/logout' />">
        
        <p>Really log out?
            <input type="submit" value="Log out" /> /
            <a href="<c:url value="/secure/profile" />">return to profile</a>
        </p>
        
    </form>
    
    </body>
</html>
