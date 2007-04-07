package org.mediawiki.scavenger.action;

import java.io.IOException;
import java.sql.SQLException;

import javax.servlet.RequestDispatcher;
import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.User;
import org.mediawiki.scavenger.Wiki;

public abstract class PageAction extends HttpServlet {
	protected HttpServletResponse resp;
	protected HttpServletRequest req;
	protected ServletContext ctx;
	protected Wiki wiki;
	protected Title title;
	protected String errormsg;
	protected User user;

	public void doPost(HttpServletRequest rq, HttpServletResponse rp) 
	throws ServletException, IOException {
		req = rq;
		resp = rp;
		ctx = getServletContext();
		
		doExecute();
	}

	public void doGet(HttpServletRequest rq, HttpServletResponse rp) 
	throws ServletException, IOException {
		req = rq;
		resp = rp;
		ctx = getServletContext();
		
		doExecute();
	}	
	
	abstract protected String pageExecute() throws Exception;
	
	private void doExecute() throws ServletException, IOException {
		String goTo = execute();
		if (goTo == null)
			return;
		
		RequestDispatcher disp = ctx.getRequestDispatcher("/WEB-INF/tpl/" + goTo + ".jsp");
		disp.forward(req, resp);
	}
	
	public final String execute() {
		try {
			wiki = Wiki.getWiki(ctx, req);
		} catch (Exception e) {
			errormsg = "Error retrieving database connection: " + e.toString();
			return "error";
		}
		title = null;
			
		try {
			user = wiki.getUser(req.getRemoteAddr(), true);

			String name = req.getParameter("title");
			if (name == null) {
				name = req.getPathInfo();
				if (name != null)
					name = name.substring(1);
			}
			
			if (name != null) {
				title = wiki.getTitle(name);
				if (!title.isValidName()) {
					errormsg = "The page name you have requested is illegal.";
					return "error";
				}
			}
			
			req.setAttribute("title", title);
			req.setAttribute("user", user);
			return pageExecute();
		} catch (Exception e) {
			try {
				wiki.rollback();
			} catch (SQLException e2) {}
			errormsg = "Page execution failed: " + e.toString();
			return "error";
		}
	}
	
	public String getErrormsg() {
		return errormsg;
	}
	
	public Title getTitle() {
		return title;
	}
	
	public User getUser() {
		return user;
	}
}
