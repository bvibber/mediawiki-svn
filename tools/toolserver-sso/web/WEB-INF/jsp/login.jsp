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
        <title>Toolserver Single Sign On</title>
        <link rel="stylesheet" href="<c:url value='/static/main.css'/>" />
    </head>
    <body>

    <h1>Toolserver Single Sign On</h1>
    
    <h2>Log in</h2>
    
    <c:if test="${!empty loginerror}">
        <div style='text-align: center'>
            <span class='errorbox'>Error: <c:out value="${loginerror}" /></span>
        </div>
    </c:if>

    <form action="<c:url value='/' />" method="post">
        <table class="loginform" cellpadding="5" cellspacing="0">
            <tr>
                <th>Username:</th>
                <td><input type="text" name="username" 
                               value="<c:out value="${param.username}"/>"/>
                    <c:if test="${validate && empty param.username}">
                        <span class="error">Required</span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th>Password:</th>
                <td><input type="password" name="password" 
                               value="<c:out value="${param.password}"/>"/>
                    <c:if test="${validate && empty param.password}">
                        <span class="error">Required</span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="submit">
        <input type="submit" name="login" value="Log in" />
                </td>
            </tr>
        </table>
    </form>
    
    <h2>Create a new account</h2>
    
    <c:if test="${!empty createerror}">
        <div style='text-align: center'>
            <span class='errorbox'>Error: <c:out value="${createerror}" /></span>
        </div>
    </c:if>

    <form action="<c:url value='/' />" method="post">
        <table class="loginform" cellpadding="5" cellspacing="0">
            <tr>
                <th>Username:</th>
                <td><input type="text" name="username" 
                               value="<c:out value="${param.username}"/>"/>
                    <c:if test="${validate && empty param.username}">
                        <span class="error">Required</span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th>Password:</th>
                <td><input type="password" name="password" 
                               value="<c:out value="${param.password}"/>"/>
                    <c:if test="${validate && empty param.password}">
                        <span class="error">Required</span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th>Email address:</th>
                <td><input type="text" name="email" 
                               value="<c:out value="${param.email}"/>"/>
                    <c:if test="${validate && empty param.email}">
                        <span class="error">Required</span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th>First name:</th>
                <td><input type="text" name="firstname" 
                               value="<c:out value="${param.firstname}"/>"/>
                    <c:if test="${validate && empty param.firstname}">
                        <span class="error">Required</span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th>Last name:</th>
                <td><input type="text" name="lastname" 
                               value="<c:out value="${param.lastname}"/>"/>
                    <c:if test="${validate && empty param.lastname}">
                        <span class="error">Required</span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="submit">
        <input type="submit" name="create" value="Create account" />
                </td>
            </tr>
    </form>
    </body>
</html>
