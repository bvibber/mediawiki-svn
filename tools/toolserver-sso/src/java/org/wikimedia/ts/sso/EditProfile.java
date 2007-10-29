package org.wikimedia.ts.sso;

import com.atlassian.crowd.integration.http.HttpAuthenticator;
import com.atlassian.crowd.integration.model.RemotePrincipal;
import com.atlassian.crowd.integration.service.soap.client.SecurityServerClient;
import com.atlassian.crowd.integration.soap.SOAPAttribute;
import com.atlassian.crowd.integration.soap.SOAPPrincipal;
import java.io.*;
import java.net.*;

import javax.servlet.*;
import javax.servlet.http.*;

public class EditProfile extends HttpServlet {
        
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        SOAPPrincipal principal = null;
        SOAPAttribute[] attributes = null;
        String username = null, firstname = null, lastname = null, email = null,
                displayname = null;
        
        try {
            principal = HttpAuthenticator.getPrincipal(request);
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
        request.setAttribute("email", email);
        request.setAttribute("firstname", firstname);
        request.setAttribute("lastname", lastname);
        request.setAttribute("displayname", displayname);
        
        RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/editprofile.jsp");
        dispatcher.forward(request, response);
    }

    public static SOAPAttribute buildAttribute(String key, String value) {
         SOAPAttribute attribute = new SOAPAttribute();

         attribute.setName(key);
         attribute.setValues(new String[1]);
         attribute.getValues()[0] = value;

         return attribute;
     }

    protected void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        String
                newemail = request.getParameter("email"),
                newdisplayname = request.getParameter("displayname"),
                newfirstname = request.getParameter("firstname"),
                newlastname = request.getParameter("lastname");
        
        try {
            SOAPPrincipal p = HttpAuthenticator.getPrincipal(request);
            String username = p.getName();

            SecurityServerClient.updatePrincipalAttribute(
                    username, buildAttribute(RemotePrincipal.DISPLAYNAME, newdisplayname));
            SecurityServerClient.updatePrincipalAttribute(
                    username, buildAttribute(RemotePrincipal.EMAIL, newemail));
            SecurityServerClient.updatePrincipalAttribute(
                    username, buildAttribute(RemotePrincipal.FIRSTNAME, newfirstname));
            SecurityServerClient.updatePrincipalAttribute(
                    username, buildAttribute(RemotePrincipal.LASTNAME, newlastname));
        } catch (Exception e) {
            request.setAttribute("error", e.getMessage());
            RequestDispatcher dispatcher = getServletContext().getRequestDispatcher(
                "/WEB-INF/jsp/editprofile.jsp");
            dispatcher.forward(request, response);
            return;
        }
        
        response.sendRedirect(request.getContextPath() + "/secure/profile");
    }
}
