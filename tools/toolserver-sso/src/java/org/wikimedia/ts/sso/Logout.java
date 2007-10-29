package org.wikimedia.ts.sso;

import com.atlassian.crowd.integration.http.HttpAuthenticator;
import com.atlassian.crowd.integration.soap.SOAPPrincipal;
import java.io.*;
import java.net.*;

import javax.servlet.*;
import javax.servlet.http.*;

public class Logout extends HttpServlet {
    
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        SOAPPrincipal principal = null;
        
        try {
            principal = HttpAuthenticator.getPrincipal(request);
        } catch (Exception e) {
            response.sendRedirect(request.getContextPath());
            return;
        }
        
        String username = principal.getName();
        request.setAttribute("username", username);
        
        RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/logoutconfirm.jsp");
        dispatcher.forward(request, response);
    }
    
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        try {
            HttpAuthenticator.logoff(request, response);
        } catch (Exception e) {}
        response.sendRedirect(request.getContextPath());
    }
}
