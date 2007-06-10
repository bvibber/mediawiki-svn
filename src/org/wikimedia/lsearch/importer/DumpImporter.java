package org.wikimedia.lsearch.importer;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map.Entry;
import java.util.concurrent.ThreadPoolExecutor.AbortPolicy;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.apache.log4j.Logger;
import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.ArticleLinks;
import org.wikimedia.lsearch.beans.Redirect;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.ranks.CompactArticleLinks;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.util.Localization;

public class DumpImporter implements DumpWriter {
	static Logger log = Logger.getLogger(DumpImporter.class);
	Page page;
	Revision revision;
	SimpleIndexWriter writer;
	int count = 0, limit;
	Links ranks;
	String langCode;

	public DumpImporter(String dbname, int limit, Boolean optimize, Integer mergeFactor, 
			Integer maxBufDocs, boolean newIndex, Links ranks, String langCode){
		Configuration.open(); // make sure configuration is loaded
		writer = new SimpleIndexWriter(IndexId.get(dbname), optimize, mergeFactor, maxBufDocs, newIndex);
		this.limit = limit;
		this.ranks = ranks;
		this.langCode = langCode;
	}
	public void writeRevision(Revision revision) throws IOException {
		this.revision = revision;		
	}
	public void writeStartPage(Page page) throws IOException {
		this.page = page;
	}
	public void writeEndPage() throws IOException {
		// get reference count
		String key = page.Title.Namespace+":"+page.Title.Text;
		CompactArticleLinks r = ranks.get(key);
		int references;
		boolean isRedirect = r.redirectsTo != null; 
		if(r == null){
			references = 0;
			log.error("Reference count for "+key+" is undefined, which should never happen.");
		} else
			references = r.links;
		// make list of redirects
		ArrayList<Redirect> redirects = new ArrayList<Redirect>();
		if(r.redirected != null){
			for(CompactArticleLinks rk : r.redirected){
				String[] parts = rk.toString().split(":",2);
				redirects.add(new Redirect(Integer.parseInt(parts[0]),parts[1],rk.links));
			}
		}		
		// make article
		Article article = new Article(page.Id,page.Title.Namespace,page.Title.Text,revision.Text,isRedirect,references,redirects);
		writer.addArticle(article);
		count++;
		if(limit >= 0 && count > limit)
			throw new IOException("stopped");
	}	
	
	public void close() throws IOException {
		// nop		
	}
	public void writeEndWiki() throws IOException {
		// nop
	}
	public void writeSiteinfo(Siteinfo info) throws IOException {
		// nop
	}	
	public void writeStartWiki() throws IOException {
		// nop		
	}
	
	public void closeIndex(){
		writer.close();
	}
	

}
