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
import java.util.*;
import java.text.*;
import javax.servlet.*;
import javax.servlet.http.*;

import javax.servlet.Filter;
import javax.servlet.FilterChain;
import javax.servlet.FilterConfig;
import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;

public class LocaleFilter implements Filter {
    private FilterConfig filterConfig = null;
    
    public void doFilter(ServletRequest request, ServletResponse response,
            FilterChain chain)
            throws IOException, ServletException {
        
        HttpSession sess = ((HttpServletRequest) request).getSession();
        if (sess.getAttribute("lang") == null)
            sess.setAttribute("lang", "en");
        response.setLocale(new Locale((String) sess.getAttribute("lang")));
        
        chain.doFilter(request, response);
    }
    
    
    public FilterConfig getFilterConfig() {
        return (this.filterConfig);
    }
    
    
    public void setFilterConfig(FilterConfig filterConfig) {
        this.filterConfig = filterConfig;
    }
    
    public void destroy() {
    }
    
    public void init(FilterConfig filterConfig) {
        this.filterConfig = filterConfig;
    }
}
