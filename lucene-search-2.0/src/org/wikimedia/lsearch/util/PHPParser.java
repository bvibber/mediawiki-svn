package org.wikimedia.lsearch.util;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;

/** 
 * Extract some variable from MediaWiki MessageXX.php files. In particular,
 * the localized namespace names (needed for proper parsing of wiki code).
 *  
 * @author rainman
 *
 */
public class PHPParser {
	
	protected HashMap<String,Integer> namespaces = new HashMap<String,Integer>();
	
	
	public PHPParser() {
		namespaces.put("NS_MAIN", 0);
		namespaces.put("NS_TALK", 1);
		namespaces.put("NS_USER", 2);
		namespaces.put("NS_USER_TALK", 3);
		namespaces.put("NS_PROJECT", 4);
		namespaces.put("NS_PROJECT_TALK", 5);
		namespaces.put("NS_IMAGE", 6);
		namespaces.put("NS_IMAGE_TALK", 7);
		namespaces.put("NS_MEDIAWIKI", 8);
		namespaces.put("NS_MEDIAWIKI_TALK", 9);
		namespaces.put("NS_TEMPLATE", 10);
		namespaces.put("NS_TEMPLATE_TALK", 11);
		namespaces.put("NS_HELP", 12);
		namespaces.put("NS_HELP_TALK", 13);
		namespaces.put("NS_CATEGORY", 14);
		namespaces.put("NS_CATEGORY_TALK", 15);
	}
	
	public String getFallBack(String text){
		text = text.replaceAll("(#.*)",""); // strip comments
		int flags = Pattern.CASE_INSENSITIVE | Pattern.DOTALL;
		Pattern fallback = Pattern.compile("\\$fallback\\s*=\\s*[\"'](.*?)[\"']",flags);
		Matcher matcher = fallback.matcher(text);
		String ret = null;
		while(matcher.find()){
			String name = matcher.group(1).trim().toLowerCase();
			if(name.length()!=0)
				ret = name;
		}		
		return ret;
		
	}
	
	/** Get a map of localized namespace names: name -> namespace_num */
	public Hashtable<String,Integer> getNamespaces(String text){
		Hashtable<String,Integer> ns = new Hashtable<String,Integer>();
		
		text = text.replaceAll("(#.*)",""); // strip comments
		
		// namespaceNames
		int flags = Pattern.CASE_INSENSITIVE | Pattern.DOTALL;
		Pattern nsnames = Pattern.compile("\\$namespaceNames\\s*=\\s*array\\s*\\((.*?)\\)",flags);
		Pattern entry = Pattern.compile("(\\w+)\\s*=>\\s*[\"'](.*?)[\"']",flags);
		Matcher matcher = nsnames.matcher(text);
		while(matcher.find()){
			Matcher me = entry.matcher(matcher.group(1));
			while(me.find()){
				Integer i = namespaces.get(me.group(1).toUpperCase());
				if(i!=null){
					String name = me.group(2).trim().toLowerCase();
					if(name.length() != 0)
						ns.put(name,i);
				}
			}
		}
		// aliases
		Pattern nsaliases = Pattern.compile("\\$namespaceAliases\\s*=\\s*array\\s*\\((.*?)\\)",flags);
		Pattern alias = Pattern.compile("[\"'](.*?)[\"']\\s*=>\\s*(\\w+)",flags);
		matcher = nsaliases.matcher(text);
		while(matcher.find()){
			Matcher me = alias.matcher(matcher.group(1));
			while(me.find()){
				Integer i = namespaces.get(me.group(2).toUpperCase());
				if(i!=null){
					String name = me.group(1).trim().toLowerCase();
					if(name.length() != 0)
						ns.put(name,i);
				}
			}
		}
		
		return ns;
	}
	
	public String readFile(String path){
		char buffer[] = new char[32768];
		String text = "";
		try {
			FileReader r = new FileReader(path);
			int len = 0;
			do{ 	
				text += new String(buffer,0,len);
				len = r.read(buffer);							
			} while(len > 0);
			r.close();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} 
		return text;		
	}
	
	public String readURL(URL url){
		char buffer[] = new char[32768];
		String text = "";
		try {
			BufferedReader r = new BufferedReader(
					new InputStreamReader(
							url.openStream()));
			int len = 0;
			do{ 	
				text += new String(buffer,0,len);
				len = r.read(buffer);							
			} while(len > 0);
			r.close();
		} catch (IOException e) {
			// silent
		}
		return text;		
	}
	
	/** Test stuff */
	public static void main(String args[]){
		String text = "$namespaceNames = array(\n"+
		"NS_MEDIA            => \"Medija\",\n"+
		"NS_SPECIAL          => \"Posebno\",\n"+
		"# NS_PROJECT set by $wgMetaNamespace\n"+
		"NS_CATEGORY_TALK    => 'Razgovor_o_kategoriji');";
		String text2 = "$fallback='sr-ec';";
	
		String file = "/var/www/html/wiki-lucene/phase3/languages/messages/MessagesEn.php";
		
		PHPParser p = new PHPParser();
		Hashtable<String,Integer> map = p.getNamespaces(p.readFile(file));
		System.out.println(map);
		System.out.println(p.getFallBack(text2));
	}
}
