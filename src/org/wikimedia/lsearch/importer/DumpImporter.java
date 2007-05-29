package org.wikimedia.lsearch.importer;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
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
import org.wikimedia.lsearch.beans.Rank;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.util.Localization;

public class DumpImporter implements DumpWriter {
	static Logger log = Logger.getLogger(DumpImporter.class);
	Page page;
	Revision revision;
	SimpleIndexWriter writer;
	int count = 0, limit;
	HashMap<String,Rank> ranks;
	String langCode;

	public DumpImporter(String dbname, int limit, Boolean optimize, Integer mergeFactor, 
			Integer maxBufDocs, boolean newIndex, HashMap<String,Rank> ranks, String langCode){
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
		// get rank
		String key = page.Title.Namespace+":"+page.Title.Text;
		Rank r = ranks.get(key);
		int rank;
		boolean isRedirect = r.redirectsTo != null; 
		if(r == null){
			rank = 0;
			log.error("Rank for "+key+" is undefined, which should never happen.");
		} else
			rank = r.links;
		// make article
		Article article = new Article(page.Id,page.Title.Namespace,page.Title.Text,revision.Text,isRedirect,rank,r.redirected);
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
