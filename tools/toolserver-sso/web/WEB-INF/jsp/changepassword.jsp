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
        <title><fmt:message key="changepassword.title" /></title>
        <link rel="stylesheet" href="<c:url value="/static/main.css" />" />
    </head>
    <body>
       
    <h1><fmt:message key="changepassword.title" /></h1>

    <jsp:include page="banner.jsp" />

        <c:if test="${!empty error}">
            <p>
                <span class='errorbox'><c:out value="${error}" /></span>
            </p>
        </c:if>
        
        <form method="post" action="<c:url value="/secure/changepassword" />">
            <table class="profiletable">
                <tr>
                    <th><fmt:message key="changepassword.curpass" /></th>
                    <td><input type="password" name="curpass" /></td>
                </tr>
                
                <tr>
                    <th><fmt:message key="changepassword.newpass1" /></th>
                    <td><input type="password" name="newpass1" /></td>
                </tr>
                
                <tr>
                    <th><fmt:message key="changepassword.newpass2" /></th>
                    <td><input type="password" name="newpass2" /></td>
                </tr>
                
                <tr>
                    <td colspan="2" class="submit">
                        <input type="submit" value="<fmt:message key="changepassword.submit" />" />
                    </td>
                </tr>
            </table>
        </form>
        
        <%@include file="langselect.jsp" %>

    </body>
</html>
