package org.wikimedia.lsearch.related;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.Map.Entry;

import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.wikimedia.lsearch.beans.ArticleLinks;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.util.Localization;

/**
 * Read a HashSet of titles from dump
 * 
 * @author rainman
 *
 */
public class TitleReader  implements DumpWriter{
	Page page;
	Revision revision;
	CompactLinks links = new CompactLinks();
	protected String langCode;
	protected IndexId iid;
	protected NamespaceFilter nsf;
	
	public TitleReader(IndexId iid, String langCode, NamespaceFilter nsf){
		this.langCode = langCode;
		this.iid = iid;
		this.nsf = nsf;
	}

	public void writeRevision(Revision revision) throws IOException {
		this.revision = revision;		
	}
	public void writeStartPage(Page page) throws IOException {
		this.page = page;
	}
	public void writeEndPage() throws IOException {
		if(nsf.contains(page.Title.Namespace)){
			String key = page.Title.Namespace+":"+page.Title.Text;
			links.add(key,0);
		}
	}
	public CompactLinks getTitles() {
		return links;
	}
	public void close() throws IOException {
		// nop		
	}
	public void writeEndWiki() throws IOException {
		// nop
	}
	public void writeSiteinfo(Siteinfo info) throws IOException {
		// write siteinfo to localization
		Iterator it = info.Namespaces.orderedEntries();
		while(it.hasNext()){
			Entry<Integer,String> pair = (Entry<Integer,String>)it.next();
			Localization.addCustomMapping(pair.getValue(),pair.getKey(),iid.getDBname());
		}
	}	
	public void writeStartWiki() throws IOException {
		// nop		
	}
}