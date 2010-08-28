package net.psammead.mwapi.connection;

import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.HashMap;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import net.psammead.mwapi.Location;
import net.psammead.mwapi.config.Site;
import net.psammead.mwapi.ui.UnsupportedFeatureException;
import net.psammead.mwapi.ui.UnsupportedURLException;

public final class URLManager {
	private Site site;

	public URLManager(Site site) {
		this.site	= site;
	}
	
	/** returns the (read-) URL for a Location */
	public URL locationToReadURL(Location location) throws UnsupportedURLException {
		// invariant: location.wiki == site.getWiki() (?)
		final Map<String,String>	args	= new HashMap<String,String>();
		final String				url		= readURL(location.title, args) + queryArgs(args);
		try {
			return new URL(url);
		}
		catch (MalformedURLException e) {
			throw new UnsupportedURLException("malformed URL", e);
		}
	}
	
	/** returns the Location for an URL if it belongs to the Site or null if not */
	public Location anyURLToLocation(URL url) throws UnsupportedURLException {
		final Location	rawLoc		= site.rawPath == null ? null
									: urlToLocation(url, site.rawPath, "\\?title=([^&]*).*");
		final Location	prettyLoc	= site.prettyPath == null ? null
									: urlToLocation(url, site.prettyPath, "([^?]*).*");
		if (rawLoc != null && prettyLoc != null) {
			// when both exist, return the one with the longer path
			return site.prettyPath.length() > site.rawPath.length() 
					? prettyLoc
					: rawLoc;
		}
		if (rawLoc != null)		return rawLoc;
		if (prettyLoc != null)	return prettyLoc;
		return null;
	}
	
	/** 
	 * tries to construct a raw URL usable for actions 
	 * and modifies the args Map if necessary. 
	 * falls bay to a pretty URL
	 */
	public String actionURL(String title, Map<String,String> args) throws UnsupportedURLException {
		if (site.rawPath != null) {
			args.put("title",	title);
			return site.protocol + site.hostName + site.rawPath;
		}
		else if (site.prettyPath != null) {
			return site.protocol + site.hostName + site.prettyPath 
				+ TitleUtil.encodeTitle(title, site.charSet);
		}
		else {
			throw new UnsupportedURLException("neither readPath nor actionPath available");
		}
	}
	
	/** 
	 * tries to construct a pretty URL usable for reading 
	 * and modifies the args Map if necessary.
	 * falls back to a raw URL if necessary
	 */
	public String readURL(String title, Map<String,String> args) throws UnsupportedURLException {
		if (site.prettyPath != null) {
			return site.protocol + site.hostName + site.prettyPath 
				+ TitleUtil.encodeTitle(title, site.charSet);
		}
		else if (site.rawPath != null) {
			args.put("title",	title);
			return site.protocol + site.hostName + site.rawPath;
		}
		else {
			throw new UnsupportedURLException("neither readPath nor actionPath available");
		}
	}
	
	/** constructs an URL for api.php */
	public String apiURL() throws UnsupportedFeatureException {
		if (site.apiPath == null)	throw new UnsupportedFeatureException("api.php is not supported on site: " + site);
		return site.protocol + site.hostName + site.apiPath; 
	}
	
	/** HACK: get an URL to read an article with the GET method */
	private String queryArgs(Map<String,?> args) throws UnsupportedURLException {
		try {
			String	query	= "";
			String	sep		= "?";
			for (String key : args.keySet()) {
				String	value	= ""+args.get(key);
				query	+= sep + URLEncoder.encode(key, site.charSet) 
						+ "=" + URLEncoder.encode(value, site.charSet);
				sep	= "&";
			}
			return query;
		}
		catch (UnsupportedEncodingException e) {
			throw new UnsupportedURLException("encoding problem", e);
		}
	}
	
	private Location urlToLocation(URL url, String path, String extractRE) throws UnsupportedURLException {
		final String	raw			= url.toExternalForm();
		final String	prefix		= site.protocol + site.hostName + path;
		if (!raw.startsWith(prefix))	return null;
		final String	rest		= raw.substring(prefix.length());
		// TODO: error if not matching
		final Pattern	pattern	= Pattern.compile(extractRE);
		final Matcher	matcher	= pattern.matcher(rest);
		if (!matcher.matches())	return null;
		final String	extracted	= matcher.group(1);
		//String	extracted	= rest.replaceAll(extractRE, "$1");
		//if (extracted == null)			return null;
		if (extracted.length() == 0)	return null;
		return site.location(
				TitleUtil.decodeTitle(extracted, site.charSet));
	} 
}
