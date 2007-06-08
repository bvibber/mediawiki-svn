package org.wikimedia.lsearch.importer;

import java.io.IOException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.apache.commons.lang.WordUtils;
import org.apache.log4j.Logger;
import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.ArticleLinks;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.util.Localization;

/** 
 * Reads page links and references, i.e. how many times a page 
 * is referenced within other articles.
 *  
 * @author rainman
 *
 */
public class LinkReader implements DumpWriter {
	static Logger log = Logger.getLogger(LinkReader.class);
	Page page;
	Revision revision;
	Siteinfo siteinfo;
	/** ns:title -> number of referring articles */
	HashMap<String,ArticleLinks> links = new HashMap<String,ArticleLinks>();
	HashSet<String> interwiki;
	String langCode;

	public LinkReader(HashMap<String,ArticleLinks> links, String langCode){
		this.links = links;
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
		ArticleLinks r = links.get(page.Title.Namespace+":"+page.Title.Text);
		// register redirect
		Title redirect = Localization.getRedirectTitle(revision.Text,langCode);
		if( redirect !=null ){
			r.redirectsTo = findArticleLinks(redirect.getNamespace(),redirect.getTitle());
		} else // process links
			processLinks(revision.Text,page.Title.Namespace);		
	}
	
	/** Find the links object for the ns:title key */
	protected ArticleLinks findArticleLinks(int ns, String title){
		String key;
		ArticleLinks rank;
		// try exact match
		key = ns+":"+title;
		rank = links.get(key);
		if(rank != null)
			return rank;
		// try lowercase
		key = ns+":"+title.toLowerCase();
		rank = links.get(key);
		if(rank != null)
			return rank;
		// try title case
		key = ns+":"+WordUtils.capitalize(title);
		rank = links.get(key);
		if(rank != null)
			return rank;
		// try capitalizing at word breaks
		key = ns+":"+WordUtils.capitalize(title,new char[] {' ','-','(',')','}','{','.',',','?','!'});
		rank = links.get(key);
		if(rank != null)
			return rank;
		
		return null;
	}
	
	/** Extract all links from this page, and increment ref count for linked pages */
	protected void processLinks(String text, int namespace) {
		Pattern linkPat = Pattern.compile("\\[\\[(.*?)(\\|(.*?))?\\]\\]");
		Matcher matcher = linkPat.matcher(text);
		int ns; String title;
		boolean escaped;
		
		HashSet<ArticleLinks> pagelinks = new HashSet<ArticleLinks>(); 
		while(matcher.find()){
			String link = matcher.group(1);
			int fragment = link.lastIndexOf('#');
			if(fragment != -1)
				link = link.substring(0,fragment);
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
				Integer inx = siteinfo.Namespaces.getIndex(parts[0].substring(0,1).toUpperCase()+parts[0].substring(1).toLowerCase());
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
			if(ns == 0 && namespace!=0)
				continue; // skip links from other namespaces into the main namespace
			
			// register as link
			ArticleLinks target = findArticleLinks(ns,title);			
			if(target != null)
				pagelinks.add(target);				
		}
		// increment page ranks 
		for(ArticleLinks rank : pagelinks){			
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
	public HashMap<String, ArticleLinks> getRanks() {
		return links;
	}
	
}
