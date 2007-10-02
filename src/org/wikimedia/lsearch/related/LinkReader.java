package org.wikimedia.lsearch.related;

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
	CompactLinks links;
	HashSet<String> interwiki;
	String langCode;
	boolean readRedirects;
	
	public LinkReader(CompactLinks links, String langCode){
		this(links,langCode,false);
	}
	
	public LinkReader(CompactLinks links, String langCode, boolean readRedirects){
		this.links = links;
		this.readRedirects = readRedirects;
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
		CompactArticleLinks p = links.get(page.Title.Namespace+":"+page.Title.Text);
		// register redirect
		Title redirect = Localization.getRedirectTitle(revision.Text,langCode);
		if(redirect != null && readRedirects){
			CompactArticleLinks cs = findArticleLinks(redirect.getNamespace(),redirect.getTitle());
			if(cs != null){
				links.setRedirect(page.Title.Namespace+":"+page.Title.Text,cs);
			}
		} else if(redirect == null){ // process only non-redirects
			processLinks(p,revision.Text,page.Title.Namespace);
		}
	}
	
	/** Find the links object for the ns:title key */
	protected CompactArticleLinks findArticleLinks(int ns, String title){
		String key;
		CompactArticleLinks rank;
		if(title.length() == 0)
			return null;
		// try exact match
		key = ns+":"+title;
		rank = links.get(key);
		if(rank != null)
			return rank;
		// try lowercase with first letter upper case
		if(title.length()==1) 
			key = ns+":"+title.toUpperCase();
		else
			key = ns+":"+title.substring(0,1).toUpperCase()+title.substring(1);
		rank = links.get(key);
		if(rank != null)
			return rank;
		
		return null;
	}
	
	/** Extract all links from this page, and increment ref count for linked pages */
	protected void processLinks(CompactArticleLinks p, String text, int namespace) {
		Pattern linkPat = Pattern.compile("\\[\\[(.*?)(\\|(.*?))?\\]\\]");
		Matcher matcher = linkPat.matcher(text);
		int ns; String title;
		boolean escaped;
		
		HashSet<CompactArticleLinks> pagelinks = new HashSet<CompactArticleLinks>(); 
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
			CompactArticleLinks target = findArticleLinks(ns,title);			
			if(target != null)
				pagelinks.add(target);				
		}
		// increment page ranks 
		for(CompactArticleLinks rank : pagelinks){			
			rank.links++;
			rank.addInLink(p);
			p.addOutLink(rank);
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
	public CompactLinks getLinks() {
		return links;
	}
	
}
