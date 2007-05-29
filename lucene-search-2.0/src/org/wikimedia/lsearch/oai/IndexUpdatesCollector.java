package org.wikimedia.lsearch.oai;

import java.io.IOException;
import java.util.ArrayList;

import org.apache.log4j.Logger;
import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexUpdateRecord;

public class IndexUpdatesCollector implements DumpWriter {
	Logger log = Logger.getLogger(DumpWriter.class);
	protected Page page;
	protected Revision revision;
	protected ArrayList<IndexUpdateRecord> records = new ArrayList<IndexUpdateRecord>();
	protected IndexId iid;
	protected int references = 0;
	protected ArrayList<String> redirects = new ArrayList<String>();
	
	public IndexUpdatesCollector(IndexId iid){
		this.iid = iid;
	}
	
	public void addRedirect(String redirectTitle, int references) {
		redirects.add(redirectTitle);
		addReferences(references);		
	}
	public void addDeletion(long pageId){
		// pageId is enough for page deletion
		Article article = new Article(pageId,-1,"","",false,1);
		records.add(new IndexUpdateRecord(iid,article,IndexUpdateRecord.Action.DELETE));
		log.debug(iid+": Deletion for "+pageId);
	}
	
	public ArrayList<IndexUpdateRecord> getRecords() {
		return records;
	}

	public void writeRevision(Revision revision) throws IOException {
		this.revision = revision;		
	}
	public void writeStartPage(Page page) throws IOException {
		this.page = page;
	}
	public void writeEndPage() throws IOException {		
		Article article = new Article(page.Id,page.Title.Namespace,page.Title.Text,revision.Text,revision.isRedirect(),references,redirects);
		//log.info("Collected "+article+" with rank "+references+" and "+redirects.size()+" redirects: "+redirects);
		records.add(new IndexUpdateRecord(iid,article,IndexUpdateRecord.Action.UPDATE));
		log.debug(iid+": Update for "+article);
		references = 0;
		redirects.clear();
	}	
	
	public void close() throws IOException {
	}

	public void writeEndWiki() throws IOException {
	}

	public void writeSiteinfo(Siteinfo info) throws IOException {
	}

	public void writeStartWiki() throws IOException {
	}

	public int getReferences() {
		return references;
	}

	public void addReferences(int references) {
		this.references += references;
	}



	
	
}
