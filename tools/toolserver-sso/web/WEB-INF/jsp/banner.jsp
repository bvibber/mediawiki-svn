<%-- Copyright (c) 2007 River Tarnell <river@wikimedia.org>. --%>
<%--
 Permission is granted to anyone to use this software for any purpose,
 including commercial applications, and to alter it and redistribute it
 freely. This software is provided 'as-is', without any express or implied
 warranty.
--%>
<%@page pageEncoding="UTF-8"%>
<%@taglib uri="http://java.sun.com/jsp/jstl/core" prefix="c"%> 
<%@taglib uri="http://java.sun.com/jsp/jstl/fmt" prefix="fmt" %>
<%@taglib uri="http://java.sun.com/jsp/jstl/functions" prefix="fn" %>
<fmt:setBundle basename="i18n" />

<form action="<c:url value='/secure/logout' />" method="post">
    
    <div class="banner">
        <fmt:message key="banner.text">
            <fmt:param value="${fn:escapeXml(username)}" />
        </fmt:message>
        -
        <input type="submit" class="flat" 
            value="<fmt:message key="banner.logout" />"/>
    </div>
    
</form>