package org.wikimedia.lsearch.storage;

import java.io.IOException;
import java.util.Collection;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.wikimedia.lsearch.analyzers.SplitAnalyzer;
import org.wikimedia.lsearch.beans.LocalIndex;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.ranks.Related;
import org.wikimedia.lsearch.ranks.StringList;

/**
 * Store/retrieve link analysis results
 * 
 * @author rainman
 *
 */
public class LinkAnalysisStorage {
	static Logger log = Logger.getLogger(LinkAnalysisStorage.class);
	protected IndexId iid;
	protected IndexWriter writer = null;
	protected IndexReader reader = null;
	protected IndexRegistry registry = IndexRegistry.getInstance();
	
	public LinkAnalysisStorage(IndexId iid){
		this.iid = iid.getLinkAnalysis();		
	}
	
	protected void ensureWrite() throws IOException{
		if(writer == null){
			writer = new IndexWriter(iid.getImportPath(), new SplitAnalyzer(), true);
		}
	}
	
	protected void ensureRead() throws IOException{
		if(reader == null){
			LocalIndex li = registry.getLatestSnapshot(iid);
			if(li == null)
				throw new IOException("There are no snapshots for "+iid);
			
			reader = IndexReader.open(li.getPath()); 
		}
	}
	/**
	 * Add rank analysis stuff for a single article  
	 * @throws IOException
	 */
	public void addAnalitics(ArticleAnalytics aa) throws IOException{
		ensureWrite();
		//log.info("Writing analitics "+aa);
		Document doc = new Document();
		doc.add(new Field("key",aa.key,Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("references",Integer.toString(aa.references),Field.Store.YES,Field.Index.NO));
		doc.add(new Field("anchor",new StringList(aa.anchorText).toString(),Field.Store.YES,Field.Index.NO));
		doc.add(new Field("related",new StringList(Related.convertToStringList(aa.related)).toString(),Field.Store.YES,Field.Index.NO));
		doc.add(new Field("redirect",new StringList(aa.redirectKeys).toString(),Field.Store.YES,Field.Index.NO));
		if(aa.redirectTarget != null)
			doc.add(new Field("redirect_to",aa.redirectTarget,Field.Store.YES,Field.Index.NO));
		writer.addDocument(doc);
	}
	
	public void snapshot() throws IOException{
		if(writer != null){
			writer.optimize();
			writer.close();
			writer = null;
			IndexThread.makeIndexSnapshot(iid,iid.getImportPath());
			registry.refreshSnapshots(iid);
		}
	}
	
	/**
	 * Read analitics from latest link analysis index snapshot
	 * @param key ns:title 
	 * @return
	 * @throws IOException
	 */
	public ArticleAnalytics getAnalitics(String key) throws IOException{
		ensureRead();
		
		TermDocs td = reader.termDocs(new Term("key",key));
		if(td.next()){
			Document d = reader.document(td.doc());
			int ref = Integer.parseInt(d.get("references"));
			StringList anchor = new StringList(d.get("anchor"));
			StringList related = new StringList(d.get("related"));
			StringList redirect = new StringList(d.get("redirect"));
			String redirectTarget = d.get("redirect_to");
			return new ArticleAnalytics(key,ref,redirectTarget,
					anchor.toCollection(),
					Related.convertToRelatedList(related.toCollection()),
					redirect.toCollection());
		}
		
		return null;
	}
}
