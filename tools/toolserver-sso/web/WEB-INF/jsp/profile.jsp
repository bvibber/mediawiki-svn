<%@page contentType="text/html"%>
<%@page pageEncoding="UTF-8"%>
<%@taglib uri="http://java.sun.com/jsp/jstl/core" prefix="c"%> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="<c:url value='/static/main.css'/>" />
        <title>Your profile - Toolserver SSO</title>
    </head>
    <body>

        <h1>Your profile - Toolserver SSO</h1>
        <jsp:include page="banner.jsp" />
        
        <p>You can
            <a href="<c:url value='/secure/editprofile' />">edit your profile</a> or
            <a href="<c:url value='/secure/changepassword' />">change your password</a>.
        </p>
        
        <table class="profiletable">
            
            <tr><th>Username:</th><td><c:out value="${username}" /></td></tr>
            <tr><th>Display name:</th><td><c:out value="${displayname}" /></td></tr>
            <tr><th>First name:</th><td><c:out value="${firstname}" /></td></tr>
            <tr><th>Last name:</th><td><c:out value="${lastname}" /></td></tr>
            <tr><th>Email:</th><td><c:out value="${email}" /></td></tr>
        
            <tr>
                <th>
                    Groups:
                </th>
                <td>
                    <c:forEach items="${groups}" var="group">
                        <c:out value="${group}" /> <br />
                    </c:forEach>
                </td>
            </tr>
        </table>

    </body>
</html>
