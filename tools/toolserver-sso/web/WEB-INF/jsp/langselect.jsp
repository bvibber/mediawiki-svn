<%-- Copyright (c) 2007 River Tarnell <river@wikimedia.org>. --%>
<%--
 Permission is granted to anyone to use this software for any purpose,
 including commercial applications, and to alter it and redistribute it
 freely. This software is provided 'as-is', without any express or implied
 warranty.
--%>
<%--
  Language selector - allows the user to change the interface language.
  Usually displayed on every page.
--%>
<%@taglib uri="http://java.sun.com/jsp/jstl/core" prefix="c"%> 
<%@taglib uri="http://java.sun.com/jsp/jstl/fmt" prefix="fmt" %>
<%@taglib uri="http://java.sun.com/jsp/jstl/functions" prefix="fn" %>
<fmt:setBundle basename="i18n" />

<form action="<c:url value='/setlang' />" method="post">
    
    <hr>
    
    <p class="langselect">
        <c:set var="cururl" 
               value="${fn:escapeXml(requestScope['javax.servlet.forward.request_uri'])}" />
        <input type="hidden" name="cururl" value="${cururl}" />
        <label for="language">
            <fmt:message key="langselect.label.language" />
        </label>
        
        <select id="language" name="language">
            <option value="en"><fmt:message key="langselect.lang.en" /></option>
            <option value="ja"><fmt:message key="langselect.lang.ja" /></option>        
        </select>
        
        <input type="submit" value="<fmt:message key="langselect.label.submit" />" />
    </p>
</form>