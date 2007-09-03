package org.wikimedia.lsearch.ranks;

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
	Links links;
	HashSet<String> interwiki;
	String langCode;

	public LinkReader(Links links, String langCode){
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
		links.addArticleInfo(revision.Text,new Title(page.Title.Namespace,page.Title.Text));
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
	public Links getLinks() {
		return links;
	}
	
}
