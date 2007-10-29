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
        <link rel="stylesheet" href="<c:url value='/static/main.css'/>" />
        <title><fmt:message key="profile.title" /></title>
    </head>
    <body>

        <h1><fmt:message key="profile.title" /></h1>
        
        <jsp:include page="banner.jsp" />
        
        <p>
            <fmt:message key="profile.header">
                <fmt:param><c:url value='/secure/editprofile' /></fmt:param>
                <fmt:param><c:url value='/secure/changepassword' /></fmt:param>
            </fmt:message>
        </p>
        
        <table class="profiletable">
            
            <tr><th><fmt:message key="profile.label.username" /></th><td><c:out value="${username}" /></td></tr>
            <tr><th><fmt:message key="profile.label.displayname" /></th><td><c:out value="${displayname}" /></td></tr>
            <tr><th><fmt:message key="profile.label.firstname" /></th><td><c:out value="${firstname}" /></td></tr>
            <tr><th><fmt:message key="profile.label.lastname" /></th><td><c:out value="${lastname}" /></td></tr>
            <tr><th><fmt:message key="profile.label.email" /></th><td><c:out value="${email}" /></td></tr>
        
            <tr>
                <th><fmt:message key="profile.label.groups" /></th>
                <td>
                    <c:forEach items="${groups}" var="group">
                        <c:out value="${group.name}" />
                        <c:if test="${!empty group.description}">
                            (<c:out value="${group.description}" />)
                        </c:if>
                        <br />
                    </c:forEach>
                </td>
            </tr>
        </table>

        <%@include file="langselect.jsp" %>

    </body>
</html>
