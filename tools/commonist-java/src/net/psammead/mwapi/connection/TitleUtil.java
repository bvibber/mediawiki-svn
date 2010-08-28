package net.psammead.mwapi.connection;

import java.io.UnsupportedEncodingException;
import java.net.URLDecoder;
import java.net.URLEncoder;

import net.psammead.mwapi.ui.UnsupportedURLException;

/** functions for MediaWiki title-encoding, a variation on URL-encoding */
public final class TitleUtil {
	/** fully static utility class, shall not be instantiated */
	private TitleUtil() {}
	
	/** adds a language to a family with a colon, or returns the family when the language is null */
	public static String buildWiki(String family, String language) {
		return family + (language != null ? (":" + language) : "");
	}
	
	/** use underscores for spaces */
	public static String underscores(String title) {
		return title.replace(' ', '_');
	}
	
	/** use spaces for underscores */
	public static String spaces(String title) {
		 return title.replace('_', ' ');
	}
	
	/** url-encoding for titles, MediaWiki readurl style*/ 
	public static String encodeTitle(String title, String charSet) throws UnsupportedURLException {
		// what's with the plus sign?
		// => works with readURL (smushed) but not as title parameter
		try {
			return URLEncoder.encode(title, charSet)
				.replaceAll("%%",		"\u0000")
				.replaceAll("%3a",		":")
				.replaceAll("%3A",		":")
				.replaceAll("%2f",		"/")
				.replaceAll("%2F",		"/")
				.replaceAll("\u0000",	"%%");
		}
		catch (UnsupportedEncodingException e) {
			throw new UnsupportedURLException("encoding problem", e);
		}
	}
	
	/** url-decoding for titles, MediaWiki readurl style */
	public static String decodeTitle(String title, String charSet) throws UnsupportedURLException {
		try {
			// since 05jan06 MediaWiki does no longer decode "+"
			return URLDecoder.decode(
					title.replaceAll("\\+", "%2b"),
					charSet);
		}
		catch (UnsupportedEncodingException e) {
			throw new UnsupportedURLException("encoding problem", e);
		}
	}
}
