/*
 * FrontPage.java
 *
 * Created on 28 October 2007, 14:46
 *
 * To change this template, choose Tools | Template Manager
 * and open the template in the editor.
 */

package org.wikimedia.ts.sso;

import com.atlassian.crowd.integration.authentication.PasswordCredential;
import com.atlassian.crowd.integration.exception.InvalidAuthenticationException;
import com.atlassian.crowd.integration.http.HttpAuthenticator;
import com.atlassian.crowd.integration.http.VerifyTokenFilter;
import com.atlassian.crowd.integration.model.RemotePrincipal;
import com.atlassian.crowd.integration.service.soap.client.SecurityServerClient;
import com.atlassian.crowd.integration.soap.SOAPAttribute;
import com.atlassian.crowd.integration.soap.SOAPPrincipal;
import java.io.IOException;
import javax.servlet.*;
import javax.servlet.http.*;
import org.codehaus.xfire.fault.XFireFault;

public class FrontPage extends HttpServlet {
    /*
     * GET - show the login form, or display the profile page.
     */
    public void doGet(HttpServletRequest request, HttpServletResponse response) 
    throws ServletException, IOException {
        RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/login.jsp");
        dispatcher.forward(request, response);
    }
    
    /*
     * POST - user is logging in or creating an account.
     */
    public void doPost(HttpServletRequest request, HttpServletResponse response) 
    throws ServletException, IOException {
        if (request.getParameter("login") != null)
            doLogin(request, response);
        else
            doCreate(request, response);
    }
    
    public void doLogin(HttpServletRequest request, HttpServletResponse response) 
    throws ServletException, IOException {
        String username = request.getParameter("username");
        String password = request.getParameter("password");
        
        try {
            HttpAuthenticator.authenticate(request, response, username, password);
        } catch (Exception e) {
            /* Login fails */
            Exception n = e;
            while (n.getCause() != null)
                n = (Exception) n.getCause();
            
            request.setAttribute("loginerror", n.toString());
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                    "/WEB-INF/jsp/login.jsp");
            dispatcher.forward(request, response);
            return;
        }

        String requestingPage = (String) request.getSession().getAttribute(
                VerifyTokenFilter.ORIGINAL_URL);
        if (requestingPage != null)
            response.sendRedirect(requestingPage);
        else
            response.sendRedirect(request.getContextPath() + "/secure/profile");
    }
    
     public static SOAPAttribute buildAttribute(String key, String value) {
         SOAPAttribute attribute = new SOAPAttribute();

         attribute.setName(key);
         attribute.setValues(new String[1]);
         attribute.getValues()[0] = value;

         return attribute;
     }


    public void doCreate(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        request.setAttribute("validate", new Boolean(true));
        
        String username = request.getParameter("username"),
                password = request.getParameter("password"),
                email = request.getParameter("email"),
                firstname = request.getParameter("firstname"),
                lastname = request.getParameter("lastname");
                
        if (username.equals("") || password.equals("") || email.equals("")
            || firstname.equals("") || lastname.equals("")) {
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                    "/WEB-INF/jsp/login.jsp");
            dispatcher.forward(request,response);
            return;
        }
        /*
         * Try to create the user.
         */
        SOAPPrincipal principal;
        principal = new SOAPPrincipal();
        principal.setActive(true);
        principal.setName(request.getParameter("username"));

        SOAPAttribute[] soapAttributes = new SOAPAttribute[4];

        soapAttributes[0] = buildAttribute(RemotePrincipal.EMAIL,
                                request.getParameter("email"));
        soapAttributes[1] = buildAttribute(RemotePrincipal.FIRSTNAME,
                                request.getParameter("firstname"));
        soapAttributes[2] = buildAttribute(RemotePrincipal.LASTNAME,
                                request.getParameter("lastname"));
        soapAttributes[3] = buildAttribute(RemotePrincipal.DISPLAYNAME, 
                                request.getParameter("firstname") +
                                request.getParameter("lastname"));

        principal.setAttributes(soapAttributes);

        PasswordCredential credentials = new PasswordCredential();
        credentials.setCredential(request.getParameter("password"));

        /*
         * Give an error if the user already exists.
         */
        try {
            SecurityServerClient.findPrincipalByName(username);
            
            request.setAttribute("createerror", "Username already exists");
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                    "/WEB-INF/jsp/login.jsp");
            dispatcher.forward(request,response);
            return;
        } catch (Exception e) {}
        
        try {
            principal = SecurityServerClient.addPrincipal(principal, credentials);
            SecurityServerClient.addPrincipalToGroup(username, "jira-users");
            SecurityServerClient.addPrincipalToGroup(username, "confluence-users");
        } catch (Exception e) {
            request.setAttribute("createerror", e.getMessage());
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                    "/WEB-INF/jsp/login.jsp");
            dispatcher.forward(request,response);
            return;
        }
        
        RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/createdone.jsp");
        request.setAttribute("username", request.getParameter("username"));
        dispatcher.forward(request,response);
    }
}