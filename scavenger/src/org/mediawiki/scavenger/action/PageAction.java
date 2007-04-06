package org.mediawiki.scavenger.action;

import java.sql.SQLException;
import java.util.Map;

import javax.servlet.ServletContext;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.struts2.interceptor.ParameterAware;
import org.apache.struts2.interceptor.ServletRequestAware;
import org.apache.struts2.interceptor.ServletResponseAware;
import org.apache.struts2.util.ServletContextAware;
import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.User;
import org.mediawiki.scavenger.Wiki;

import com.opensymphony.xwork2.ActionSupport;

public abstract class PageAction 
	extends ActionSupport 
	implements ParameterAware, ServletResponseAware, ServletRequestAware, ServletContextAware {
	
	HttpServletResponse resp;
	HttpServletRequest req;
	ServletContext ctx;
	
	public void setServletContext(ServletContext ctx) {
		this.ctx = ctx;
	}
	
	public void setServletResponse(HttpServletResponse resp) {
		this.resp = resp;
	}

	public void setServletRequest(HttpServletRequest req) {
		this.req = req;
	}

	protected Wiki wiki;
	protected Map parameters;
	protected Title title;
	protected String errormsg;
	protected User user;
	
	abstract String pageExecute() throws Exception;
	
	public void setParameters(Map params) {
		parameters = params;
	}
	
	public final String execute() {
		try {
			wiki = Wiki.getWiki(ctx, req);
		} catch (Exception e) {
			errormsg = "Error retrieving database connection: " + e.toString();
			return ERROR;
		}
		title = null;
			
		String[] title_ = (String[]) parameters.get("title");
		if (title_ != null)
			title = wiki.getTitle(title_[0]);
		
		try {
			user = wiki.getUser(req.getRemoteAddr(), true);
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
