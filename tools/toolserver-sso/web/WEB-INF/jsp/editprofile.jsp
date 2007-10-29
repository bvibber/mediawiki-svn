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
        <link rel="stylesheet" href="<c:url value='/static/main.css'/>" />
        <title>Edit your profile - Toolserver SSO</title>
    </head>
    <body>

        <h1>Edit your profile - Toolserver SSO</h1>
        <jsp:include page="banner.jsp" />
        
        <p>You can
            <a href="<c:url value='/secure/profile' />">view your profile</a> or
            <a href="<c:url value='/secure/changepassword' />">change your password</a>.
        </p>
        
        <c:if test="${!empty error}">
            <div style="text-align: center">
                <c:out value="${error}" />
            </div>
        </c:if>
        
        <form method="post" action="<c:url value='/secure/editprofile' />">
            
            <table class="profiletable">
                
                <tr>
                    <th>Display name:</th>
                    <td><input type="text" name="displayname" value="<c:out value="${displayname}" />" /></td>
                </tr>
                <tr>
                    <th>First name:</th>
                    <td><input type="text" name="firstname" value="<c:out value="${firstname}" />" /></td>
                </tr>
                <tr>
                    <th>Last name:</th>
                    <td><input type="text" name="lastname" value="<c:out value="${lastname}" />" /></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><input type="text" name="email" value="<c:out value="${email}" />" /></td>
                </tr>
            
                <tr>
                    <td colspan="2" class="submit">
                        <input type="submit" value="Submit changes" />
                    </td>
                </tr>
            </table>
            
        </form>
        
    </body>
</html>
