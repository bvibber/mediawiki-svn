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
<fmt:setBundle basename="i18n" />

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="<c:url value='/static/main.css'/>" />
        <title><fmt:message key="editprofile.title" /></title>
    </head>
    <body>

        <h1><fmt:message key="editprofile.title" /></h1>
        <jsp:include page="banner.jsp" />

        <p>
            <fmt:message key="editprofile.header">
                <fmt:param><c:url value='/secure/profile' /></fmt:param>
                <fmt:param><c:url value='/secure/changepassword' /></fmt:param>
            </fmt:message>
        </p>
        
        <c:if test="${!empty error}">
            <div style="text-align: center">
                <c:out value="${error}" />
            </div>
        </c:if>
        
        <form method="post" action="<c:url value='/secure/editprofile' />">
            
            <table class="profiletable">
                
                <tr>
                    <th><fmt:message key="editprofile.label.displayname" /></th>
                    <td><input type="text" name="displayname" value="<c:out value="${displayname}" />" /></td>
                </tr>
                <tr>
                    <th><fmt:message key="editprofile.label.firstname" /></th>
                    <td><input type="text" name="firstname" value="<c:out value="${firstname}" />" /></td>
                </tr>
                <tr>
                    <th><fmt:message key="editprofile.label.lastname" /></th>
                    <td><input type="text" name="lastname" value="<c:out value="${lastname}" />" /></td>
                </tr>
                <tr>
                    <th><fmt:message key="editprofile.label.email" /></th>
                    <td><input type="text" name="email" value="<c:out value="${email}" />" /></td>
                </tr>
            
                <tr>
                    <td colspan="2" class="submit">
                        <input type="submit" value="<fmt:message key="editprofile.label.submit" />" />
                    </td>
                </tr>
            </table>
            
        </form>
        
    </body>
</html>
