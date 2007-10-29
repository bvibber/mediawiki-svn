/* Copyright (c) 2007 River Tarnell <river@wikimedia.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
package org.wikimedia.ts.sso;

import java.io.*;
import java.net.*;
import java.util.Locale;

import javax.servlet.*;
import javax.servlet.http.*;

public class SetLang extends HttpServlet {
    public void doPost(HttpServletRequest request, HttpServletResponse response)
    throws ServletException, IOException {
        Locale l = new Locale(request.getParameter("language"));
        System.out.printf("setting locale to %s (%s)\n", request.getParameter("language"), l.toString());
//        request.getSession().setAttribute("locale", l);
        request.getSession().setAttribute("lang", request.getParameter("language"));
        response.sendRedirect(response.encodeRedirectURL(request.getParameter("cururl")));
    }
}
