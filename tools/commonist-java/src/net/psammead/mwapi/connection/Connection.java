package net.psammead.mwapi.connection;

import net.psammead.mwapi.config.Site;
import net.psammead.util.Throttle;

import org.apache.commons.httpclient.HttpClient;

/** manages HttpClient, a Throttle and login state for a single Site */
public final class Connection {
	private static final int THROTTLE_TIME = 1000;
	
	public final HttpClient	client;
	public final Site		site;
	
	public final URLManager	urlManager;
	public final Throttle	throttle;
	
	private String	userName;
	private boolean	loggedIn;
	
	public Connection(HttpClient client, Site site) {
		this.client		= client;
		this.site		= site;
		
		urlManager	= new URLManager(site);
		throttle	= new Throttle(THROTTLE_TIME);

		userName	= null;
		loggedIn	= false;
	}
	
	//------------------------------------------------------------------------------
	//## login state
	
	/** returns true while a user is logged in */
	public boolean isLoggedIn() {
		return loggedIn;
	}

	/** returns the last logged in user */
	public String getUserName() {
		return userName;
	}

	public void setLoggedIn(boolean loggedIn) {
		this.loggedIn = loggedIn;
	}

	public void setUserName(String userName) {
		this.userName = userName;
	}
	
	//------------------------------------------------------------------------------
	//## action callbacks
	
	/** slow down edits */
	public void throttle() throws InterruptedException {
		throttle.gate();
	}
}
