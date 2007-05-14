/*
 * Created on Feb 11, 2007
 *
 */
package org.wikimedia.lsearch.index;

import java.io.File;
import java.io.IOException;
import java.util.Collection;
import java.util.HashSet;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.PerFieldAnalyzerWrapper;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;

/**
 * IndexModifier for batch update of local lucene index. 
 * 
 * @author rainman
 *
 */
public class WikiIndexModifier {
	/** simple wrapper for int for the words hashtable */
	class SimpleInt{
		public int i;
		
		SimpleInt(int value){
			i = value;
		}
	}

	/** Simple implementation of batch addition and deletion */
	class SimpleIndexModifier {
		protected IndexId iid;
		protected IndexReader reader;
		protected IndexWriter writer;
		protected boolean rewrite;
		protected int maxFieldLength;
		protected String langCode;
		
		protected HashSet<IndexUpdateRecord> nonDeleteDocuments;
		
		/** All reports to be sent back to main indexer host */
		Hashtable<IndexUpdateRecord,IndexReportCard> reportQueue;
				
		// TODO : synchronize multiple threads
		
		/**
		 * SimpleIndexModifier
		 * 
		 * @param iid 
		 * @param analyzer
		 * @param rewrite - if true, will create new index
		 */
		SimpleIndexModifier(IndexId iid, String langCode, boolean rewrite){
			this.iid = iid;
			this.rewrite = rewrite;
			this.langCode = langCode;
			maxFieldLength = 0;
			reportQueue = new Hashtable<IndexUpdateRecord,IndexReportCard>();
		}
		
		public void setMaxFieldLength(int maxFieldLength) {
			this.maxFieldLength = maxFieldLength;
		}

		protected IndexReportCard getReportCard(IndexUpdateRecord rec){
			if(!rec.isReportBack())
				return null;
			IndexReportCard card = reportQueue.get(rec);
			if(card == null){
				card = new IndexReportCard(rec.getReportId(),global.getLocalhost(),iid.toString());
				reportQueue.put(rec,card);
			}
			return card;
		}
		
		/** Batch-delete documents */
		void deleteDocuments(Collection<IndexUpdateRecord> records){
			nonDeleteDocuments = new HashSet<IndexUpdateRecord>();
			try {
				reader = IndexReader.open(iid.getIndexPath());				
				for(IndexUpdateRecord rec : records){								
					if(rec.doDelete()){
						int count = reader.deleteDocuments(new Term("key", rec.getKey()));
						if(count == 0)
							nonDeleteDocuments.add(rec);
						IndexReportCard card = getReportCard(rec);
						if(card!=null){
							if(count == 0 && rec.isReportBack())
								card.setFailedDelete();
							else 
								card.setSuccessfulDelete();
						}
						log.debug(iid+": Deleting document "+rec.getKey()+" "+rec.getArticle());
					}
				}
				reader.close();
			} catch (IOException e) {
				log.warn("I/O Error: could not open/read "+iid.getIndexPath()+" while deleting document.");
				for(IndexUpdateRecord rec : records){
					nonDeleteDocuments.add(rec);
					IndexReportCard card = getReportCard(rec);
					if(card!=null)
						card.setFailedDelete();
				}
				return;
			}			
		}
		
		/** Batch-add documents */
		void addDocuments(Collection<IndexUpdateRecord> records){
			String path = iid.getIndexPath();
			try {
				writer = new IndexWriter(path,null,rewrite);
			} catch (IOException e) {				
				try {
					// try to make brand new index
					makeDBPath(path); // ensure all directories are made
					log.info("Making new index at path "+path);
					writer = new IndexWriter(path,null,true);
				} catch (IOException e1) {
					log.error("I/O error openning index for addition of documents at "+path+" : "+e.getMessage());
					return;
				}				
			}
			writer.setSimilarity(new WikiSimilarity());
			int mergeFactor = iid.getIntParam("mergeFactor",2);
			int maxBufDocs = iid.getIntParam("maxBufDocs",10);
			writer.setMergeFactor(mergeFactor);
			writer.setMaxBufferedDocs(maxBufDocs);
			writer.setUseCompoundFile(true);
			if(maxFieldLength!=0)
				writer.setMaxFieldLength(maxFieldLength);
			
			FilterFactory filters = new FilterFactory(langCode);

			for(IndexUpdateRecord rec : records){								
				if(rec.doAdd()){
					if(!rec.isAlwaysAdd() && nonDeleteDocuments.contains(rec))
						continue; // don't add if delete/add are paired operations
					IndexReportCard card = getReportCard(rec);
					Object[] ret = makeDocumentAndAnalyzer(rec.getArticle(),filters);
					Document doc = (Document) ret[0];
					Analyzer analyzer = (Analyzer) ret[1];
					try {
						writer.addDocument(doc,analyzer);
						log.debug(iid+": Adding document "+rec.getKey()+" "+rec.getArticle());
						if(card != null)
							card.setSuccessfulAdd();
					} catch (IOException e) {
						log.error("Error writing  document "+rec+" to index "+path);
						if(card != null)
							card.setFailedAdd();
					} catch(Exception e){
						e.printStackTrace();
						log.error("Error adding document "+rec.getKey()+" with message: "+e.getMessage());
						if(card != null)
							card.setFailedAdd();
					}
				}
			}
			try {
				writer.close();
			} catch (IOException e) {
				log.error("Error closing index "+path);
			}
		}

	

	}
	/**
	 * Create necessary directories for index
	 * @param dbname
	 * @return relative path (to document root) of db within filesystem 
	 */
	public static String makeDBPath(String path){
		File dir = new File(path); 
		if(!dir.exists()){
			boolean succ = dir.mkdirs();
			if(!succ){
				log.error("Could not create directory "+path+", do you have permissions to create it?");
				return null;
			}
		}		
		return path;
	}
	
	// ============================================================================
	static org.apache.log4j.Logger log = Logger.getLogger(WikiIndexModifier.class);
	protected static GlobalConfiguration global = null;
	/** iids of modified dbs (needed for snapshots) */
	protected static HashSet<IndexId> modifiedDBs = new HashSet<IndexId>();
	
	
	WikiIndexModifier(){
		if(global == null)
			global = GlobalConfiguration.getInstance();
	}
	
	/**
	 * Update all documents in the collection. If needed the request  
	 * is forwarded to a remote object (i.e. if the part of the split
	 * index is indexed by another host). 
	 * 
	 * @param iid
	 * @param updateRecords
	 */
	public void updateDocuments(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		long now = System.currentTimeMillis();
		log.info("Starting update of "+updateRecords.size()+" records on "+iid+", started at "+now);
			
		SimpleIndexModifier modifier = new SimpleIndexModifier(iid,global.getLanguage(iid.getDBname()),false);
		modifier.deleteDocuments(updateRecords);
		modifier.addDocuments(updateRecords);
		
		// send reports back to the main indexer host
		RMIMessengerClient messenger = new RMIMessengerClient();
		if(modifier.reportQueue.size() != 0)
			messenger.sendReports(modifier.reportQueue.values().toArray(new IndexReportCard[] {}),
					IndexId.get(iid.getDBname()).getIndexHost());
		
		modifiedDBs.add(iid);
		long delta = System.currentTimeMillis()-now;
		log.info("Finishing update ["+(int)((double)updateRecords.size()/delta*1000)+" articles/s] of "+updateRecords.size()+" records in "+delta+"ms on "+iid);
		
	}

	/** Close all IndexModifier instances, and optimize if needed */
	public synchronized static HashSet<IndexId> closeAllModifiers(){
		for(IndexId iid : modifiedDBs){
			if(iid.isLogical()) continue;
			if(iid.getBooleanParam("optimize",true)){
				try {
					log.debug("Optimizing "+iid);
					long start = System.currentTimeMillis();
					IndexWriter writer = new IndexWriter(iid.getIndexPath(),new SimpleAnalyzer(),false);
					writer.optimize();
					writer.close();
					long delta = System.currentTimeMillis() - start;
					log.info("Optimized "+iid+" in "+delta+" ms");
				} catch (IOException e) {
					log.error("Could not optimize index at "+iid.getIndexPath());
				}
			}
		}
		HashSet<IndexId> retVal = modifiedDBs;
		modifiedDBs = new HashSet<IndexId>();
		return retVal;
	}

	/**
	 * Make a document and a corresponding analyzer
	 * @param rec
	 * @param languageAnalyzer
	 * @return array { document, analyzer }
	 */
	public static Object[] makeDocumentAndAnalyzer(Article article, FilterFactory filters){
		PerFieldAnalyzerWrapper perFieldAnalyzer = null;
		Document doc = new Document();
		
		// This will be used to look up and replace entries on index updates.
		doc.add(new Field("key", article.getKey(), Field.Store.YES, Field.Index.UN_TOKENIZED));
		
		// These fields are returned with results
		doc.add(new Field("namespace", article.getNamespace(), Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("title", article.getTitle(),Field.Store.YES, Field.Index.TOKENIZED));
		
		// the next fields are generated using wikitokenizer 
		doc.add(new Field("contents", "", 
				Field.Store.NO, Field.Index.TOKENIZED));
		
		// each token is one category (category names themself are not tokenized)
		doc.add(new Field("category", "", 
				Field.Store.NO, Field.Index.TOKENIZED));

		String text = article.getContents();
		if(article.isRedirect())
			text=""; // for redirects index only the title
		
		perFieldAnalyzer = Analyzers.getIndexerAnalyzer(text,filters);
		
		return new Object[] { doc, perFieldAnalyzer };
	}

}
