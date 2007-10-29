<%-- Copyright (c) 2007 River Tarnell <river@wikimedia.org>. --%>
<%--
 Permission is granted to anyone to use this software for any purpose,
 including commercial applications, and to alter it and redistribute it
 freely. This software is provided 'as-is', without any express or implied
 warranty.
--%>
<%@page contentType="text/html"%>
<%@page pageEncoding="UTF-8"%>
<%@taglib uri="http://java.sun.com/jsp/jstl/core" prefix="c" %> 
<%@taglib uri="http://java.sun.com/jsp/jstl/fmt" prefix="fmt" %>
<%@taglib uri="http://java.sun.com/jsp/jstl/functions" prefix="fn" %>
<fmt:setLocale value="${sessionScope['lang']}" />
<fmt:setBundle basename="i18n" />
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><fmt:message key="frontpage.title" /></title>
        <link rel="stylesheet" href="<c:url value='/static/main.css'/>" />
    </head>
    <body>

    <h1><fmt:message key="frontpage.title" /></h1>
    
    <h2><fmt:message key="frontpage.login.header" /></h2>
    
    <c:if test="${!empty loginerror}">
        <div style='text-align: center'>
            <span class='errorbox'>
                <fmt:message key="frontpage.errormsg">
                    <fmt:param value="${fn:escapeXml(loginerror)}" />
                </fmt:message>
            </span>
        </div>
    </c:if>

    <form action="<c:url value='/' />" method="post">
        <table class="loginform" cellpadding="5" cellspacing="0">
            <tr>
                <th><fmt:message key="frontpage.label.username" /></th>
                <td><input type="text" name="username" 
                               value="<c:out value="${param.username}"/>"/>
                    <c:if test="${validate && empty param.username}">
                        <span class="error"><fmt:message key="frontpage.error.required" /></span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th><fmt:message key="frontpage.label.password" /></th>
                <td><input type="password" name="password" 
                               value="<c:out value="${param.password}"/>"/>
                    <c:if test="${validate && empty param.password}">
                        <span class="error"><fmt:message key="frontpage.error.required" /></span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="submit">
                    <input type="submit" name="login" 
                        value="<fmt:message key="frontpage.label.login" />" />
                </td>
            </tr>
        </table>
    </form>
    
    <h2><fmt:message key="frontpage.create.header" /></h2>
    
    <c:if test="${!empty createerror}">
        <div style='text-align: center'>
            <span class='errorbox'>
                <fmt:message key="frontpage.errormsg">
                    <fmt:param value="${fn:escapeXml(createerror)}" />
                </fmt:message>
            </span>
        </div>
    </c:if>

    <form action="<c:url value='/' />" method="post">
        <table class="loginform" cellpadding="5" cellspacing="0">
            <tr>
                <th><fmt:message key="frontpage.label.username" /></th>
                <td><input type="text" name="username" 
                               value="<c:out value="${param.username}"/>"/>
                    <c:if test="${validate && empty param.username}">
                        <span class="error"><fmt:message key="frontpage.error.required" /></span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th><fmt:message key="frontpage.label.password" /></th>
                <td><input type="password" name="password" 
                               value="<c:out value="${param.password}"/>"/>
                    <c:if test="${validate && empty param.password}">
                        <span class="error"><fmt:message key="frontpage.error.required" /></span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th><fmt:message key="frontpage.label.email" /></th>
                <td><input type="text" name="email" 
                               value="<c:out value="${param.email}"/>"/>
                    <c:if test="${validate && empty param.email}">
                        <span class="error"><fmt:message key="frontpage.error.required" /></span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th><fmt:message key="frontpage.label.firstname" /></th>
                <td><input type="text" name="firstname" 
                               value="<c:out value="${param.firstname}"/>"/>
                    <c:if test="${validate && empty param.firstname}">
                        <span class="error"><fmt:message key="frontpage.error.required" /></span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <th><fmt:message key="frontpage.label.lastname" /></th>
                <td><input type="text" name="lastname" 
                               value="<c:out value="${param.lastname}"/>"/>
                    <c:if test="${validate && empty param.lastname}">
                        <span class="error"><fmt:message key="frontpage.error.required" /></span>
                    </c:if>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="submit">
                    <input type="submit" name="create" 
                           value="<fmt:message key="frontpage.create.submit" />" />
                </td>
            </tr>
        </table>
    </form>
    
            <%@include file="langselect.jsp" %>
        
    </body>
</html>
