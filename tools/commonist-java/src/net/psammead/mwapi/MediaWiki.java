package net.psammead.mwapi;

import java.io.File;
import java.io.PrintStream;
import java.io.PrintWriter;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Properties;

import net.psammead.mwapi.api.API;
import net.psammead.mwapi.api.data.list.AllLinks;
import net.psammead.mwapi.api.data.list.AllPages;
import net.psammead.mwapi.api.data.list.AllUsers;
import net.psammead.mwapi.api.data.list.BackLinks;
import net.psammead.mwapi.api.data.list.CategoryMembers;
import net.psammead.mwapi.api.data.list.EmbeddedIn;
import net.psammead.mwapi.api.data.list.ExtUrlUsage;
import net.psammead.mwapi.api.data.list.ImageUsage;
import net.psammead.mwapi.api.data.list.LogEvents;
import net.psammead.mwapi.api.data.list.RecentChanges;
import net.psammead.mwapi.api.data.list.UserContribs;
import net.psammead.mwapi.api.data.list.WatchList;
import net.psammead.mwapi.api.data.prop.Categories;
import net.psammead.mwapi.api.data.prop.ExtLinks;
import net.psammead.mwapi.api.data.prop.ImageInfo;
import net.psammead.mwapi.api.data.prop.Images;
import net.psammead.mwapi.api.data.prop.Info;
import net.psammead.mwapi.api.data.prop.LangLinks;
import net.psammead.mwapi.api.data.prop.Links;
import net.psammead.mwapi.api.data.prop.Revisions;
import net.psammead.mwapi.api.data.prop.Templates;
import net.psammead.mwapi.config.Site;
import net.psammead.mwapi.connection.ConfigException;
import net.psammead.mwapi.connection.ConfigManager;
import net.psammead.mwapi.connection.Connection;
import net.psammead.mwapi.connection.LocationManager;
import net.psammead.mwapi.connection.URLManager;
import net.psammead.mwapi.net.NonProxyConnectionManager;
import net.psammead.mwapi.ui.MoveCallback;
import net.psammead.mwapi.ui.Page;
import net.psammead.mwapi.ui.ProgressCallback;
import net.psammead.mwapi.ui.UnsupportedFeatureException;
import net.psammead.mwapi.ui.UnsupportedURLException;
import net.psammead.mwapi.ui.UnsupportedWikiException;
import net.psammead.mwapi.ui.UploadCallback;
import net.psammead.mwapi.ui.Uploaded;
import net.psammead.mwapi.ui.action.FileURLAction;
import net.psammead.mwapi.ui.action.FileUploadAction;
import net.psammead.mwapi.ui.action.PageDeleteAction;
import net.psammead.mwapi.ui.action.PageLoadAction;
import net.psammead.mwapi.ui.action.PageMoveAction;
import net.psammead.mwapi.ui.action.PageProtectAction;
import net.psammead.mwapi.ui.action.PageStoreAction;
import net.psammead.mwapi.ui.action.PageWatchAction;
import net.psammead.mwapi.ui.action.UserBlockAction;
import net.psammead.mwapi.ui.action.UserLoginAction;
import net.psammead.mwapi.ui.action.UserLogoutAction;
import net.psammead.util.Disposable;
import net.psammead.util.Logger;

import org.apache.commons.httpclient.Credentials;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.HttpState;
import org.apache.commons.httpclient.UsernamePasswordCredentials;
import org.apache.commons.httpclient.auth.AuthScope;
import org.apache.commons.httpclient.cookie.CookiePolicy;
import org.apache.commons.httpclient.params.HttpConnectionManagerParams;

/** this is the main entry point, a facade to all configured MediaWiki sites */
public final class MediaWiki implements Disposable {
	public static final String	DEFAULT_USER_AGENT	= "mwapi/0.0";
	public static final String	COOKIE_POLICY		= CookiePolicy.RFC_2109;	// BROWSER_COMPATIBILITY
	
	private Logger	logger;
	
	private final ConfigManager		configManager;
	private	final LocationManager	locationManager;
	
	private	final NonProxyConnectionManager	manager;
	private final HttpClient				client;
	
	private String	userAgent;
	
	private final Map<String,Connection>	connections;
	
	public MediaWiki() throws ConfigException {
		userAgent	= DEFAULT_USER_AGENT;
		
		logger	= new Logger(MediaWiki.class);
		
		configManager	= new ConfigManager();
		locationManager	= new LocationManager(configManager);
		
		connections	= new HashMap<String,Connection>();
		
		manager	= new NonProxyConnectionManager();
		
		HttpConnectionManagerParams	managerParams	= manager.getParams();
		managerParams.setDefaultMaxConnectionsPerHost(6);
		managerParams.setMaxTotalConnections(18);
		managerParams.setStaleCheckingEnabled(true);
		
		client	= new HttpClient(manager);
		// deprecated! client.State.CookiePolicy	= CookiePolicy.COMPATIBILITY;
		//client.HostConfiguration.setHost(LOGON_SITE, LOGON_PORT, "http");
	}
	
	/** destructor freeing all resources. the Connection is not usable any more after calling this method */
	public void dispose() {
		manager.shutdown();
	}
	
	//------------------------------------------------------------------------------
	//## site management
	
	/** return an immutable List of supported wiki names (Strings) */
	public List<String> supportedWikis() {
		return configManager.getSupportedWikiNames();
	}
	
	/** (re)loads a Family and returns its name */
	public String loadFamily(URL familyDescriptor) throws ConfigException {
		// without clearing Connections are left with the wrong Site object
		connections.clear();	
		return configManager.loadFamily(familyDescriptor).name;
	}

	//------------------------------------------------------------------------------
	//## public API
	
	/** creates a Location for a link within a wiki */
	public Location location(String wiki, String link) throws UnsupportedWikiException {
		return relativeLocation(new Location(wiki, "."), link);
	}
	
	/** creates a Location for an absolute link or null when invalid */
	public Location absoluteLocation(String link) throws UnsupportedWikiException {
		return locationManager.absoluteLocation(link);
	}

	/** creates a Location for a link relative to a base Location */
	public Location relativeLocation(Location baseLocation, String link) throws UnsupportedWikiException {
		return locationManager.relativeLocation(baseLocation, link);
	}
	
	/** 
	 * returns the regular (non-discussion) page for an article 
	 * or null when the location is not a discussion page 
	 */
	public Location regularPageFor(Location location) throws UnsupportedWikiException {
		return locationManager.regularPageFor(location);
	}
	
	/** 
	 * returns the discussion page for an article 
	 * or null when it already is a discussion page 
	 * or no discussion page exists (Media and Special namespace)
	 */
	public Location discussionPageFor(Location location) throws UnsupportedWikiException {
		return locationManager.discussionPageFor(location);
	}
	
	/**
	 * returns the discussion page for an article or the article for a diskussion page
	 * or null for Special and Media pages where no counterpart exists
	 */
	public Location toggleDiscussion(Location location) throws UnsupportedWikiException {
		return locationManager.toggleDiscussion(location);
	}
	
	/** get a home directory "User:<name>/" or null when not logged in */
	public Location homeLocation(String wiki) throws UnsupportedWikiException {
		Site		site		= configManager.getSite(wiki);
		Connection	connection	= connection(wiki);
		if (!connection.isLoggedIn())	return null;
		NameSpace	userNS		= site.nameSpace(NameSpace.USER);
		String		userName	= connection.getUserName();
		String		title		= userNS.addTo(userName);
		return new Location(wiki, title);
	}
	
	/** returns the location for an URL */
	public Location urlToLocation(URL url) throws UnsupportedWikiException, UnsupportedURLException {
		List<String>	supported	= configManager.getSupportedWikiNames();
		for (String wiki : supported) {
			URLManager	urlManager	= connection(wiki).urlManager;
			Location	location	= urlManager.anyURLToLocation(url);
			if (location != null)	return location;
		}
		throw new UnsupportedURLException("url " + url + " does not belong to a known wiki");
	}

	/** returns the URL for a location */
	public URL locationToURL(Location location) throws UnsupportedURLException, UnsupportedWikiException {
		URLManager	urlManager	= connection(location.wiki).urlManager;	
		return urlManager.locationToReadURL(location);
	}

	/** returns a NameSpace object for a NS_ index */
	public NameSpace nameSpace(String wiki, int index) throws UnsupportedWikiException {
		return configManager.getSite(wiki).nameSpace(index);
	} 
	
	/** find out the NameSpace used for a Location */
	public NameSpace nameSpaceFor(Location location) throws UnsupportedWikiException {
		return configManager.getSite(location.wiki).nameSpaceForTitle(location.title);
	} 
	
	/** return true while a user is logged in */
	public String userName(String wiki) throws UnsupportedWikiException {
		Connection	connection	= connection(wiki);
		return connection.getUserName();
	}
	
	/** return true while a user is logged in */
	public boolean loggedIn(String wiki) throws UnsupportedWikiException {
		Connection	connection	= connection(wiki);
		return connection.isLoggedIn();
	}
	
	//------------------------------------------------------------------------------
	//## server access
	
	/** load the current version of a Page */
	public Page load(Location location) throws MediaWikiException {
		return loadOld(location, null);
	}

	/** load an old version of a Page */
	public Page loadOld(Location location, String oldid) throws MediaWikiException {
		Connection		connection	= connection(location.wiki);
		PageLoadAction	action		= new PageLoadAction(this, connection, location.title, oldid);
		action.execute();	return action.getPage();
	}

	/**
	 * store a new version of a page.
	 * returns a conflict Page or null when sucessful
	 */
	public Page store(Page page, String summary, boolean minorEdit) throws MediaWikiException {
		Connection		connection	= connection(page.location.wiki);
		PageStoreAction	action		= new PageStoreAction(this, connection, page, summary, minorEdit);
		action.execute();	return action.getConflict();
	}

	/** 
	 * upload a File and returns an Uploaded object. 
	 * file exor sessionKey may be null. 
	 * progressListener may be null 
	 */
	public Uploaded upload(String wiki, String title, String description, File file,
			boolean watchThis, ProgressCallback progressListener, UploadCallback callback) 
			throws MediaWikiException {
		// TODO: FileNotFoundException! 
		Connection			connection	= connection(wiki);
		FileUploadAction	action		= new FileUploadAction(this, connection, title, description, file, watchThis, progressListener, callback);
		action.execute();	return new Uploaded(action.getUploaded());
	}
	
	/** watches or unwatches a Page */
	public void watched(Location location, boolean watch) throws MediaWikiException {
		Connection		connection	= connection(location.wiki);
		PageWatchAction	action		= new PageWatchAction(this, connection, location.title, watch);
		action.execute();
	}

	/** deletes an article */
	public void delete(Location location, String reason) throws MediaWikiException {
		Connection			connection	= connection(location.wiki);
		PageDeleteAction	action		= new PageDeleteAction(this, connection, location.title, reason);
		action.execute();
	}
	
	/** 
	 * change a page's protection state 
	 * @param levelEdit		may be "", "autoconfirmed" and "sysop" 
	 * @param levelMove		may be "", "autoconfirmed" and "sysop" 
	 * @param cascade		if transcluded pages should be protected, too
	 * @param expiry		may be empty for indefinite, "indefinite", 
	 * 						or a number followed by a space and 
	 * 						"years", "months", "days", "hours" or "minutes"
	 */
	public void protect(Location location, String levelEdit, String levelMove, boolean cascade, String expiry, String reason) throws MediaWikiException {
		Connection			connection	= connection(location.wiki);
		PageProtectAction	action		= new PageProtectAction(this, connection, location.title, levelEdit, levelMove, cascade, expiry, reason);
		action.execute();
	}
	
	/**
	 * blocks a user
	 * @param expiry			may be empty for indefinite, "indefinite", 
	 * 							or a number followed by a space and 
	 * 							"years", "months", "days", "hours" or "minutes"
	 * @param anonOnly			defaults to false
	 * @param createAccounts	defaults to true 
	 * @param enableAutoblock	defaults to true 
	 */
	public void block(String wiki, String user, String duration, String reason, boolean anonOnly, boolean createAccount, boolean enableAutoblock, boolean emailBan) throws MediaWikiException {
		Connection		connection	= connection(wiki);
		UserBlockAction	action		= new UserBlockAction(this, connection, user, duration, reason, anonOnly, createAccount, enableAutoblock, emailBan);
		action.execute();
	}
	
	/** 
	 * moves an article
	 * the callback is used to query overwriting existing articles and may be null
	 */
	public void move(String wiki, String oldTitle, String newTitle, String reason, MoveCallback moveCallback) throws MediaWikiException {
		Connection		connection	= connection(wiki);
		PageMoveAction	action		= new PageMoveAction(this, connection, oldTitle, newTitle, reason, moveCallback);
		action.execute();
	}
		
	/** gets the URL of a full resolution image */
	public URL fileURL(String wiki, String name) throws MediaWikiException {
		Connection		connection	= connection(wiki);
		FileURLAction	action		= new FileURLAction(this, connection, name);
		action.execute();	return action.getURL();
	}
		
	/** log in */
	public boolean login(String wiki, String user, String passwd, boolean remember) throws MediaWikiException {
		Connection		connection	= connection(wiki);
		UserLoginAction	action		= new UserLoginAction(this, connection, user, passwd, remember);
		action.execute();	return action.isSuccess();
	}

	/** log out */
	public boolean logout(String wiki) throws MediaWikiException {
		Connection			connection	= connection(wiki);
		UserLogoutAction	action		= new UserLogoutAction(this, connection);
		action.execute();	return action.isSuccess();
	}
	
	/** log out on from all Sites we are logged in */
	public void logoutAll() throws MediaWikiException {
		for (Connection connection : connections.values()) {
			if (!connection.isLoggedIn())	continue;
			UserLogoutAction	action		= new UserLogoutAction(this, connection);
			action.execute();
		}
	}

	//------------------------------------------------------------------------------
	//## api.php 
	
	public boolean apiSupported(String wiki) throws UnsupportedWikiException {
		return connection(wiki).site.apiPath != null;
	}
	
	private API api(String wiki) throws UnsupportedFeatureException, UnsupportedWikiException {
		Connection connection = connection(wiki);
		return new API(
			getLogger(),
			connection.client,
			connection.throttle,
			connection.urlManager.apiURL(),
			getUserAgent(),
			connection.site.wiki,
			connection.site.charSet
		);
	}
	
	// TODO ensure NameSpaces are compatible with wiki
	
	//## prop
	
	public Categories categories(Location location) throws MediaWikiException {
		return api(location.wiki)
				.categories(location.title);
	}
	
	public ExtLinks extLinks(Location location) throws MediaWikiException {
		return api(location.wiki)
				.extLinks(location.title);
	}
	
	public ImageInfo imageInfo(Location location) throws MediaWikiException {
		return api(location.wiki)
				.imageInfo(location.title);
	}
	
	public Images images(Location location) throws MediaWikiException {
		return api(location.wiki)
				.images(location.title);
	}
	
	public Info info(Location location) throws MediaWikiException {
		return api(location.wiki)
				.info(location.title);
	}
	
	public LangLinks langLinks(Location location) throws MediaWikiException {
		return api(location.wiki)
				.langLinks(location.title);
	}
	
	public Links links(Location location, List<NameSpace> nameSpaces) throws MediaWikiException {
		return api(location.wiki)
				.links(location.title, nameSpaces);
	}
	
	public Revisions revisions(Location location, Long startId, Long endId, Date start, Date end, boolean newer, String user, String excludeUser, int limit) throws MediaWikiException {
		return api(location.wiki)
				.revisions(location.title, startId, endId, start, end, newer, user, excludeUser, limit);
	}
	
	public Templates templates(Location location, List<NameSpace> nameSpaces) throws MediaWikiException {
		return api(location.wiki)
				.templates(location.title, nameSpaces);
	}

	//## list
	
	// continueKey is from
	public AllPages allPages(String wiki, String from, String prefix, List<NameSpace> nameSpaces, String filterRedir, int limit) throws MediaWikiException {
		return api(wiki)
				.allPages(from, prefix, nameSpaces, filterRedir, limit);
	}
	
	// continueKey is from
	public AllLinks allLinks(String wiki, String from, String prefix, List<NameSpace> nameSpaces, int limit) throws MediaWikiException {
		return api(wiki)
				.allLinks(from, prefix, nameSpaces, limit);
	}
	
	// continueKey is from
	public AllUsers allUsers(String wiki, String from, String prefix, List<NameSpace> nameSpaces, String group, int limit) throws MediaWikiException {
		return api(wiki)
				.allUsers(from, prefix, nameSpaces, group, limit);
	}
	
	// continueKey is start
	public UserContribs userContribs(String wiki, String user,  List<NameSpace> nameSpaces, Date start, Date end, boolean newer, int limit) throws MediaWikiException {
		return api(wiki)
				.userContribs(user, nameSpaces, start, end, newer, limit);
	}
	
	public CategoryMembers categoryMembers(String wiki, String category, List<NameSpace> nameSpaces, int limit, String continueKey) throws MediaWikiException {
		return api(wiki)
				.categoryMembers(category, nameSpaces, limit, continueKey);
	}
	
	public BackLinks backLinks(Location location, List<NameSpace> nameSpaces, boolean redirect, int limit, String continueKey) throws MediaWikiException {
		return api(location.wiki)
				.backLinks(location.title, nameSpaces, redirect, limit, continueKey);
	}
	
	public EmbeddedIn embeddedIn(Location location, List<NameSpace> nameSpaces, boolean redirect, int limit, String continueKey) throws MediaWikiException {
		return api(location.wiki)
				.embeddedIn(location.title, nameSpaces, redirect, limit, continueKey);
	}
	
	public ImageUsage imageUsage(Location location, List<NameSpace> nameSpaces, boolean redirect, int limit, String continueKey) throws MediaWikiException {
		return api(location.wiki)
				.imageUsage(location.title, nameSpaces, redirect, limit, continueKey);
	}
	
	public ExtUrlUsage extUrlUsage(String wiki, String protocol, String query, List<NameSpace> nameSpaces, int limit, String continueKey) throws MediaWikiException {
		return api(wiki)
				.extUrlUsage(protocol, query, nameSpaces, limit, continueKey);
	}
	
	// continueKey is start
	public LogEvents logEvents(String wiki, Date start, Date end, boolean newer, String user, String title, int limit) throws MediaWikiException {
		return api(wiki)
				.logEvents(start, end, newer, user, title, limit);
	}
	
	// continueKey is start
	public RecentChanges recentChanges(String wiki, List<NameSpace> nameSpaces, Date start, Date end, boolean newer, int limit) throws MediaWikiException {
		return api(wiki)
				.recentChanges(nameSpaces, start, end, newer, limit);
	}
	
	// continueKey is start
	public WatchList watchList(String wiki, List<NameSpace> nameSpaces, Date start, Date end, boolean newer, int limit) throws MediaWikiException {
		return api(wiki)
				.watchList(nameSpaces, start, end, newer, limit);
	}
	
	//-------------------------------------------------------------------------
	//## public configuration
	
	/** gets the user-agent for HTTP-requests */
	public String getUserAgent()	{ 
		return userAgent;			
	}
	
	/** sets the user-agent for HTTP-requests */
	public void setUserAgent(String userAgent) {
		this.userAgent	= userAgent;
	}
	
	/** setup a proxy automatically */
	public void setupProxy() {
		// from system properties
		Properties	sysProps	= System.getProperties();
		String	proxyHost		= sysProps.getProperty("http.proxyHost");
		String	proxyPort		= sysProps.getProperty("http.proxyPort");
		String	nonProxyHosts	= sysProps.getProperty("http.nonProxyHosts");
		if (proxyHost != null && proxyPort != null)	{
			int	portNum;
			try {
				portNum = Integer.parseInt(proxyPort);
			}
			catch (NumberFormatException e) {
				logger.error("system property http.proxyPort is not a number: " + proxyPort, e);
				return;
			}	
			logger.info("using system proxy: " + proxyHost + ":" + proxyPort);
			configureProxy(
					proxyHost, 
					portNum,
					null, null,						// user and password
					nonProxyHosts);
			return;
		}
		else if (proxyHost != null || proxyPort != null) {
			logger.info("proxy not set, both http.proxyHost and http.proxyPort have to be set");
		}
		
		// from environment variable http_proxy
		String	proxyStr	= null;
		try { proxyStr = System.getenv("http_proxy"); }
		catch (Throwable t) { logger.info("cannot get environment variable: http_proxy", t); }
		if (proxyStr != null) {
			URL	url;
			try {
				url	= new URL(proxyStr);
			}
			catch (MalformedURLException e) {
				logger.error("environment variable http_proxy is not an URL: " + proxyStr, e);
				return;
			}
			logger.info("using environment proxy: " + proxyStr);
			configureProxy(
					url.getHost(), 
					url.getPort(),
					null, null,						// user and password
					nonProxyHosts);
			return;
		}
	}

	/** 
	 * configure the proxy to be used.<br>
	 * host may be null to disable proxy usage.<br>
	 * user may be null to disable proxy authentication<br>
	 * nonProxyHosts may be null to signify all hosts should go thru the proxy
	 */
	public void configureProxy(String host, int port, String user, String password, String nonProxyHosts) {
		if (host != null) {
			logger.info("using proxy " + host + ":" + port);
			client.getHostConfiguration().setProxy(host, port);
			
			HttpState	httpState	= client.getState();
			AuthScope	authScope	= new AuthScope(
					AuthScope.ANY_HOST,
					AuthScope.ANY_PORT,
					AuthScope.ANY_REALM,
					AuthScope.ANY_SCHEME);

			// set credentials if wanted
			Credentials credentials;
			if (user != null) {
				logger.info("using proxy user " + user);
				credentials	= new UsernamePasswordCredentials(user, password);
			}
			else {
				logger.info("not using proxy authentication");
				credentials	= null;
			}
			httpState.setProxyCredentials(authScope, credentials);
		}
		else {
			logger.info("proxy usage disabled");
			client.getHostConfiguration().setProxyHost(null);
		}
		
		logger.info("proxy usage disabled for hosts: " + nonProxyHosts);
		manager.setNonProxyHosts(nonProxyHosts);
	}
	
	/** 
	 * set credentials for the host of a wiki.
	 * user and password may be null to disable 
	 */
	public void httpCredentials(String wiki, String user, String password) throws UnsupportedWikiException {
		// we don't want to be asked
		client.getParams().setAuthenticationPreemptive(true);
		
		Site		site		= configManager.getSite(wiki);
		HttpState	httpState	= client.getState();
		AuthScope	authScope	= new AuthScope(
				site.hostName,
				AuthScope.ANY_PORT,
				AuthScope.ANY_REALM,
				AuthScope.ANY_SCHEME);
		
		// set HTTP credentials
		Credentials	credentials;
		if (user != null && password != null) {
			credentials	= new UsernamePasswordCredentials(user, password);
		}
		else {
			credentials	= null;
		}
		httpState.setCredentials(authScope, credentials);
	}

	//-------------------------------------------------------------------------
	//## logging
	
	/** gets the currently used Logger */
	public Logger getLogger() {
		return logger;
	}
	
	/** sets the currently used Logger */
	public void setLogger(Logger logger) {
		this.logger	= logger;
	}
	
	/** convenience method: use a PrintWriter to create an internal Logger */
	public void setLog(PrintWriter log) {
		this.logger	= new Logger(log, MediaWiki.class);
	}
	
	/** convenience method: use a PrintStream to create an internal Logger */
	public void setLog(PrintStream log) {
		this.logger	= new Logger(log, MediaWiki.class);
	}
	
	//------------------------------------------------------------------------------
	//## ConnectionManager
	
	/** returns the HttpClient internally used */
	public HttpClient getClient() {
		return client;
	}

	/** get a connection from the cache or or create a connection and cache it */
	private Connection connection(String wiki) throws UnsupportedWikiException {
		Connection	connection	= connections.get(wiki);
		if (connection == null)	{
			Site	site	= configManager.getSite(wiki);
			connection	= new Connection(client, site);
			connections.put(wiki, connection);
		}
		return connection;
	}
}
