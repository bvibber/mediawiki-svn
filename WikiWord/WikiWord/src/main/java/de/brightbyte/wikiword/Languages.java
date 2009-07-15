package de.brightbyte.wikiword;

import java.io.IOException;
import java.io.InputStream;
import java.util.Collections;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;

/**
 * The Languages class represents a mapping of language codes to language names. 
 * The codes are the ones used for Wikimedia projects, and are mostly consistent with 
 * ISO 639-1 codes. The language names are given in the respective language.
 * The actual mapping is provided by the unmodofiable static final <tt>name</tt> map.
 * It is loaded from the Languages.properties located in the de.brightbyte.wikiword package.
 */
public class Languages {
	
	public static Map<String, String> load(TweakSet tweaks){
		try {
			InputStream in = Languages.class.getResourceAsStream("Languages.properties");
			if (in == null) throw new ExceptionInInitializerError("missing resource Languages.properties");
			Properties p = new Properties();
			p.load(in);
			
			Map<String, String> ln = new HashMap<String, String>(p.size());
			Enumeration en = p.propertyNames();
			while (en.hasMoreElements()) {
				String k = (String)en.nextElement();
				String v = p.getProperty(k);
				
				ln.put(k, v);
			}
			
			if (tweaks.getTweak("languages.commonsAsLanguage", false)) {
				ln.put("commons", "Commons");
			} else {
				ln.remove("commons");
			}
			
			if (tweaks.getTweak("languages.simpleAsLanguage", true)) {
				ln.put("simple", "Simple English");
			} else {
				ln.remove("simple");
			}
			
			if (tweaks.getTweak("languages.metaAsLanguage", false)) {
				ln.put("meta", "Meta-Wiki");
			} else {
				ln.remove("meta");
			}
			
			return Collections.unmodifiableMap(ln);
		}
		catch (IOException ex) {
			throw new RuntimeException("failed to load Languages.properties via ClassLoader", ex);
		}		
	}
	
    /** defy instantiation **/
	private Languages() {
		throw new UnsupportedOperationException();
	}
}
