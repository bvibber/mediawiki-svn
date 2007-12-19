/*
 * Created on Feb 11, 2007
 *
 */
package org.wikimedia.lsearch.index;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Set;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.PerFieldAnalyzerWrapper;
import org.apache.lucene.analysis.SimpleAnalyzer;
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
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.beans.Redirect;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.spell.api.SpellCheckIndexer;
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

	/** Simple implementation of batch addition and deletion */
	class SimpleIndexModifier {
		protected IndexId iid;
		protected IndexReader reader;
		protected IndexWriter writer;
		protected boolean rewrite;		
		protected String langCode;
		protected boolean exactCase;
		protected IndexId original;
		
		protected HashSet<IndexUpdateRecord> nonDeleteDocuments;
		
		/** All reports to be sent back to main indexer host */
		Hashtable<IndexUpdateRecord,IndexReportCard> reportQueue;
				
		// TODO : synchronize multiple threads
		
		/**
		 * SimpleIndexModifier
		 * 
		 * @param iid 
		 * @param indexAnalyzer
		 * @param rewrite - if true, will create new index
		 * @param original - the source iid (for titles indexes, e.g. for en-titles.tspart1 it could be enwiki) 
		 */
		SimpleIndexModifier(IndexId iid, String langCode, boolean rewrite, boolean exactCase, IndexId original){
			this.iid = iid;
			this.rewrite = rewrite;
			this.langCode = langCode;
			this.exactCase = exactCase;
			this.original = original;
			reportQueue = new Hashtable<IndexUpdateRecord,IndexReportCard>();
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
		
		/** Batch-delete documents, returns true if successfull */
		boolean deleteDocuments(Collection<IndexUpdateRecord> records){
			nonDeleteDocuments = new HashSet<IndexUpdateRecord>();
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
					if(rec.doDelete()){
						int count = 0;
						if(iid.isHighlight())
							count = reader.deleteDocuments(new Term("key", rec.getHighlightKey()));
						else // normal or titles index
							count = reader.deleteDocuments(new Term("key", rec.getKey()));
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
				return false;
			}
			return true;
		}
		
		/** Batch-add documents, returns true if successfull */
		boolean addDocuments(Collection<IndexUpdateRecord> records){
			boolean succ = true;
			String path = iid.getIndexPath();
			try{
				openForWrite(path,rewrite);
			} catch(IOException e){
				return false;
			}
			
			writer.setSimilarity(new WikiSimilarity());
			int mergeFactor = iid.getIntParam("mergeFactor",10);
			int maxBufDocs = iid.getIntParam("maxBufDocs",10);
			writer.setMergeFactor(mergeFactor);
			writer.setMaxBufferedDocs(maxBufDocs);
			writer.setUseCompoundFile(true);
			writer.setMaxFieldLength(MAX_FIELD_LENGTH);
			FieldBuilder.Case dCase = (exactCase)? FieldBuilder.Case.EXACT_CASE : FieldBuilder.Case.IGNORE_CASE; 
			FieldBuilder builder = new FieldBuilder(iid,dCase);
			Analyzer analyzer = null;
			ReusableLanguageAnalyzer highlightContentAnalyzer = null;
			if(iid.isHighlight()){
				highlightContentAnalyzer = Analyzers.getReusableHighlightAnalyzer(builder.getBuilder(dCase).getFilters());
				analyzer = Analyzers.getHighlightAnalyzer(iid); 			
			} else
				analyzer = Analyzers.getIndexerAnalyzer(new FieldBuilder(iid,FieldBuilder.Case.EXACT_CASE));
			
			HashSet<String> stopWords = StopWords.getPredefinedSet(iid);
			for(IndexUpdateRecord rec : records){								
				if(rec.doAdd()){
					if(!rec.isAlwaysAdd() && nonDeleteDocuments.contains(rec))
						continue; // don't add if delete/add are paired operations
					if(!checkPreconditions(rec))
						continue; // article shouldn't be added for some reason					
					IndexReportCard card = getReportCard(rec);
					Document doc;					
					try {
						if(iid.isHighlight())
							doc = makeHighlightDocument(rec.getArticle(),analyzer,highlightContentAnalyzer,iid);
						else if(iid.isTitlesBySuffix())
							doc = makeTitleDocument(rec.getArticle(),analyzer,iid,original.getTitlesSuffix(),exactCase);
						else // normal index
							doc = makeDocument(rec.getArticle(),builder,iid,stopWords,analyzer);
						
						writer.addDocument(doc,analyzer);
						log.debug(iid+": Adding document "+rec.getKey()+" "+rec.getArticle());
						if(card != null)
							card.setSuccessfulAdd();
					} catch (IOException e) {
						log.error("Error writing  document "+rec+" to index "+path);
						if(card != null)
							card.setFailedAdd();
						succ = false; // report unsucc, but still continue, to process all cards 
					} catch(Exception e){
						e.printStackTrace();
						log.error("Error adding document "+rec.getKey()+" with message: "+e.getMessage());
						if(card != null)
							card.setFailedAdd();
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
			return checkAddPreconditions(rec.getArticle(),langCode);
		}
	}
	
	public static IndexWriter openForWrite(String path, boolean rewrite) throws IOException{
		try {
			return new IndexWriter(path,null,rewrite);
		} catch (IOException e) {				
			try {
				// unlock, retry
				if(!new File(path).exists()){
					// try to make brand new index
					makeDBPath(path); // ensure all directories are made
					log.info("Making new index at path "+path);
					return new IndexWriter(path,null,true);
				} else if(IndexReader.isLocked(path)){
					unlockIndex(path);
					return new IndexWriter(path,null,rewrite); 					
				} else
					throw e;
			} catch (IOException e1) {
				e1.printStackTrace();
				log.error("I/O error openning index for addition of documents at "+path+" : "+e.getMessage());
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
	public static boolean checkAddPreconditions(Article ar, String langCode){
		Title redirect = Localization.getRedirectTitle(ar.getContents(),langCode);
		int ns = Integer.parseInt(ar.getNamespace());
		if(redirect!=null && redirect.getNamespace() == ns){
			return false; // don't add redirects to same namespace, always add as redirect field
		} 
		
			/*if(ar.getNamespace().equals("0")){
			  if(redirect != null && redirect.toLowerCase().equals(ar.getTitle().toLowerCase())){
				log.debug("Not adding "+ar+" into index: "+ar.getContents());
				return false;
			} */
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
			// index only redirects from the same namespace
			// to avoid a lot of unusable redirects from/to
			// user namespace, but always index redirect FROM main
			for(Redirect r : redirects){
				if(ns == r.getNamespace() || (r.getNamespace() == 0 && ns != 0)){
					filtered.add(r.getTitle());
					ranks.add(r.getReferences());
					ar.addToRank(r.getReferences()+1);
				} else
					log.debug("Ignoring redirect "+r+" to "+ar);
			}
			ar.setRedirectKeywords(filtered);
			ar.setRedirectKeywordRanks(ranks);
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
	
	/** Check if atitle should be added to the titles index */
	public static boolean checkTitlePreconditions(Article article, IndexId iid){
		if(global == null)
			global = GlobalConfiguration.getInstance();
		NamespaceFilter defaultNamespaces = global.getDefaultNamespace(iid);
		return defaultNamespaces.contains(article.getTitleObject().getNamespace());
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
	 * Update both the search and highlight index for iid.
	 *  
	 * @param iid
	 * @param updateRecords
	 */
	public boolean updateDocuments(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		boolean index = updateDocumentsOn(iid,updateRecords,iid);
		boolean highlight = updateDocumentsOn(iid.getHighlight(),updateRecords,iid);
		boolean titles = true;
		if(iid.hasTitlesIndex())
			titles = updateDocumentsOn(iid.getTitlesIndex(),updateRecords,iid);
		return index && highlight && titles;
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
				mod.put(part.toString(),new SimpleIndexModifier(part,part.getLangCode(),false,global.exactCaseIndex(part.getDBname()),original));
				Transaction t = new Transaction(part);
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
				SimpleIndexModifier modifier = mod.get(subs.get(i));
				modifier.addDocuments(split.get(i));
			}
			// commit
			for(IndexId part : subs){
				trans.get(part.toString()).commit();
				modifiedDBs.add(part);
			}
			
		} else{
			// normal index
			SimpleIndexModifier modifier = new SimpleIndexModifier(iid,iid.getLangCode(),false,global.exactCaseIndex(original.getDBname()),original);
			
			Transaction trans = new Transaction(iid);
			trans.begin();
			boolean succDel = modifier.deleteDocuments(updateRecords);
			boolean succAdd = modifier.addDocuments(updateRecords);
			succ = succAdd; // it's OK if articles cannot be deleted
			trans.commit();
			
			// send reports back to the main indexer host
			RMIMessengerClient messenger = new RMIMessengerClient();
			if(modifier.reportQueue.size() != 0)
				messenger.sendReports(modifier.reportQueue.values().toArray(new IndexReportCard[] {}),
						IndexId.get(iid.getDBname()).getIndexHost());
			
			modifiedDBs.add(iid);		
		}
		long delta = System.currentTimeMillis()-now;
		if(succ)
			log.info("Successful update ["+(int)((double)updateRecords.size()/delta*1000)+" articles/s] of "+updateRecords.size()+" records in "+delta+"ms on "+iid);
		else
			log.warn("Failed update of "+updateRecords.size()+" records in "+delta+"ms on "+iid);
		return succ;
	}

	/** Close all IndexModifier instances */
	public synchronized static HashSet<IndexId> closeAllModifiers(){
		HashSet<IndexId> retVal = modifiedDBs;
		modifiedDBs = new HashSet<IndexId>();
		return retVal;
	}

	/**
	 * Make a document and a corresponding analyzer
	 * @param rec
	 * @param languageAnalyzer
	 * @return array { document, analyzer }
	 * @throws IOException 
	 */
	public static Document makeDocument(Article article, FieldBuilder builder, IndexId iid, HashSet<String> stopWords, Analyzer analyzer) throws IOException{
		Document doc = new Document();

		// tranform record so that unnecessary stuff is deleted, e.g. some redirects
		transformArticleForIndexing(article);
				
		// page_id from database, used to look up and replace entries on index updates
		doc.add(new Field("key", article.getKey(), Field.Store.YES, Field.Index.UN_TOKENIZED));

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
		
		float rankBoost = transformRank(article.getRank());
		
		/** Following fields can be optionally case-dependent */  
		for(FieldBuilder.BuilderSet bs : builder.getBuilders()){
			FieldNameFactory fields = bs.getFields();
			FilterFactory filters = bs.getFilters();
			
			// tokenize the article to fill in pre-analyzed fields
			WikiTokenizer tokenizer = new WikiTokenizer(article.getContents(),iid,new TokenizerOptions(bs.isExactCase()));
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
			makeAlttitle(doc,fields.alttitle(),article,iid,stopWords,bs.isExactCase(),tokenizer,rankBoost,analyzer);

			// category
			if(!bs.isExactCase()){
				// each token is one category (category names themself are not tokenized)
				doc.add(new Field("category", new CategoryAnalyzer(tokenizer.getCategories(),false).tokenStream("category","")));
			}
			
			// reverse title for wildcard searches
			Field rtitle = new Field(fields.reverse_title(), StringUtils.reverseString(article.getTitle()), Field.Store.NO, Field.Index.TOKENIZED);				 
			rtitle.setBoost(rankBoost);
			doc.add(rtitle);

		}
		return doc;
	}
	
	/** Make the document that will be indexed as highlighting data */
	public static Document makeHighlightDocument(Article article, Analyzer analyzer, ReusableLanguageAnalyzer contentAnalyzer, IndexId iid) throws IOException{
		String key = article.getTitleObject().getKey();
		Document doc = new Document();
		doc.add(new Field("key",key,Store.NO,Index.UN_TOKENIZED));
		doc.add(new Field("text",ExtToken.serialize(contentAnalyzer.tokenStream("contents",article.getContents())),Store.COMPRESS));
		ArrayList<String> sections = contentAnalyzer.getWikiTokenizer().getHeadingText();
		doc.add(new Field("alttitle",Alttitles.serializeAltTitle(article,iid,sections,analyzer,"alttitle"),Store.COMPRESS));
		return doc;
	}
	
	/** Make the document that holds only title data */
	public static Document makeTitleDocument(Article article, Analyzer analyzer, IndexId titles, String suffix, boolean exactCase) throws IOException{
		String key = article.getKey();
		float rankBoost = transformRank(article.getRank());
		Document doc = new Document();
		doc.add(new Field("key",suffix+key,Store.NO,Index.UN_TOKENIZED));
		doc.add(new Field("suffix",suffix,Store.YES,Index.UN_TOKENIZED));
		doc.add(new Field("namespace",article.getNamespace(),Store.YES,Index.UN_TOKENIZED));
		if(exactCase){
			// title will always hold the original stored title
			doc.add(new Field("title",article.getTitle(),Store.YES, Index.NO));
			Field title = new Field("title_exact",article.getTitle(),Store.NO, Index.TOKENIZED);
			title.setBoost(rankBoost);
			doc.add(title);
		} else{
			Field title = new Field("title",article.getTitle(),Store.YES, Index.TOKENIZED);
			title.setBoost(rankBoost);
			doc.add(title);
		}
		return doc;
	}
	
	/** add related aggregate field 
	 * @throws IOException */
	protected static void makeRelated(Document doc, String prefix, Article article, IndexId iid, HashSet<String> stopWords, Analyzer analyzer) throws IOException{
		ArrayList<Aggregate> items = new ArrayList<Aggregate>();
		for(RelatedTitle rt : article.getRelated()){
			addToItems(items,new Aggregate(rt.getRelated().getTitle(),transformRelated(rt.getScore()),iid,analyzer,prefix,stopWords));
		}
		makeAggregate(doc,prefix,items);
	}
	
	/** add alttitle aggregate field */
	protected static void makeAlttitle(Document doc, String prefix, Article article, IndexId iid, HashSet<String> stopWords, 
			boolean exactCase, WikiTokenizer tokenizer, float rankBoost, Analyzer analyzer) throws IOException {
		ArrayList<Aggregate> items = new ArrayList<Aggregate>();
		// add title
		String title = article.getTitle();		
		addToItems(items, new Aggregate(title,transformRank(article.getRank()),iid,analyzer,prefix,stopWords));
		// add all redirects
		ArrayList<String> redirects = article.getRedirectKeywords();
		ArrayList<Integer> ranks = article.getRedirectKeywordRanks();
		for(int i=0;i<redirects.size();i++){
			addToItems(items, new Aggregate(redirects.get(i),transformRank(ranks.get(i)),iid,analyzer,prefix,stopWords));
		}
		// add section headings!
		for(String h : tokenizer.getHeadingText()){			
			addToItems(items, new Aggregate(title+" "+h,rankBoost*HEADINGS_BOOST,iid,analyzer,prefix,stopWords));
		}
		makeAggregate(doc,prefix,items);
	}	
	
	
	private static void addToItems(ArrayList<Aggregate> items, Aggregate a){
		if(a.length() != 0)
			items.add(a);
	}
	
	protected static void makeAggregate(Document doc, String prefix, ArrayList<Aggregate> items){
		if(items.size() == 0)
			return; // don't add aggregate fields if they are empty
		doc.add(new Field(prefix,new AggregateAnalyzer(items).tokenStream(prefix,"")));
		doc.add(new Field(prefix+"_meta",AggregateAnalyzer.generateMetaField(items),Field.Store.YES));
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
