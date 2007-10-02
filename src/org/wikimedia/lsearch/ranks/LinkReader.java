package org.wikimedia.lsearch.ranks;

import java.io.IOException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.Map.Entry;
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
	IndexId iid;

	public LinkReader(Links links, IndexId iid, String langCode){
		this.links = links;
		if(langCode == null || langCode.equals(""))
			langCode = "en";
		this.langCode = langCode;
		this.iid = iid;
		interwiki = Localization.getInterwiki();
	}
	public void writeRevision(Revision revision) throws IOException {
		this.revision = revision;		
	}
	public void writeStartPage(Page page) throws IOException {
		this.page = page;
	}
	public void writeEndPage() throws IOException {
		Title t = new Title(page.Title.Namespace,page.Title.Text);
		try{
			links.addArticleInfo(revision.Text,t);
		} catch(Exception e){
			log.error("Error adding article "+t+" : "+e.getMessage());
			e.printStackTrace();
		}
	}	
	public void writeSiteinfo(Siteinfo info) throws IOException {
		siteinfo = info;
		// write siteinfo to localization
		Iterator it = info.Namespaces.orderedEntries();
		while(it.hasNext()){
			Entry<Integer,String> pair = (Entry<Integer,String>)it.next();
			Localization.addCustomMapping(pair.getValue(),pair.getKey(),iid.getDBname());
			links.addToNamespaceMap(pair.getValue(),pair.getKey());
		}
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
