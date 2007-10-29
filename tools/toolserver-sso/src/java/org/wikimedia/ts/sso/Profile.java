/* Copyright (c) 2007 River Tarnell <river@wikimedia.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
package org.wikimedia.ts.sso;

import com.atlassian.crowd.integration.exception.InvalidAuthorizationTokenException;
import com.atlassian.crowd.integration.http.HttpAuthenticator;
import com.atlassian.crowd.integration.model.RemotePrincipal;
import com.atlassian.crowd.integration.service.soap.client.SecurityServerClient;
import com.atlassian.crowd.integration.soap.SOAPAttribute;
import com.atlassian.crowd.integration.soap.SOAPPrincipal;
import java.io.*;
import java.net.*;

import javax.servlet.*;
import javax.servlet.http.*;

public class Profile extends HttpServlet {
    
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        SOAPPrincipal principal = null;
        SOAPAttribute[] attributes = null;
        String username = null, firstname = null, lastname = null, email = null,
                displayname = null;
        String[] groups = null;
        
        try {
            principal = HttpAuthenticator.getPrincipal(request);
            groups = SecurityServerClient.findGroupMemberships(principal.getName());
            attributes = principal.getAttributes();
        } catch (Exception e) {
            response.sendRedirect(request.getContextPath());
            return;
        }
        username = principal.getName();

        for (SOAPAttribute a: attributes) {
            if (a.getName().equals(RemotePrincipal.EMAIL))
                email = a.getValues()[0];
            else if (a.getName().equals(RemotePrincipal.FIRSTNAME))
                firstname = a.getValues()[0];
            else if (a.getName().equals(RemotePrincipal.LASTNAME))
                lastname = a.getValues()[0];
            else if (a.getName().equals(RemotePrincipal.DISPLAYNAME))
                displayname = a.getValues()[0];
        }
        request.setAttribute("username", username);
        request.setAttribute("groups", groups);
        request.setAttribute("email", email);
        request.setAttribute("firstname", firstname);
        request.setAttribute("lastname", lastname);
        request.setAttribute("displayname", displayname);
        
        RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/profile.jsp");
        dispatcher.forward(request, response);
    }
}
