package org.wikimedia.lsearch.util;

import java.io.BufferedReader;
import java.io.FileReader;
import java.net.URL;
import java.text.MessageFormat;
import java.util.Collection;
import java.util.Hashtable;
import java.util.HashSet;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;

/** 
 * Provides localization for namespace names, and provides interwiki map. 
 * Localization is read from MediaWiki Message files by some simple php
 * parsing (which might be a problem if the format changes significantly) 
 */
public class Localization {
	static org.apache.log4j.Logger log = Logger.getLogger(Localization.class);
	/** langCode -> name -> ns_id */ 
	protected static Hashtable<String,Hashtable<String,Integer>> namespaces = new Hashtable<String,Hashtable<String,Integer>>();
	protected static Object lock = new Object();
	/** Languages for which loading of localization failed */
	protected static HashSet<String> badLocalizations = new HashSet<String>();
	protected static HashSet<String> interwiki = null;
	
	public static HashSet<String> getLocalizedImage(String langCode){
		return getLocalizedNamespace(langCode,6);
	}
	
	public static HashSet<String> getLocalizedCategory(String langCode){
		return getLocalizedNamespace(langCode,14);
	}
	
	public static HashSet<String> getLocalizedNamespace(String langCode, int nsId){
		synchronized (lock){
			if(namespaces.get(langCode)==null){
				if(!readLocalization(langCode))
					return new HashSet<String>();
			}
			return collect(namespaces.get(langCode.toLowerCase()),nsId);
		}
	}
	
	/** Pre-load a number localizations for languages */
	public static void readLocalizations(Collection<String> langCodes){
		for(String langCode : langCodes){
			readLocalization(langCode);
		}
	}
	
	/** Collect all the names with some certain namespace id */
	protected static HashSet<String> collect(Hashtable<String,Integer> ns, int nsid) {
		HashSet<String> ret = new HashSet<String>();
		for(Entry<String,Integer> e : ns.entrySet()){
			if(e.getValue().intValue() == nsid)
				ret.add(e.getKey());
		}
		return ret;
	}

	/** Reads localization for language, return true if success */
	public static boolean readLocalization(String langCode){
		return readLocalization(langCode,0);
	}
	
	/** Level is recursion level (to detect infinite recursion if language
	 * defines itself as a fallback) */
	protected static boolean readLocalization(String langCode, int level){
		if(langCode == null || langCode.equals(""))
			return false;
		if(level == 5) // max 5 recursions in depth
			return false;
		// make title case
		langCode = langCode.substring(0,1).toUpperCase()+langCode.substring(1).toLowerCase();
		if(badLocalizations.contains(langCode)){
			log.debug("Localization for "+langCode.toLowerCase()+" previously failed");
			return false;
		}
		Configuration config = Configuration.open();
		String loc = config.getString("Localization","url");
		if(loc == null){
			log.warn("Property Localization.url not set in config file. Localization disabled.");
			return false;
		}
		URL url;
		try {
			url = new URL(MessageFormat.format(loc,langCode));
			
			PHPParser parser = new PHPParser();
			String text = parser.readURL(url);
			if(text!=null && text.length()!=0){
				Hashtable<String,Integer> ns = parser.getNamespaces(text);
				if(ns!=null && ns.size()!=0){
					namespaces.put(langCode.toLowerCase(),ns);
					log.debug("Succesfully loaded localization for "+langCode.toLowerCase());
					return true;
				} else{ // maybe a fallback language is defines instead
					String fallback = parser.getFallBack(text);
					if(fallback!=null && !fallback.equals("")){
						fallback = fallback.replace('-','_');
						boolean succ = readLocalization(fallback,level+1);
						if(succ)
							namespaces.put(langCode.toLowerCase(),namespaces.get(fallback.toLowerCase()));
						return succ;
					}
				}
			}
		} catch (Exception e) {
			log.warn("Error processing message file at "+MessageFormat.format(loc,langCode));
		}
		log.warn("Could not load localization for "+langCode);
		badLocalizations.add(langCode);
		return false;
	}
	
	/** Loads interwiki from default location lib/interwiki.map */
	public static void loadInterwiki(){
		if(interwiki != null)
			return;
		interwiki = new HashSet<String>();
		String lib = Configuration.open().getString("MWConfig","lib","./lib");
		String path = lib+"/interwiki.map";
		try{
			BufferedReader r = new BufferedReader(new FileReader(path));
			String line;
			while((line = r.readLine()) != null){
				interwiki.add(line.trim());
			}
			log.debug("Read interwiki map from "+path);
			r.close();
		} catch(Exception e){
			log.warn("Cannot read interwiki map from "+path);
		}
	}
	
	public static HashSet<String> getInterwiki(){
		if(interwiki == null)
			loadInterwiki();
		return interwiki;
	}
}
