/*
 * Created on Feb 11, 2007
 *
 */
package org.wikimedia.lsearch.index;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Map;
import java.util.Set;
import java.util.TimeZone;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.PerFieldAnalyzerWrapper;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.document.Field.Index;
import org.apache.lucene.document.Field.Store;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;
import org.wikimedia.lsearch.analyzers.Aggregate;
import org.wikimedia.lsearch.analyzers.AggregateAnalyzer;
import org.wikimedia.lsearch.analyzers.Alttitles;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.CategoryAnalyzer;
import org.wikimedia.lsearch.analyzers.ContextAnalyzer;
import org.wikimedia.lsearch.analyzers.ExtToken;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.analyzers.KeywordsAnalyzer;
import org.wikimedia.lsearch.analyzers.LanguageAnalyzer;
import org.wikimedia.lsearch.analyzers.RelatedAnalyzer;
import org.wikimedia.lsearch.analyzers.ReusableLanguageAnalyzer;
import org.wikimedia.lsearch.analyzers.StopWords;
import org.wikimedia.lsearch.analyzers.TokenizerOptions;
import org.wikimedia.lsearch.analyzers.WikiTokenizer;
import org.wikimedia.lsearch.analyzers.Aggregate.Flags;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.beans.Redirect;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.prefix.PrefixIndexBuilder;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.related.Related;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.spell.CleanIndexImporter;
import org.wikimedia.lsearch.spell.CleanIndexWriter;
import org.wikimedia.lsearch.spell.api.SpellCheckIndexer;
import org.wikimedia.lsearch.spell.api.TitleNgramIndexer;
import org.wikimedia.lsearch.storage.RelatedStorage;
import org.wikimedia.lsearch.util.Buffer;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.MathFunc;
import org.wikimedia.lsearch.util.StringUtils;

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

	static public final int MAX_FIELD_LENGTH = 100000;
	/** number of aditional alttitle1, alttitle2, .. etc fields to be filled in with redirects */
	static public int ALT_TITLES = 3;	
	
	/** Index-time boost for headings, multiplied with article rank */
	static public final float HEADINGS_BOOST = 0.05f;
	
	/** By how much should be anchor text score by scaled (in alttitle) */
	static public final float ANCHOR_SCALE = 0.1f;

	/** Simple implementation of batch addition and deletion */
	class SimpleIndexModifier {
		protected IndexId iid;
		protected IndexReader reader;
		protected IndexWriter writer;
		protected boolean rewrite;		
		protected String langCode;
		protected boolean exactCase;
		
		// TODO : synchronize multiple threads
		
		/**
		 * SimpleIndexModifier
		 * 
		 * @param iid 
		 * @param indexAnalyzer
		 * @param rewrite - if true, will create new index
		 * @param original - the source iid (for titles indexes, e.g. for en-titles.tspart1 it could be enwiki) 
		 */
		SimpleIndexModifier(IndexId iid, String langCode, boolean rewrite, boolean exactCase){
			this.iid = iid;
			this.rewrite = rewrite;
			this.langCode = langCode;
			this.exactCase = exactCase;
		}
		
		/** Batch-delete documents, returns true if successfull */
		boolean deleteDocuments(Collection<IndexUpdateRecord> records){			
			try {
				try{
					reader = IndexReader.open(iid.getIndexPath());
				} catch(IOException e){
					if(IndexReader.isLocked(iid.getIndexPath())){
						// unlock the index, and then retry
						unlockIndex(iid.getIndexPath());
						reader = IndexReader.open(iid.getIndexPath());
					} else
						throw e;
				}
				for(IndexUpdateRecord rec : records){
					String suffix = rec.getIndexId().getDB().getTitlesSuffix();
					if(rec.doDelete()){
						if(iid.isHighlight())
							reader.deleteDocuments(new Term("pageid", rec.getIndexKey()));
						else if(iid.isTitlesBySuffix())
							reader.deleteDocuments(new Term("pageid", suffix+":"+rec.getIndexKey()));
						else // normal or titles index
							reader.deleteDocuments(new Term("key", rec.getIndexKey()));
						log.debug(iid+": Deleting document "+rec.getArticle());
					}
				}
				reader.close();
			} catch (IOException e) {
				log.warn("I/O Error: could not open/read "+iid.getIndexPath()+" while deleting document.");
				return false;
			}
			return true;
		}
		
		/** Batch-add documents, returns true if successfull */
		boolean addDocuments(Collection<IndexUpdateRecord> records){
			boolean succ = true;
			String path = iid.getIndexPath();
			try{
				writer = openForWrite(path,rewrite);
			} catch(IOException e){
				return false;
			}
			
			writer.setSimilarity(new WikiSimilarity());
			int mergeFactor = iid.getIntParam("mergeFactor",20);
			int maxBufDocs = iid.getIntParam("maxBufDocs",500);
			writer.setMergeFactor(mergeFactor);
			writer.setMaxBufferedDocs(maxBufDocs);
			writer.setUseCompoundFile(true);
			writer.setMaxFieldLength(MAX_FIELD_LENGTH);
			FieldBuilder.Case dCase = (exactCase)? FieldBuilder.Case.EXACT_CASE : FieldBuilder.Case.IGNORE_CASE; 
			FieldBuilder builder = new FieldBuilder(iid,dCase);
			Analyzer indexAnalyzer = null;
			Analyzer highlightAnalyzer = null;
			highlightAnalyzer = Analyzers.getHighlightAnalyzer(iid,exactCase); 			
			indexAnalyzer = Analyzers.getIndexerAnalyzer(new FieldBuilder(iid,FieldBuilder.Case.EXACT_CASE));
			
			HashSet<String> stopWords = StopWords.getPredefinedSet(iid);
			for(IndexUpdateRecord rec : records){								
				if(rec.doAdd()){
					if(!checkPreconditions(rec))
						continue; // article shouldn't be added for some reason					
					Document doc;					
					try {
						if(iid.isHighlight()){
							doc = makeHighlightDocument(rec.getArticle(),builder,iid);
							writer.addDocument(doc,highlightAnalyzer);
						} else if(iid.isTitlesBySuffix()){
							IndexId orig = rec.getIndexId().getDB();
							doc = makeTitleDocument(rec.getArticle(),indexAnalyzer,highlightAnalyzer,iid,orig.getTitlesSuffix(),orig.getDBname(),exactCase,stopWords);
							writer.addDocument(doc,indexAnalyzer);
						} else{ // normal index
							doc = makeDocument(rec.getArticle(),builder,iid,stopWords,indexAnalyzer);
							writer.addDocument(doc,indexAnalyzer);
						}
												
						log.debug(iid+": Adding document "+rec.getArticle().toStringFull());
					} catch (IOException e) {
						log.error("Error writing  document "+rec+" to index "+path);
						succ = false; // report unsucc, but still continue, to process all cards 
					} catch(Exception e){
						e.printStackTrace();
						log.error("Error adding document "+rec.getIndexKey()+" with message: "+e.getMessage());
						succ = false; // report unsucc, but still continue, to process all cards
					}
				}
			}
			try {
				writer.close();					
			} catch (IOException e) {
				log.error("Error closing index "+path);
				return false;
			}
			return succ;
		}

		public boolean checkPreconditions(IndexUpdateRecord rec){
			return checkAddPreconditions(rec.getArticle(),rec.getIndexId());
		}
	}
	
	public static IndexWriter openForWrite(String path, boolean rewrite) throws IOException{
		return openForWrite(path,rewrite,null);
	}
	public static IndexWriter openForWrite(String path, boolean rewrite, Analyzer analyzer) throws IOException{
		try {
			return new IndexWriter(path,analyzer,rewrite);
		} catch (IOException e) {				
			try {
				// unlock, retry
				if(!new File(path).exists()){
					// try to make brand new index
					makeDBPath(path); // ensure all directories are made
					log.info("Making new index at path "+path);
					return new IndexWriter(path,analyzer,true);
				} else if(IndexReader.isLocked(path)){
					unlockIndex(path);
					return new IndexWriter(path,analyzer,rewrite); 					
				} else
					throw e;
			} catch (IOException e1) {
				e1.printStackTrace();
				log.error("I/O error openning index at "+path+" : "+e.getMessage());
				throw e1;
			}				
		}
	}
	
	/**
	 * Check if the article should be added. For instance, we don't want
	 * useless redirect in our index, i.e. Robin hood -> Robin Hood
	 * @param rec
	 * @param langCode
	 * @return
	 */	
	public static boolean checkAddPreconditions(Article ar, IndexId iid){
		int ns = Integer.parseInt(ar.getNamespace());
		if(ar.isRedirect() && ar.getRedirectTargetNamespace()==ns)
			return false; // don't add redirects to same namespace, always add as redirect field
		return true;
	}
	
	/**
	 * Generate the articles transient characterstics needed only for indexing, 
	 * i.e. list of redirect keywords and article rank. 
	 * 
	 * @param article
	 */
	public static void transformArticleForIndexing(Article ar) {
		ArrayList<Redirect> redirects = ar.getRedirects();
		// sort redirect by their rank
		Collections.sort(redirects,new Comparator<Redirect>() {
			public int compare(Redirect o1,Redirect o2){
				return o2.getReferences() - o1.getReferences();
			}
		});		
		int ns = Integer.parseInt(ar.getNamespace());
		ar.setRank(ar.getReferences()); // base rank value
		if(redirects != null){
			ArrayList<String> filtered = new ArrayList<String>();
			ArrayList<Integer> ranks = new ArrayList<Integer>();
			ArrayList<Redirect> sorted = new ArrayList<Redirect>();
			// index only redirects from the same namespace
			// to avoid a lot of unusable redirects from/to
			// user namespace, but always index redirect FROM main
			for(Redirect r : redirects){
				if(ns == r.getNamespace() || (r.getNamespace() == 0 && ns != 0) || (r.getNamespace() != 0 && ns != 0)){
					filtered.add(r.getTitle());
					ranks.add(r.getReferences());
					sorted.add(r);
					ar.addToRank(r.getReferences()+1);					
				} else
					log.debug("Ignoring redirect "+r+" to "+ar);
			}
			ar.setRedirectKeywords(filtered);
			ar.setRedirectKeywordRanks(ranks);
			ar.setRedirectsSorted(sorted);
		}
	}
	
	/** Check if for this article for this db we should extract keywords */ 
	public static boolean checkKeywordPreconditions(Article article, IndexId iid) {
		if(global == null)
			global = GlobalConfiguration.getInstance();
		if(article.getNamespace().equals("0") && global.useKeywordScoring(iid.getDBname()))
			return true;
		else
			return false;
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
	
	/** 
	 * Try unlocking the index. This should only be called on index recovery
	 * 
	 * IMPORTANT: assumes single-threaded application and one indexer !!!! 
	 */
	public static void unlockIndex(String path){
		try{
			if(IndexReader.isLocked(path)){
				Directory dir = FSDirectory.getDirectory(path, false);
				IndexReader.unlock(dir);
				log.info("Unlocked index at "+path);
			}
		} catch(IOException e){
			log.warn("I/O error unlock index at "+path+" : "+e.getMessage());
		}
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
	 * Fetch links info and update precursor indexes (for spell, prefix, title_ngram) 
	 * 
	 * @param iid
	 * @param updateRecords
	 * @return
	 */
	public boolean updateLinksAndPrecursors(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		// note: we assume articles are shared between links are index update records
		return updatePrefix(iid,updateRecords)
		    && updateSpell(iid,updateRecords)
		    && updateSimilar(iid,updateRecords);
	}
	
	/**
	 * Update search/highlight/titles indexes
	 *  
	 * @param iid
	 * @param updateRecords
	 * @param additionalFetch - pageids that need to be further fetched to update redirect info
	 * @return success
	 */
	public boolean updateDocuments(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		try{
		return updateDocumentsOn(iid,updateRecords,iid)
		    && updateDocumentsOn(iid.getHighlight(),updateRecords,iid)
		    && updateTitles(iid,updateRecords);
		} catch(Exception e){
			e.printStackTrace();
			log.error("Error updating "+iid+" : "+e.getMessage());
			return false;
		}
	}
	
	/** Wrapper for fetchAdditional(IndexId,Collection<IndexUpdateRecord>) */
	public static Set<String> fetchAdditional(IndexId iid, IndexUpdateRecord record, Links links) throws IOException {
		ArrayList<IndexUpdateRecord> list = new ArrayList<IndexUpdateRecord>();
		list.add(record);
		return fetchAdditional(iid,list,links);
		
	}
	
	/** Wrapper for fetchAdditional(IndexId,Collection<IndexUpdateRecord>) */
	public static Set<String> fetchAdditional(IndexId iid, IndexUpdateRecord[] records, Links links) throws IOException {
		ArrayList<IndexUpdateRecord> list = new ArrayList<IndexUpdateRecord>(records.length);
		for(IndexUpdateRecord r : records)
			list.add(r);
		return fetchAdditional(iid,list,links);
		
	}
	
	/** 
	 * If a rediret A->B changes to A->C then we need to update both B,C in addition to A, to maintain
	 * correct redirects-to info at all times
	 * 
	 * This function will fetch these additional page ids, these should then be returned to 
	 * the incremental updater client, and redelivered for updates to be complete 
	 *  
	 * @param iid
	 * @param updateRecords
	 * @return additionalFetch - list of article pageids to fetch
	 */
	public static Set<String> fetchAdditional(IndexId iid, Collection<IndexUpdateRecord> updateRecords, Links links) throws IOException {
		HashSet<String> additionalFetch = new HashSet<String>();

		HashSet<String> allKeys = new HashSet<String>();
		HashSet<String> allPageIds = new HashSet<String>();
		for(IndexUpdateRecord rec : updateRecords){
			allKeys.add(rec.getNsTitleKey());
			allPageIds.add(rec.getIndexKey());
		}
		iid = iid.getLinks();
		for(IndexUpdateRecord rec : updateRecords){
			Article a = rec.getArticle();
			// where redirect currently leads
			String redirect = Localization.getRedirectKey(a.getContents(),iid);
			if(redirect != null && !allKeys.contains(redirect)){
				String pageId = links.getPageId(redirect);
				if(pageId != null && !allPageIds.contains(pageId)){
					additionalFetch.add(pageId);
					allKeys.add(redirect);
					allPageIds.add(pageId);
				}
			}
			// where redirect used to lead
			String oldRedirect = links.getRedirectTarget(rec.getNsTitleKey());
			if(oldRedirect != null && !allKeys.contains(oldRedirect)){
				String pageId = links.getPageId(oldRedirect);
				if(pageId != null && !allPageIds.contains(pageId)){
					additionalFetch.add(pageId);
					allKeys.add(oldRedirect);
					allPageIds.add(pageId);
				}
			}
		}
		return additionalFetch;
	}
	
	public static boolean fetchLinksInfo(IndexId iid, IndexUpdateRecord[] updateRecords, Links links) throws IOException {
		ArrayList<IndexUpdateRecord> list = new ArrayList<IndexUpdateRecord>(updateRecords.length);
		for(IndexUpdateRecord r : updateRecords)
			list.add(r);
		return fetchLinksInfo(iid,list,links);
	}
	
	/** Update articles with latest linking & related information */ 
	public static boolean fetchLinksInfo(IndexId iid, Collection<IndexUpdateRecord> updateRecords, Links links) throws IOException {
		try{
			RelatedStorage related = new RelatedStorage(iid);
			for(IndexUpdateRecord rec : updateRecords){
				if(rec.doAdd()){
					String key = rec.getNsTitleKey();
					Article article = rec.getArticle();
					Hashtable<String,Integer> anchors = new Hashtable<String,Integer>();
					// references, redirect status, anchors
					article.setReferences(links.getNumInLinks(key));
					article.setRedirectTo(links.getRedirectTarget(key));
					anchors.putAll(links.getAnchorMap(key,article.getReferences()));
					if(article.isRedirect()){
						article.setRedirectTargetNamespace(links.getRedirectTargetNamespace(key));
						article.setRedirectRank(links.getRank(article.getRedirectTarget()));
					} else
						article.setRedirectTargetNamespace(-1);
					
					// redirects				
					ArrayList<Redirect> redirects = new ArrayList<Redirect>();
					for(String rk : links.getRedirectsTo(key)){
						String[] parts = rk.toString().split(":",2);
						int redirectRef = links.getNumInLinks(rk);
						redirects.add(new Redirect(Integer.parseInt(parts[0]),parts[1],redirectRef));
						Links.mergeAnchorMaps(anchors,links.getAnchorMap(rk,redirectRef));
					}
					article.setRedirects(redirects);
					article.setAnchors(anchors);
					// related
					if(related != null)
						article.setRelated(related.getRelated(key));
				}
			}
			return true;
		} catch(IOException e){
			e.printStackTrace();
			log.error("Cannot fetch links info: "+e.getMessage());
			throw e;
		}
	}
	
	public boolean updateLinks(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		IndexId iidLinks = iid.getLinks();
		Transaction trans = new Transaction(iidLinks,IndexId.Transaction.INDEX);
		try{
			trans.begin();
			Links links = Links.openForModification(iid);
			for(IndexUpdateRecord rec : updateRecords){
				// TODO: this might do some unnecessary additions/deletions on split index architecture
				if(rec.doDelete()){
					Article a = rec.getArticle();
					log.debug(iidLinks+": Deleting "+a);
					if(a.getTitle()==null || a.getTitle().equals("")){
						// try to fetch ns:title so we can have nicer debug info					
						String key = links.getKeyFromPageId(rec.getIndexKey());
						if(key != null)
							a.setNsTitleKey(key);
					}
					links.deleteArticleInfoByIndexKey(rec.getIndexKey());					
				} 
				if(rec.doAdd()){
					Article a = rec.getArticle();
					log.debug(iidLinks+": Adding "+a);
					links.addArticleInfo(a.getContents(),a.getTitleObject(),iid.isExactCase(),a.getIndexKey());
				}
			}
			links.close();
			trans.commit();
			return true;
		} catch(IOException e){
			trans.rollback();
			e.printStackTrace();
			log.error("Cannot update links index: "+e.getMessage());
			return false;
		}
	}
	
	public boolean updatePrefix(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		if(!iid.hasPrefix())
			return true;
		
		try{
			PrefixIndexBuilder prefix = PrefixIndexBuilder.forPrecursorBatchModification(iid);
			prefix.batchUpdate(updateRecords);
			return true;
		} catch(IOException e){
			e.printStackTrace();
			log.error("Cannot update prefix index: "+e.getMessage());
			return false;
		}
	}
	
	public boolean updateSpell(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		if(!iid.hasSpell())
			return true;
		try{
			CleanIndexWriter writer = CleanIndexWriter.openForBatchModification(iid.getSpell().getPrecursor());
			writer.batchUpdate(updateRecords);
			return true;
		} catch(IOException e){
			e.printStackTrace();
			log.error("Cannot update spellcheck index: "+e.getMessage());
			return false;
		}
	}
	
	public boolean updateSimilar(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		if(!iid.hasTitleNgram())
			return true;
		try{
			TitleNgramIndexer indexer = TitleNgramIndexer.openForBatchModification(iid);
			indexer.batchUpdate(updateRecords);
			return true;
		} catch(IOException e){
			e.printStackTrace();
			log.error("Cannot update spellcheck index: "+e.getMessage());
			return false;
		}
	}
	
	public boolean updateTitles(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		if(iid.hasTitlesIndex())
			return updateDocumentsOn(iid.getTitlesIndex(),updateRecords,iid);
		return true;
	}
	
	/**
	 * Update all documents in the collection. If needed the request  
	 * is forwarded to a remote object (i.e. if the part of the split
	 * index is indexed by another host). 
	 * 
	 * @param iid
	 * @param updateRecords
	 * @param original
	 */
	protected boolean updateDocumentsOn(IndexId iid, Collection<IndexUpdateRecord> updateRecords, IndexId original){
		long now = System.currentTimeMillis();
		log.info("Starting update of "+updateRecords.size()+" records on "+iid+", started at "+now);
		boolean succ = true;
		if(iid.isFurtherSubdivided()){
			ArrayList<IndexId> subs = iid.getPhysicalIndexIds();
			HashMap<String,SimpleIndexModifier> mod = new HashMap<String,SimpleIndexModifier>();
			HashMap<String,Transaction> trans = new HashMap<String,Transaction>();
			// init
			for(IndexId part : subs){
				mod.put(part.toString(),new SimpleIndexModifier(part,part.getLangCode(),false,global.exactCaseIndex(part.getDBname())));
				Transaction t = new Transaction(part,IndexId.Transaction.INDEX);
				t.begin();
				trans.put(part.toString(),t);				
			}
			
			// delete all first
			for(IndexId part : subs){
				mod.get(part.toString()).deleteDocuments(updateRecords);
			}
			ArrayList<ArrayList<IndexUpdateRecord>> split = new ArrayList<ArrayList<IndexUpdateRecord>>();
			for(int i=0;i<subs.size();i++)
				split.add(new ArrayList<IndexUpdateRecord>());
			// split
			for(IndexUpdateRecord rec : updateRecords){
				int part = (int)(Math.random()*subs.size());
				split.get(part).add(rec);
			}
			// add
			for(int i=0;i<subs.size();i++){
				SimpleIndexModifier modifier = mod.get(subs.get(i).toString());
				modifier.addDocuments(split.get(i));
			}
			// commit
			for(IndexId part : subs){
				trans.get(part.toString()).commit();
				modifiedDBs.add(part);
			}
			
		} else{
			// normal index
			SimpleIndexModifier modifier = new SimpleIndexModifier(iid,iid.getLangCode(),false,original.isExactCase());
			
			Transaction trans = new Transaction(iid,IndexId.Transaction.INDEX);
			trans.begin();
			boolean succDel = modifier.deleteDocuments(updateRecords);
			boolean succAdd = modifier.addDocuments(updateRecords);
			succ = succAdd; // it's OK if articles cannot be deleted
			trans.commit();
						
			modifiedDBs.add(iid);		
		}
		long delta = System.currentTimeMillis()-now;
		if(succ)
			log.info("Successful update ["+(int)((double)updateRecords.size()/delta*1000)+" articles/s] of "+updateRecords.size()+" records in "+delta+"ms on "+iid);
		else
			log.warn("Failed update of "+updateRecords.size()+" records in "+delta+"ms on "+iid);
		return succ;
	}

	/** Get modified indexes */
	public synchronized static HashSet<IndexId> getModifiedIndexes(){
		HashSet<IndexId> retVal = modifiedDBs;
		modifiedDBs = new HashSet<IndexId>();
		return retVal;
	}

	/**
	 * Make a document for a regular index
	 * @throws IOException
	 */
	public static Document makeDocument(Article article, FieldBuilder builder, IndexId iid, HashSet<String> stopWords, Analyzer analyzer) throws IOException{
		return makeDocument(article,builder,iid,stopWords,analyzer,false);
	}	
	
	/** Make a document with optional extra info (spellcheck context words) */
	public static Document makeDocument(Article article, FieldBuilder builder, IndexId iid, HashSet<String> stopWords, Analyzer analyzer, boolean extraInfo) throws IOException{
		Document doc = new Document();
		
		// tranform record so that unnecessary stuff is deleted, e.g. some redirects
		transformArticleForIndexing(article);
				
		// page_id from database, used to look up and replace entries on index updates
		doc.add(new Field("key", article.getIndexKey(), Field.Store.YES, Field.Index.UN_TOKENIZED));

		// namespace, returned with results
		doc.add(new Field("namespace", article.getNamespace(), Field.Store.YES, Field.Index.UN_TOKENIZED));
				
		// raw rank value
		doc.add(new Field("rank",Integer.toString(article.getRank()),
				Field.Store.YES, Field.Index.NO));
		
		// redirect namespace
		if(article.isRedirect()){
			doc.add(new Field("redirect_namespace",Integer.toString(article.getRedirectTargetNamespace()),
					Field.Store.NO, Field.Index.UN_TOKENIZED));
		}

		// related
		makeRelated(doc,"related",article,iid,stopWords,analyzer);
		
		// date
		SimpleDateFormat isoDate = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'");
		isoDate.setTimeZone(TimeZone.getTimeZone("GMT"));
		doc.add(new Field("date",isoDate.format(article.getDate()),Store.YES,Index.NO));
		
		float rankBoost = transformRank(article.getRank());

		// add both title and redirects to content, so queries that match part of title and content won't fail
		String contents = article.getContents();
		contents = article.getTitle()+". "+contents;
		
		/** Following fields can be optionally case-dependent */  
		for(FieldBuilder.BuilderSet bs : builder.getBuilders()){
			FieldNameFactory fields = bs.getFields();
			FilterFactory filters = bs.getFilters();
			
			String anchoredContents = contents +"\n\n"+serializeAnchors(article.getAnchorRank(),bs.isExactCase());
			
			// tokenize the article to fill in pre-analyzed fields
			TokenizerOptions options = new TokenizerOptions(bs.isExactCase());
			if(filters.isSpellCheck())
				options = new TokenizerOptions.SpellCheck();
			WikiTokenizer tokenizer = new WikiTokenizer(anchoredContents,iid,options);
			tokenizer.tokenize();
			
			// title
			Field title = new Field(fields.title(), article.getTitle(),Field.Store.YES, Field.Index.TOKENIZED);				 
			title.setBoost(rankBoost);
			doc.add(title);
			
			// contents 
			doc.add(new Field(fields.contents(),
					new LanguageAnalyzer(filters,tokenizer).tokenStream(fields.contents(),"")));
			
			if(bs.getFilters().hasStemmer()){
				// stemtitle
				Field stemtitle = new Field(fields.stemtitle(), article.getTitle(),Field.Store.NO, Field.Index.TOKENIZED);				
				stemtitle.setBoost(rankBoost);
				doc.add(stemtitle);
			}

			// altititle
			makeAlttitle(doc,fields.alttitle(),article,iid,stopWords,tokenizer,analyzer,fields.isExactCase());
			
			// sections
			makeSections(doc,fields.sections(),article,iid,stopWords,tokenizer,analyzer,transformRankLog(article.getRank()));

			// category
			if(!bs.isExactCase()){
				// each token is one category (category names themself are not tokenized)
				doc.add(new Field("category", new CategoryAnalyzer(tokenizer.getCategories(),false).tokenStream("category","")));
			}
			
			// reverse title for wildcard searches
			Field rtitle = new Field(fields.reverse_title(), StringUtils.reverseString(article.getTitle()), Field.Store.NO, Field.Index.TOKENIZED);				 
			rtitle.setBoost(rankBoost);
			doc.add(rtitle);
			
			// extra info (for spellcheck indexes)
			if(extraInfo){
				addSpellCheckInfo(doc,article.getTitle(),tokenizer.getKeywords(),tokenizer.getHeadingText(),article.getRedirectKeywords(),iid,fields);
				if(article.isRedirect())
					doc.add(new Field("redirect",article.getRedirectTarget(),Store.YES,Index.NO));
			}

		}
		return doc;
	}

	/** Serialize redirects that will be added to end of the article */
	private static String serializeRedirects(ArrayList<String> redirectKeywords) {
		if(redirectKeywords.size()==0)
			return "";
		StringBuilder sb = new StringBuilder();
		for(String s : redirectKeywords){
			sb.append(s);
			sb.append(". ");
		}
		return sb.toString();
	}
	
	/** Serialize anchors (which includes titles and redirects) to be added to end of contents */
	public static String serializeAnchors(Map<String,Integer> anchors, boolean exactCase){
		if(!exactCase){
			anchors = Links.lowercaseAnchorMap(anchors);
		}
		// sort & add
		ArrayList<Entry<String,Integer>> sorted = Links.sortAnchors(anchors); 
		StringBuilder sb = new StringBuilder();
		for(Entry<String,Integer> e : sorted){
			sb.append(e.getKey());
			sb.append("\n\n"); // paragraph gap
		}
		return sb.toString();
	}
	
	/** Make the document that will be indexed as highlighting data */
	public static Document makeHighlightDocument(Article article, FieldBuilder builder, IndexId iid) throws IOException{
		WikiIndexModifier.transformArticleForIndexing(article);
		String key = article.getTitleObject().getKey();
		SimpleDateFormat isoDate = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'");
		isoDate.setTimeZone(TimeZone.getTimeZone("GMT"));
		Document doc = new Document();
		doc.add(new Field("pageid",article.getPageIdStr(),Store.NO,Index.UN_TOKENIZED));
		doc.add(new Field("key",key,Store.NO,Index.UN_TOKENIZED));
		for(FieldBuilder.BuilderSet bs : builder.getBuilders()){
			FieldNameFactory fields = bs.getFields();
			FilterFactory filters = bs.getFilters();
			boolean exactCase = bs.isExactCase();
			
			Analyzer analyzer = Analyzers.getHighlightAnalyzer(filters,fields,exactCase);
			ReusableLanguageAnalyzer contentAnalyzer = Analyzers.getReusableHighlightAnalyzer(filters,exactCase);
			doc.add(new Field(fields.hl_text(),ExtToken.serialize(contentAnalyzer.tokenStream(fields.contents(),article.getContents())),Store.COMPRESS));
			ArrayList<String> sections = contentAnalyzer.getWikiTokenizer().getHeadingText();
			doc.add(new Field(fields.hl_alttitle(),Alttitles.serializeAltTitle(article,iid,sections,analyzer,fields.alttitle()),Store.COMPRESS));
		}
		doc.add(new Field("date",isoDate.format(article.getDate()),Store.YES,Index.NO));
		doc.add(new Field("size",Long.toString(article.getContents().getBytes().length),Store.YES,Index.NO));
		return doc;
	}
	
	/** Make the document that holds only title data */
	public static Document makeTitleDocument(Article article, Analyzer analyzer, Analyzer highlightAnalyzer, IndexId titles, String suffix, String dbname, boolean exactCase, HashSet<String> stopWords) throws IOException{
		transformArticleForIndexing(article);
		
		String key = article.getTitleObject().getKey();
		float rankBoost = transformRank(article.getRank());
		Document doc = new Document();
		log.debug("Adding interwiki title pageid="+suffix+":"+article.getPageIdStr()+", key="+suffix+":"+key);
		doc.add(new Field("pageid",suffix+":"+article.getPageIdStr(),Store.NO,Index.UN_TOKENIZED));
		doc.add(new Field("key",suffix+":"+key,Store.NO,Index.UN_TOKENIZED));
		doc.add(new Field("suffix",suffix,Store.YES,Index.UN_TOKENIZED));
		doc.add(new Field("dbname",dbname,Store.NO,Index.UN_TOKENIZED));
		doc.add(new Field("namespace",article.getNamespace(),Store.YES,Index.UN_TOKENIZED));
		// redirect namespace
		if(article.isRedirect()){
			doc.add(new Field("redirect_namespace",Integer.toString(article.getRedirectTargetNamespace()),
					Field.Store.NO, Field.Index.UN_TOKENIZED));
		}
		Field title = new Field("title",article.getTitle(),Store.YES, Index.NO);
		title.setBoost(rankBoost);
		doc.add(title);
		// add titles + redirects in aggregate field
		makeAlttitle(doc,"alttitle",article,titles,stopWords,null,analyzer,false);		
		// store highlight data
		doc.add(new Field("alttitle",Alttitles.serializeAltTitle(article,titles,new ArrayList<String>(),highlightAnalyzer,"alttitle"),Store.COMPRESS));
		
		return doc;
	}
	
	/** Add words from different sources as additional info to provide context for spellchecks 
	 * @throws IOException */
	protected static void addSpellCheckInfo(Document doc, String title, HashSet<String> keywords, ArrayList<String> headingText, 
			ArrayList<String> redirectKeywords, IndexId iid, FieldNameFactory fields) throws IOException {		
		HashSet<String> collected = new HashSet<String>();
		collected.add(title);
		collected.addAll(keywords);
		collected.addAll(headingText);
		// collected.addAll(redirectKeywords);
		StringBuilder sb = new StringBuilder();
		for(String s : collected){
			sb.append(s);
			sb.append(" ");
		}
		// get individual words
		TokenStream ts = Analyzers.getIndexerAnalyzer(new FieldBuilder(iid,false)).tokenStream("title",sb.toString());
		Token t;
		HashSet<String> tokenized = new HashSet<String>();
		while((t = ts.next()) != null){
			tokenized.add(t.termText());
		}
		
		doc.add(new Field(fields.spellcheck_context(), new StringList(tokenized).toString(), Field.Store.COMPRESS, Field.Index.NO));
	}
	
	/** add related aggregate field 
	 * @throws IOException */
	protected static void makeRelated(Document doc, String prefix, Article article, IndexId iid, HashSet<String> stopWords, Analyzer analyzer) throws IOException{
		ArrayList<Aggregate> items = new ArrayList<Aggregate>();
		for(RelatedTitle rt : article.getRelated()){
			addToItems(items,new Aggregate(rt.getRelated().getTitle(),transformRelated(rt.getScore()),iid,analyzer,prefix,stopWords,Flags.RELATED),stopWords);
		}
		makeAggregate(doc,prefix,items);
	}
	
	/** add alttitle aggregate field */
	protected static void makeAlttitle(Document doc, String prefix, Article article, IndexId iid, HashSet<String> stopWords, 
			WikiTokenizer tokenizer, Analyzer analyzer, boolean exactCase) throws IOException {
		ArrayList<Aggregate> items = new ArrayList<Aggregate>();
		HashSet<String> titles = new HashSet<String>();
		titles.add(article.getTitle());
		titles.addAll(article.getRedirectKeywords());
		// get anchors
		Map<String,Integer> anchors = article.getAnchorRank();
		if(!exactCase){
			titles = lowercaseSet(titles);
			anchors = Links.lowercaseAnchorMap(anchors);
		}
		// sort & add
		ArrayList<Entry<String,Integer>> sorted = Links.sortAnchors(anchors); 
		for(Entry<String,Integer> e : sorted){
			Flags flag = titles.contains(e.getKey())? Flags.ALTTITLE :  Flags.ANCHOR;  
			addToItems(items, new Aggregate(e.getKey(),transformRankLog(e.getValue()),iid,analyzer,prefix,stopWords,flag),stopWords);
		}
		makeAggregate(doc,prefix,items);
	}	
	
	private static HashSet<String> lowercaseSet(HashSet<String> set){
		HashSet<String> ret = new HashSet<String>();
		for(String s : set)
			ret.add(s.toLowerCase());
		return ret;
	}
	
	/** Section heading aggregate field */
	protected static void makeSections(Document doc, String prefix, Article article, IndexId iid, HashSet<String> stopWords, 
			WikiTokenizer tokenizer, Analyzer analyzer, float boost) throws IOException{
		if(tokenizer != null){
			ArrayList<Aggregate> items = new ArrayList<Aggregate>();
			// add section headings!
			for(String h : tokenizer.getHeadingText()){			
				addToItems(items, new Aggregate(h,boost,iid,analyzer,prefix,stopWords,Flags.SECTION),stopWords);
			}
			makeAggregate(doc,prefix,items);
		}
	}
	
	
	private static void addToItems(ArrayList<Aggregate> items, Aggregate a, HashSet<String> stopWords){
		if(a.length() != 0){
			ArrayList<Token> tokens = a.getTokens();
			if(tokens.size()==2 && tokens.get(1).type().equals("singular")){
				tokens.remove(1); // we don't want singulars for single-token titles or redirects
				a.setTokens(tokens,stopWords);
			}
			items.add(a);
		}
	}
	
	protected static void makeAggregate(Document doc, String prefix, ArrayList<Aggregate> items){
		if(items.size() == 0)
			return; // don't add aggregate fields if they are empty
		doc.add(new Field(prefix,new AggregateAnalyzer(items).tokenStream(prefix,"")));
		doc.add(new Field(prefix+"_meta",Aggregate.serializeAggregate(items),Field.Store.YES));
	}
	
	protected static float transformRelated(double score){
		return (float)Math.log10(1+score);
	}

	/** 
	 * 
	 * Calculate document boost (article rank) from number of
	 * pages that link this page.
	 * 
	 * @param rank
	 * @return
	 */
	public static float transformRank(int rank){
		if(rank == 0)
			return 1;
		else 
			return (float) (1 + rank/15.0);
	}
	
	public static float transformRankLog(int rank){
		return 1+(float)Math.log(transformRank(rank));
	}

	/**
	 * Boost factor for contents 
	 * 
	 * @param rank
	 * @return
	 */
	public static float contentsBoost(int rank) {
		if(rank == 0)
			return 1;
		else
			return (float) (Math.log(Math.E + rank/15.0) / 10.0);
	}

	
}
