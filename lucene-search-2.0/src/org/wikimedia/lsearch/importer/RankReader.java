package org.wikimedia.lsearch.importer;

import java.io.IOException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.apache.log4j.Logger;
import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.Rank;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.util.Localization;

/** 
 * Reads page ranks, i.e. how many times a page is referenced
 *  within other articles.
 *  
 * @author rainman
 *
 */
public class RankReader implements DumpWriter {
	static Logger log = Logger.getLogger(RankReader.class);
	Page page;
	Revision revision;
	Siteinfo siteinfo;
	/** ns:title -> number of referring articles */
	HashMap<String,Rank> ranks = new HashMap<String,Rank>();
	HashSet<String> interwiki;
	String langCode;

	public RankReader(HashMap<String,Rank> ranks, String langCode){
		this.ranks = ranks;
		if(langCode == null || langCode.equals(""))
			langCode = "en";
		this.langCode = langCode;
		interwiki = Localization.getInterwiki();
	}
	public void writeRevision(Revision revision) throws IOException {
		this.revision = revision;		
	}
	public void writeStartPage(Page page) throws IOException {
		this.page = page;
	}
	public void writeEndPage() throws IOException {
		Rank r = ranks.get(page.Title.Namespace+":"+page.Title.Text.toLowerCase());
		// register redirect
		String redirect = Localization.getRedirectTarget(revision.Text,langCode);
		if( redirect !=null ){
			redirect = redirect.toLowerCase();
			int ns = 0;
			String title = redirect;
			String[] parts = redirect.split(":",2);
			if(parts.length == 2 && parts[0].length()>1){
				Integer inx = siteinfo.Namespaces.getIndex(parts[0].substring(0,1).toUpperCase()+parts[0].substring(1));
				if(inx != null){
					ns = inx;
					title = parts[1];
				}
			}
			r.redirect = ns+":"+title;
		} else // process links
			processRanks(revision.Text,page.Title.Namespace);		
	}
	
	/** Extract all links from this page, and increment ranks for linked pages */
	protected void processRanks(String text, int namespace) {
		Pattern linkPat = Pattern.compile("\\[\\[(.*?)(\\|(.*?))?\\]\\]");
		Matcher matcher = linkPat.matcher(text);
		int ns; String title;
		boolean escaped;
		
		HashSet<String> links = new HashSet<String>(); 
		while(matcher.find()){
			String link = matcher.group(1).toLowerCase();
			int fragment = link.lastIndexOf('#');
			if(fragment != -1)
				link = link.substring(0,fragment);
			if(link.length() > 100)
				continue; // probably an error
			//System.out.println("Got link "+link);
			if(link.startsWith(":")){
				escaped = true;
				link = link.substring(1);
			} else escaped = false;
			ns = 0; 
			title = link;			
			// check for ns:title syntax
			String[] parts = link.split(":",2);
			if(parts.length == 2 && parts[0].length() > 1){
				Integer inx = siteinfo.Namespaces.getIndex(parts[0].substring(0,1).toUpperCase()+parts[0].substring(1));
				if(!escaped && (parts[0].equalsIgnoreCase("category") || (inx!=null && inx==14)))
					continue; // categories, ignore
				if(inx!=null && inx < 0) 
					continue; // special pages, ignore
				if(inx != null){
					ns = inx;
					title = parts[1];
				}
				
				// ignore interwiki links
				if(interwiki.contains(parts[0]))
					continue;
			}
			// register as link
			String key = ns+":"+title;
			links.add(key);
		}
		// increment page ranks 
		for(String t : links){
			if(t.startsWith("0:") && namespace!=0)
				continue; // skip links from other namespaces into the main namespace
			Rank rank = ranks.get(t);
			if(rank != null)
				rank.links++;
		}
	}
	public void writeSiteinfo(Siteinfo info) throws IOException {
		siteinfo = info;
	}	
	public void close() throws IOException {
		// nop		
	}
	public void writeEndWiki() throws IOException {
		// nop
	}
	public void writeStartWiki() throws IOException {
		// nop		
	}
	public HashMap<String, Rank> getRanks() {
		return ranks;
	}
	
}
