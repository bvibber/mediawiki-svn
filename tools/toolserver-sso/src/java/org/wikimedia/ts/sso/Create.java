/* Copyright (c) 2007 River Tarnell <river@wikimedia.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
package org.wikimedia.ts.sso;

import com.atlassian.crowd.integration.authentication.PasswordCredential;
import com.atlassian.crowd.integration.model.RemotePrincipal;
import com.atlassian.crowd.integration.service.soap.client.SecurityServerClient;
import com.atlassian.crowd.integration.soap.SOAPAttribute;
import com.atlassian.crowd.integration.soap.SOAPPrincipal;
import java.io.*;
import java.net.*;

import javax.servlet.*;
import javax.servlet.http.*;

public class Create extends HttpServlet {
     public SOAPAttribute buildAttribute(String key, String value) {
         SOAPAttribute attribute = new SOAPAttribute();

         attribute.setName(key);
         attribute.setValues(new String[1]);
         attribute.getValues()[0] = value;

         return attribute;
     }


    public void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        request.setAttribute("validate", new Boolean(true));
        
        String username = request.getParameter("username"),
                password = request.getParameter("password"),
                email = request.getParameter("email"),
                firstname = request.getParameter("firstname"),
                lastname = request.getParameter("lastname");
                
        if (username.equals("") || password.equals("") || email.equals("")
            || firstname.equals("") || lastname.equals("")) {
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher("/index.jsp");
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
            
            request.setAttribute("error", "Username already exists");
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher("/index.jsp");
            dispatcher.forward(request,response);
            return;
        } catch (Exception e) {}
        
        try {
            principal = SecurityServerClient.addPrincipal(principal, credentials);
            SecurityServerClient.addPrincipalToGroup(username, "jira-users");
            SecurityServerClient.addPrincipalToGroup(username, "confluence-users");
        } catch (Exception e) {
            request.setAttribute("error", e.getMessage());
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher("/index.jsp");
            dispatcher.forward(request,response);
            return;
        }
        
        RequestDispatcher dispatcher = getServletContext().getRequestDispatcher("/WEB-INF/jsp/createdone.jsp");
        request.setAttribute("username", request.getParameter("username"));
        dispatcher.forward(request,response);
    }
}
