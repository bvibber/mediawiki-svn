package org.mediawiki.scavenger.action;

import java.io.IOException;
import java.sql.SQLException;

import javax.servlet.RequestDispatcher;
import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.jsp.PageContext;

import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.User;
import org.mediawiki.scavenger.Wiki;

public abstract class PageAction extends HttpServlet {
	protected HttpServletResponse resp;
	protected HttpServletRequest req;
	protected ServletContext ctx;
	protected Wiki wiki;
	protected Title title;
	protected User user;

	public void doPost(HttpServletRequest rq, HttpServletResponse rp) 
	throws ServletException, IOException {
		req = rq;
		resp = rp;
		ctx = getServletContext();

		try {
			doExecute();
		} catch (Exception e) {
			throw new ServletException(e);
		} finally {
			try {
				cleanup();
			} catch (Exception e) {
				throw new ServletException(e);
			}
		}
	}

	public void doGet(HttpServletRequest rq, HttpServletResponse rp) 
	throws ServletException, IOException {
		req = rq;
		resp = rp;
		ctx = getServletContext();
		
		try {
			doExecute();
		} catch (Exception e) {
			throw new ServletException(e);
		} finally {
			try {
				cleanup();
			} catch (Exception e) {
				throw new ServletException(e);
			}
		}
	}	
	
	abstract protected String pageExecute() throws Exception;
	
	public void showError(String e) {
		req.setAttribute("errormsg", e);
		RequestDispatcher disp = ctx.getRequestDispatcher("/WEB-INF/tpl/error.jsp");
		
		try {
			disp.include(req, resp);
		} catch (Exception ex) {}
	}

	void cleanup() {
		try {
			wiki.rollback();
		} catch (Exception e) {}
		try {
			wiki.close();
		} catch (Exception e) {}
	}

	Title getRequestTitle() {
		Title t = null;
		String name = req.getParameter("title");
		if (name == null) {
			name = req.getPathInfo();
			if (name != null)
				name = name.substring(1);
		}
		
		if (name != null)
			t = wiki.getTitle(name);
		
		return t;
	}
	
	public void doExecute() {
		try {
			wiki = Wiki.getWiki(ctx, req);
		} catch (Exception e) {
			showError("Error retrieving database connection: " + e.toString());
			return;
		}
		
		title = null;
		req.setAttribute("wiki", wiki);
		
		try {
			user = wiki.getUser(req.getRemoteAddr(), true);
			title = getRequestTitle();

			if (title != null && !title.isValidName()) {
				showError("The page name you have requested is illegal.");
				return;
			}
			
			req.setAttribute("title", title);
			req.setAttribute("user", user);
			String ret = pageExecute();
			
			if (ret != null) {
				RequestDispatcher disp = ctx.getRequestDispatcher("/WEB-INF/tpl/" + ret + ".jsp");
				disp.include(req, resp);
			}
			return;
		} catch (Exception e) {
			showError("Page execution failed: " + e.toString());
		}
	}
}
