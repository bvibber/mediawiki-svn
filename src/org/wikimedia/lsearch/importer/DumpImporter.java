package org.wikimedia.lsearch.importer;

import java.io.IOException;
import java.util.concurrent.ThreadPoolExecutor.AbortPolicy;

import org.apache.log4j.Logger;
import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;

public class DumpImporter implements DumpWriter {
	static Logger log = Logger.getLogger(DumpImporter.class);
	Page page;
	Revision revision;
	SimpleIndexWriter writer;
	int count = 0, limit;

	public DumpImporter(String dbname, int limit, Boolean optimize, Integer mergeFactor, Integer maxBufDocs, boolean newIndex){
		Configuration.open(); // make sure configuration is loaded
		writer = new SimpleIndexWriter(IndexId.get(dbname), optimize, mergeFactor, maxBufDocs, newIndex);
		this.limit = limit;
	}
	public void writeRevision(Revision revision) throws IOException {
		this.revision = revision;		
	}
	public void writeStartPage(Page page) throws IOException {
		this.page = page;
	}
	public void writeEndPage() throws IOException {
		Article article = new Article(page.Id,page.Title.Namespace,page.Title.Text,revision.Text,revision.isRedirect());
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
