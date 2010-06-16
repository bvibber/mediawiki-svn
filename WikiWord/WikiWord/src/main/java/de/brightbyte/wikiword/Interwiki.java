package de.brightbyte.wikiword;

import java.io.IOException;
import java.io.InputStream;
import java.util.Collections;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;

/**
 * Static helper class for dealing with MediaWiki interwiki links. 
 */
public class Interwiki {
	
    private final String prefix;
    private final String urlTemplate;
    private final String languageName;

	public Interwiki(String prefix, String urlTemplate, String languageName) {
		if (prefix==null) throw new NullPointerException();
		if (urlTemplate==null) throw new NullPointerException();
		
		this.prefix = prefix;
		this.urlTemplate = urlTemplate;
		this.languageName = languageName;
	}

	public String getLanguageName() {
		return languageName;
	}

	public String getPrefix() {
		return prefix;
	}

	public String getUrlTemplate() {
		return urlTemplate;
	}

	/**
	 * Returns a Map representing the interwiki prefixes defined for the given Corpus
	 * (i.e. wiki project).  
	 */
	public static Map<String, Interwiki> getInterwikiMap(Corpus corpus) {
		String n;
		
		//TODO: merge files for language and specific wiki!
		if (corpus!=null) n = "wikis/InterwikiMap_"+corpus.getClassSuffix()+".properties";
		else n = "InterwikiMap.properties";
		
		InputStream in = Interwiki.class.getResourceAsStream(n);
		if (in == null) return Collections.emptyMap();
		
		Map<String, Interwiki> interwikis = null;
		try {
			Properties p = new Properties();
			p.load(in);
			in.close();
			
			interwikis = new HashMap<String, Interwiki>();
			
			Enumeration en = p.propertyNames();
			while (en.hasMoreElements()) {
				String k = en.nextElement().toString().trim();
				String u = p.getProperty(k).toString().trim();
				String lang = null;
				
				int idx = u.indexOf(' ');
				if (idx>0) {
					lang = u.substring(idx).trim();
					u = u.substring(0, idx).trim();
				}

				Interwiki iw = new Interwiki(k, u, lang);
				interwikis.put(k, iw);
			}
			
			return interwikis;
		} catch (IOException e) {
			throw new RuntimeException("failed to load interwiki map from "+n, e);
		} catch (IllegalArgumentException e) { //NOTE: malformed \\u-encoding triggers this. wtf? why not a *real* exception?... 
			throw new RuntimeException("failed to load interwiki map from "+n, e);
		}
		
	}

	@Override
	public String toString() {
		return prefix+":"+urlTemplate;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((languageName == null) ? 0 : languageName.hashCode());
		result = PRIME * result + ((prefix == null) ? 0 : prefix.hashCode());
		result = PRIME * result + ((urlTemplate == null) ? 0 : urlTemplate.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final Interwiki other = (Interwiki) obj;
		if (languageName == null) {
			if (other.languageName != null)
				return false;
		} else if (!languageName.equals(other.languageName))
			return false;
		if (prefix == null) {
			if (other.prefix != null)
				return false;
		} else if (!prefix.equals(other.prefix))
			return false;
		if (urlTemplate == null) {
			if (other.urlTemplate != null)
				return false;
		} else if (!urlTemplate.equals(other.urlTemplate))
			return false;
		return true;
	}
	

}
