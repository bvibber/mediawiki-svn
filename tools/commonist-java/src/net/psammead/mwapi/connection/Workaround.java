package net.psammead.mwapi.connection;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.security.Permission;

public final class Workaround {
	private Workaround() {}
	
	/**
	 * workaround for http://bugs.sun.com/bugdatabase/view_bug.do?bug_id=6746185
	 * JDK_1.5.0_16 broke the URL-construction under webstart and is the default for all OS X users
	 * copied from http://forums.sun.com/thread.jspa?threadID=5347404
	 */
	public static URL fixJarURL(URL url) {
	    // final String method = _module + ".fixJarURL";
	    String originalURLProtocol = url.getProtocol();
	    // if (log.isDebugEnabled()) { log.debug(method + " examining '" + originalURLProtocol + "' protocol url: " + url); }
	    if ("jar".equalsIgnoreCase(originalURLProtocol) == false)
	    {
	        // if (log.isDebugEnabled()) { log.debug(method + " skipping fix: URL is not 'jar' protocol: " + url); }
	        return url;
	    }
	 
	    // if (log.isDebugEnabled()) { log.debug(method + " URL is jar protocol, continuing"); }
	    String originalURLString = url.toString();
	    // if (log.isDebugEnabled()) { log.debug(method + " using originalURLString: " + originalURLString); }
	    int bangSlashIndex = originalURLString.indexOf("!/");
	    if (bangSlashIndex > -1)
	    {
	        // if (log.isDebugEnabled()) { log.debug(method + " skipping fix: originalURLString already has bang-slash: " + originalURLString); }
	        return url;
	    }
	 
	    // if (log.isDebugEnabled()) { log.debug(method + " originalURLString needs fixing (it has no bang-slash)"); }
	    String originalURLPath = url.getPath();
	    // if (log.isDebugEnabled()) { log.debug(method + " using originalURLPath: " + originalURLPath); }
	 
	    URLConnection urlConnection;
	    try
	    {
	        urlConnection = url.openConnection();
	        if (urlConnection == null)
	        {
	            throw new IOException("urlConnection is null");
	        }
	    }
	    catch (IOException e)
	    {
	        // if (log.isDebugEnabled()) { log.debug(method + " skipping fix: openConnection() exception", e); }
	        return url;
	    }
	    // if (log.isDebugEnabled()) { log.debug(method + " using urlConnection: " + urlConnection); }
	 
	    Permission urlConnectionPermission;
	    try
	    {
	        urlConnectionPermission = urlConnection.getPermission();
	        if (urlConnectionPermission == null)
	        {
	            throw new IOException("urlConnectionPermission is null");
	        }
	    }
	    catch (IOException e)
	    {
	        // if (log.isDebugEnabled()) { log.debug(method + " skipping fix: getPermission() exception", e); }
	        return url;
	    }
	    // if (log.isDebugEnabled()) { log.debug(method + " using urlConnectionPermission: " + urlConnectionPermission); }
	 
	    String urlConnectionPermissionName = urlConnectionPermission.getName();
	    if (urlConnectionPermissionName == null)
	    {
	        // if (log.isDebugEnabled()) { log.debug(method + " skipping fix: urlConnectionPermissionName is null"); }
	        return url;
	    }
	 
	    // if (log.isDebugEnabled()) { log.debug(method + " using urlConnectionPermissionName: " + urlConnectionPermissionName); }
	 
	    File file = new File(urlConnectionPermissionName);
	    if (file.exists() == false)
	    {
	        // if (log.isDebugEnabled()) { log.debug(method + " skipping fix: file does not exist: " + file); }
	        return url;
	    }
	    // if (log.isDebugEnabled()) { log.debug(method + " using file: " + file); }
	 
	    String newURLStr;
	    try
	    {
	        newURLStr = "jar:" + file.toURI().toURL().toExternalForm() + "!/" + originalURLPath;
	    }
	    catch (MalformedURLException e)
	 
	    {
	        // if (log.isDebugEnabled()) { log.debug(method + " skipping fix: exception creating newURLStr", e); }
	        return url;
	    }
	    // if (log.isDebugEnabled()) { log.debug(method + " using newURLStr: " + newURLStr); }
	 
	    try
	    {
	        url = new URL(newURLStr);
	    }
	    catch (MalformedURLException e)
	    {
	        // if (log.isDebugEnabled()) { log.debug(method + " skipping fix: exception creating new URL", e); }
	        return url;
	    }
	 
	    return url;
	}
}
