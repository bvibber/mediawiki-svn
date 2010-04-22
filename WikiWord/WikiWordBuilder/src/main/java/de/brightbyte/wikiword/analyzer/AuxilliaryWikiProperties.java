package de.brightbyte.wikiword.analyzer;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.text.ParseException;
import java.text.ParsePosition;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Properties;

import de.brightbyte.text.DefaultStringLiteralParser;


public class AuxilliaryWikiProperties {
	
	public static Properties loadProperties(String prefix, String lang) throws IOException {
		URL u= getPropertyFileURL(prefix, lang);
		Properties p = new Properties();

		if (u!=null) p.load(u.openStream());
		
		return p;
	}

	public static final DefaultStringLiteralParser lineParser = new DefaultStringLiteralParser("", '\\', 
			DefaultStringLiteralParser.ALLOW_NAMED_ESCAPE | DefaultStringLiteralParser.ALLOW_HEX_ESCAPE | DefaultStringLiteralParser.ALLOW_UNICODE_ESCAPE | 
			DefaultStringLiteralParser.IMPLICIT_INITIAL_QUOTE | DefaultStringLiteralParser.IMPLICIT_FINAL_QUOTE );
	
	public static List<String> loadList(String prefix, String lang) throws IOException {
		URL u= getPropertyFileURL(prefix, lang);
		if (u==null) return Collections.emptyList();
		
		BufferedReader in = new BufferedReader(new InputStreamReader(u.openStream(), "UTF-8"));

		List<String> list = new ArrayList<String>();
		ParsePosition p = new ParsePosition(0);
		String s;
		while ((s = in.readLine()) != null) {
			s = s.trim();
			if (s.length()==0) continue;
			
			try {
				p.setIndex(0);
				s = lineParser.parseStringLiteral(s, p);
				list.add(s);
			} catch (ParseException e) {
				throw new RuntimeException("failed to parse line", e);
			}
		}
		
		return list;
	}

	public static URL getPropertyFileURL(String prefix, String wiki) {
		wiki = wiki.toLowerCase().replace('-', '_');
		
		ClassLoader cl = AuxilliaryWikiProperties.class.getClassLoader();
		String basePackage = "de.brightbyte.wikiword.wikis"; //TODO: extra packages from Tweaks 
		
		while (true) {
			String n = prefix+"_"+wiki+".properties";
			String p = basePackage.replace('.', '/')+"/"+n;
			URL u = cl.getResource(p);
			if (u!=null) return u;
			
			if (wiki.matches(".*_[^_]*$")) {
				wiki = wiki.replaceAll("_[^_]*$", "");
			} else {
				break;
			}
		}
		
		//if (!lang.equals("en")) return getPropertyFileURL("en", lang);
		
		return null;
	}
}
