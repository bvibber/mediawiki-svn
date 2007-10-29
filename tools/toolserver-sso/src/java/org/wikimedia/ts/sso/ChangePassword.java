package org.wikimedia.ts.sso;

import com.atlassian.crowd.integration.authentication.PasswordCredential;
import com.atlassian.crowd.integration.authentication.PrincipalAuthenticationContext;
import com.atlassian.crowd.integration.http.HttpAuthenticator;
import com.atlassian.crowd.integration.service.soap.client.SecurityServerClient;
import com.atlassian.crowd.integration.soap.SOAPPrincipal;
import java.io.*;
import java.net.*;

import javax.servlet.*;
import javax.servlet.http.*;

public class ChangePassword extends HttpServlet {
    
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        String username = null;
        SOAPPrincipal principal = null;
        
        try {
            principal = HttpAuthenticator.getPrincipal(request);
        } catch (Exception e) {
            response.sendRedirect(request.getContextPath());
            return;
        }

        username = principal.getName();

        request.setAttribute("username", username);
        RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
            "/WEB-INF/jsp/changepassword.jsp");
        dispatcher.forward(request, response);
    }
    
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        String  oldpass = request.getParameter("curpass"),
                newpass1 = request.getParameter("newpass1"),
                newpass2 = request.getParameter("newpass2");
        SOAPPrincipal principal = null;
        String username = null;
        
        try {
            principal = HttpAuthenticator.getPrincipal(request);
        } catch (Exception e) {
            response.sendRedirect(request.getContextPath());
            return;
        }

        username = principal.getName();
        request.setAttribute("username", username);

        if (!newpass1.equals(newpass2)) {
            request.setAttribute("error", "Passwords don't match");
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/changepassword.jsp");
            dispatcher.forward(request, response);
            return;
        }
        
        if (newpass1.length() == 0) {
            request.setAttribute("error", "New password cannot be empty");
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/changepassword.jsp");
            dispatcher.forward(request, response);
            return;            
        }
        
        try {
            PrincipalAuthenticationContext ctx = null;
            ctx = HttpAuthenticator.getPrincipalAuthenticationContext(request, response, username, oldpass);
            SecurityServerClient.authenticatePrincipal(ctx);
        } catch (Exception e) {
            request.setAttribute("error", "Current password is incorrect");
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/changepassword.jsp");
            dispatcher.forward(request, response);
            return;
        }
        
        PasswordCredential passcred = new PasswordCredential();
        passcred.setCredential(newpass1);
        try {
            SecurityServerClient.updatePrincipalCredential(username, passcred);
        } catch (Exception e) {
            request.setAttribute("error", "Internal error occured");
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/changepassword.jsp");
            dispatcher.forward(request, response);
            return;            
        }
        response.sendRedirect(request.getContextPath() + "/secure/profile");
    }
}