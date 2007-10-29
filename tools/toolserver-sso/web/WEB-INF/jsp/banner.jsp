<%@page pageEncoding="UTF-8"%>
<%@taglib uri="http://java.sun.com/jsp/jstl/core" prefix="c"%> 

<form action="<c:url value='/secure/logout' />" method="post">
    
    <div class="banner">
        Logged in as <c:out value="${username}" /> -
        <input type="submit" class="flat" value="log out"/>
    </div>
    
</form>