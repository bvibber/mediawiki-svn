package org.wikimedia.lsearch.importer;

import java.io.IOException;

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

	public DumpImporter(String dbname){
		Configuration.open(); // make sure configuration is loaded
		writer = new SimpleIndexWriter(IndexId.get(dbname));
	}
	public void writeRevision(Revision revision) throws IOException {
		this.revision = revision;		
	}
	public void writeStartPage(Page page) throws IOException {
		this.page = page;
	}
	public void writeEndPage() throws IOException {
		Article article = new Article(page.Title.Namespace,page.Title.Text,revision.Text,revision.isRedirect());
		writer.addArticle(article);		
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
