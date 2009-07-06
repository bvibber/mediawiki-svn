package de.brightbyte.wikiword.analyzer;

import java.io.IOException;
import java.net.URL;
import java.util.Properties;


public class AuxilliaryWikiProperties {
	
	public static Properties loadProperties(String prefix, String lang) throws IOException {
		URL u= getPropertyFileURL(prefix, lang);
		if (u==null) return null;
		
		Properties p = new Properties();
		p.load(u.openStream());
		return p;
	}

	public static URL getPropertyFileURL(String prefix, String lang) {
		lang = lang.toLowerCase().replace('-', '_');
		
		ClassLoader cl = AuxilliaryWikiProperties.class.getClassLoader();
		String basePackage = "de.brightbyte.wikiword.wikis";
		
		while (true) {
			String n = prefix+"_"+lang+"wiki.properties";
			URL u = cl.getResource(basePackage+"."+n);
			if (u!=null) return u;
			
			if (lang.matches(".*_[^_]*$")) {
				lang = lang.replaceAll("_[^_]*$", "");
			} else {
				break;
			}
		}
		
		if (!lang.equals("en")) return getPropertyFileURL("en", lang);
		
		return null;
	}
}
