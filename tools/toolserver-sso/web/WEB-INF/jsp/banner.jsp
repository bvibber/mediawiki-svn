<%-- Copyright (c) 2007 River Tarnell <river@wikimedia.org>. --%>
<%--
 Permission is granted to anyone to use this software for any purpose,
 including commercial applications, and to alter it and redistribute it
 freely. This software is provided 'as-is', without any express or implied
 warranty.
--%>
<%@page pageEncoding="UTF-8"%>
<%@taglib uri="http://java.sun.com/jsp/jstl/core" prefix="c"%> 

<form action="<c:url value='/secure/logout' />" method="post">
    
    <div class="banner">
        Logged in as <c:out value="${username}" /> -
        <input type="submit" class="flat" value="log out"/>
    </div>
    
</form>