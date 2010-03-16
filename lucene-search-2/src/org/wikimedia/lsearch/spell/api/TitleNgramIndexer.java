package org.wikimedia.lsearch.spell.api;

import java.io.IOException;
import java.util.Collection;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.index.Transaction;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.util.ProgressReport;

public class TitleNgramIndexer {
	static Logger log = Logger.getLogger(TitleNgramIndexer.class);
	NgramIndexer indexer;
	IndexId iid;
	String path;
	
	/** Create new at import path */
	public static TitleNgramIndexer createNew(IndexId iid) throws IOException {
		return new TitleNgramIndexer(iid,true,true);
	}
	/** Open for modification at index path */
	public static TitleNgramIndexer openForModification(IndexId iid) throws IOException {
		return new TitleNgramIndexer(iid,false,true);
	}	
	/** Open for batch modification at index path */
	public static TitleNgramIndexer openForBatchModification(IndexId iid) throws IOException {
		return new TitleNgramIndexer(iid,false,false);
	}
	
	private TitleNgramIndexer(IndexId iid, boolean makeNew, boolean openWriter) throws IOException {
		iid = iid.getTitleNgram();
		this.iid = iid;
		indexer = new NgramIndexer();
		if(openWriter){
			if(makeNew){
				path = iid.getImportPath();
				indexer.createIndex(path,new SimpleAnalyzer());
			} else{
				path = iid.getIndexPath();
				indexer.reopenIndex(path,new SimpleAnalyzer());
			}
		}
	}
	
	/** Old-fashioned batch update, all-delete + all-add */
	public void batchUpdate(Collection<IndexUpdateRecord> records) throws IOException {
		Transaction trans = new Transaction(iid, IndexId.Transaction.INDEX);
		trans.begin();
		try{
			IndexReader reader = IndexReader.open(iid.getIndexPath()); 
			// batch delete
			for(IndexUpdateRecord rec : records){
				if(rec.doDelete()){
					Article a = rec.getArticle();
					log.debug(iid+": Deleting "+a);
					reader.deleteDocuments(new Term("pageid",rec.getIndexKey()));
				}
			}
			reader.close();
			// batch add
			indexer.reopenIndex(iid.getIndexPath(),new SimpleAnalyzer());
			for(IndexUpdateRecord rec : records){
				if(rec.doAdd()){
					Article a = rec.getArticle();
					log.debug(iid+": Adding "+a.toStringFull());
					addTitle(a.getNamespace(),a.getTitle(),a.getRedirectTarget(),a.getRank(),Long.toString(a.getPageId()));
				}
			}
			indexer.close();
			trans.commit();
		} catch(IOException e){
			trans.rollback();
			throw e;
		}
	}
	
	public void deleteTitle(String pageId){
		indexer.deleteDocuments(new Term("pageid",pageId));
	}
	
	public void addTitle(String ns, String title, String redirectTo, int rank, String pageId){
		// since most queries will be on main namespace, put it into separate field
		String field = ns.equals("0")? "title0" :  "title";
		// use decomposed/normalized title for ngram indexes
		String decomposed = FastWikiTokenizerEngine.stripTitle(title.toLowerCase());
		
		Document doc = new Document();		
		// pageId is primary key
		doc.add(new Field("pageid", pageId, Field.Store.NO, Field.Index.UN_TOKENIZED));
		if(!ns.equals("0"))
			doc.add(new Field("namespace", ns, Field.Store.NO, Field.Index.UN_TOKENIZED));
		doc.add(new Field("key", ns+":"+title, Field.Store.YES, Field.Index.NO));
		doc.add(new Field(field, decomposed, Field.Store.YES, Field.Index.NO));
		if(redirectTo != null)
			doc.add(new Field("redirect", redirectTo, Field.Store.YES, Field.Index.NO));
		doc.add(new Field("rank", Integer.toString(rank) , Field.Store.YES, Field.Index.NO));
		
				
		if(decomposed.length()>0)
			indexer.createNgramFields(doc,field,decomposed,NgramIndexer.Type.TITLE_NGRAM);
		indexer.addDocument(doc);
	}
	
	public void close() throws IOException{
		indexer.close();
	}
	
	public void closeAndOptimize() throws IOException{
		indexer.closeAndOptimize();
	}
	
	public void snapshot() throws IOException {
		IndexThread.makeIndexSnapshot(iid,path);
	}
	
	/** Make new index at import part from (standalone) links */
	public static void importFromLinks(IndexId iid) throws IOException {
		Links links = Links.openStandalone(iid); 
		TitleNgramIndexer indexer = createNew(iid);
		LuceneDictionary dict = links.getKeys();
		dict.setProgressReport(new ProgressReport("titles",1000));
		Word w;
		while((w = dict.next()) != null){
			String key = w.getWord();
			String ns = key.substring(0,key.indexOf(':'));
			String title = key.substring(key.indexOf(':')+1);
			String redirect = links.getRedirectTarget(key);
			int rank = links.getRank(key);
			String pageId = links.getPageId(key);
			indexer.addTitle(ns,title,redirect,rank,pageId);
		}
		log.info("Optimizing...");
		indexer.closeAndOptimize();
		indexer.snapshot();
		links.close();
	}
}
