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
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Set;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.PerFieldAnalyzerWrapper;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;
import org.wikimedia.lsearch.analyzers.Aggregate;
import org.wikimedia.lsearch.analyzers.AggregateAnalyzer;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.ContextAnalyzer;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.analyzers.KeywordsAnalyzer;
import org.wikimedia.lsearch.analyzers.RelatedAnalyzer;
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
import org.wikimedia.lsearch.spell.api.SpellCheckIndexer;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.MathFunc;

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

	/** Simple implementation of batch addition and deletion */
	class SimpleIndexModifier {
		protected IndexId iid;
		protected IndexReader reader;
		protected IndexWriter writer;
		protected boolean rewrite;		
		protected String langCode;
		protected boolean exactCase;
		
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
		SimpleIndexModifier(IndexId iid, String langCode, boolean rewrite, boolean exactCase){
			this.iid = iid;
			this.rewrite = rewrite;
			this.langCode = langCode;
			this.exactCase = exactCase;
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
			// TODO: fixme
			Links links = null;
			try {
				links = Links.openForRead(iid,iid.getImportPath());
			} catch (IOException e1) {
				// TODO Auto-generated catch block
				e1.printStackTrace();
			}

			for(IndexUpdateRecord rec : records){								
				if(rec.doAdd()){
					if(!rec.isAlwaysAdd() && nonDeleteDocuments.contains(rec))
						continue; // don't add if delete/add are paired operations
					if(!checkPreconditions(rec))
						continue; // article shouldn't be added for some reason					
					IndexReportCard card = getReportCard(rec);
					Object[] ret = makeDocumentAndAnalyzer(rec.getArticle(),builder,iid,links);
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
	protected static void transformArticleForIndexing(Article ar) {
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
	 * Update all documents in the collection. If needed the request  
	 * is forwarded to a remote object (i.e. if the part of the split
	 * index is indexed by another host). 
	 * 
	 * @param iid
	 * @param updateRecords
	 */
	public boolean updateDocuments(IndexId iid, Collection<IndexUpdateRecord> updateRecords){
		long now = System.currentTimeMillis();
		log.info("Starting update of "+updateRecords.size()+" records on "+iid+", started at "+now);
			
		SimpleIndexModifier modifier = new SimpleIndexModifier(iid,global.getLanguage(iid.getDBname()),false,global.exactCaseIndex(iid.getDBname()));
		
		Transaction trans = new Transaction(iid);
		trans.begin();
		boolean succDel = modifier.deleteDocuments(updateRecords);
		boolean succAdd = modifier.addDocuments(updateRecords);
		boolean succ = succAdd; // it's OK if articles cannot be deleted
		trans.commit();
		
		// send reports back to the main indexer host
		RMIMessengerClient messenger = new RMIMessengerClient();
		if(modifier.reportQueue.size() != 0)
			messenger.sendReports(modifier.reportQueue.values().toArray(new IndexReportCard[] {}),
					IndexId.get(iid.getDBname()).getIndexHost());
		
		modifiedDBs.add(iid);
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
	 */
	public static Object[] makeDocumentAndAnalyzer(Article article, FieldBuilder builder, IndexId iid, Links links){
		PerFieldAnalyzerWrapper perFieldAnalyzer = null;
		Document doc = new Document();

		// tranform record so that unnecessary stuff is deleted, e.g. some redirects
		transformArticleForIndexing(article);
		
		// This will be used to look up and replace entries on index updates.
		doc.add(new Field("key", article.getKey(), Field.Store.YES, Field.Index.UN_TOKENIZED));

		// These fields are returned with results
		doc.add(new Field("namespace", article.getNamespace(), Field.Store.YES, Field.Index.UN_TOKENIZED));
		
		// each token is one category (category names themself are not tokenized)
		doc.add(new Field("category", "", 
				Field.Store.NO, Field.Index.TOKENIZED));
		
		// interwiki associated with this page
		//doc.add(new Field("interwiki", "", 
		//    Field.Store.NO, Field.Index.TOKENIZED));
		
		doc.add(new Field("rank",Integer.toString(article.getRank()),
				Field.Store.YES, Field.Index.NO));
		
		// related partition
		int[] p = null;
		
		if(article.isRedirect()){
			doc.add(new Field("redirect_namespace",Integer.toString(article.getRedirectTargetNamespace()),
					Field.Store.NO, Field.Index.UN_TOKENIZED));
		}
		
		// related articles
		ArrayList<Aggregate> relatedAg = makeRelated(doc,"related",article,iid);
		
		for(FieldBuilder.BuilderSet bs : builder.getBuilders()){
			FieldNameFactory fields = bs.getFields();
			
			// boost document title with it's article rank
			Field title = new Field(fields.title(), article.getTitle(),Field.Store.YES, Field.Index.TOKENIZED);				
			//log.info(article.getNamespace()+":"+article.getTitle()+" has rank "+article.getRank()+" and redirect: "+((article.getRedirects()==null)? "" : article.getRedirects().size()));
			float rankBoost = calculateArticleRank(article.getRank()); 
			title.setBoost(rankBoost);
			doc.add(title);

			if(bs.getFilters().hasStemmer()){
				Field stemtitle = new Field(fields.stemtitle(), article.getTitle(),Field.Store.NO, Field.Index.TOKENIZED);				
				//log.info(article.getNamespace()+":"+article.getTitle()+" has rank "+article.getRank()+" and redirect: "+((article.getRedirects()==null)? "" : article.getRedirects().size()));
				stemtitle.setBoost(rankBoost);
				doc.add(stemtitle);
			}

			// put the best redirects as alternative titles
			makeAltTitles(doc,fields.alttitle(),article);

			// most significant words in the text, gets extra score, from analyzer
			bs.setAddKeywords(checkKeywordPreconditions(article,iid));		
			makeKeywordField(doc,fields.keyword(),rankBoost);

			// contents - generated using wikitokenizer
			Field contents = new Field(fields.contents(), "", 
					Field.Store.NO, Field.Index.TOKENIZED); 
			doc.add(contents);
			
			
			
			//makeContextField(doc,fields.context(),fields.related());
			
			// anchors
			// makeKeywordField(doc,fields.anchor(),rankBoost);
			
			// add the whole title for extract boost
			String wt = FastWikiTokenizerEngine.stipTitle(article.getTitle());
			if(!bs.isExactCase())
				wt = wt.toLowerCase();
			Field wtitle = new Field(fields.wholetitle(),wt,Field.Store.NO, Field.Index.UN_TOKENIZED);				
			wtitle.setBoost(rankBoost);
			doc.add(wtitle);

		}
		// make analyzer
		String text = article.getContents();
		Object[] ret = Analyzers.getIndexerAnalyzer(text,builder,article.getRedirectKeywords(),article.getAnchorText(),article.getRelated(),p,article.makeTitle(),links,relatedAg);
		perFieldAnalyzer = (PerFieldAnalyzerWrapper) ret[0];

		
		return new Object[] { doc, perFieldAnalyzer };
	}
	
	protected static ArrayList<Aggregate> makeRelated(Document doc, String prefix, Article article, IndexId iid){
		ArrayList<Aggregate> items = new ArrayList<Aggregate>();
		for(RelatedTitle rt : article.getRelated()){
			items.add(new Aggregate(rt.getRelated().getTitle(),transformRelated(rt.getScore()),iid,false));
		}
		if(items.size() == 0)
			return items; // don't add aggregate fields if they are empty
		doc.add(new Field(prefix,"",Field.Store.NO,Field.Index.TOKENIZED));
		doc.add(new Field(prefix+"_meta",AggregateAnalyzer.generateMetaField(items),Field.Store.COMPRESS,Field.Index.NO));
		return items;
	}
	
	protected static float transformRelated(double score){
		return (float)Math.log10(1+score);
	}

	/** Returns partioning of related titles, or null if there aren't any */
	protected static int[] makeRelatedOld(Document doc, String prefix, Article article, float boost, String context) {
		ArrayList<RelatedTitle> rel = article.getRelated();
		if(rel == null || rel.size()==0)
			return null;
		double[] scores = new double[rel.size()];
		for(int i=0;i<rel.size();i++)
			scores[i] = rel.get(i).getScore();
		
		// partition the sorted scores into groups
		int[] p = MathFunc.partitionList(scores,RelatedAnalyzer.RELATED_GROUPS);
		// don't add last related group
		for(int i=1;i<RelatedAnalyzer.RELATED_GROUPS;i++){
			Field relfield = new Field(prefix+i, "", 
					Field.Store.NO, Field.Index.TOKENIZED);
			float fb = boost*(float)MathFunc.avg(scores,p[i-1],p[i]);
			relfield.setBoost(fb);
			doc.add(relfield);
			/*if(i <= ContextAnalyzer.CONTEXT_GROUPS){
				Field confield = new Field(context+i, "", 
						Field.Store.NO, Field.Index.TOKENIZED);
				confield.setBoost(fb); // use same boost as related field
				doc.add(confield);
			}  */
		}
		
		return p;
	}

	/** Make a multiple context field ...  */
	protected static void makeContextField(Document doc, String prefix, String related) {
		for(int i=1;i<=ContextAnalyzer.CONTEXT_GROUPS;i++){
			Field keyfield = new Field(prefix+i, "", 
					Field.Store.NO, Field.Index.TOKENIZED);
			keyfield.setBoost(doc.getField(related+i).getBoost()); // use same boost as related field
			doc.add(keyfield);
		}
		
	}

	/** Make a multiple keyword field, e.g. keyword1, keyword2, keyword3 ...  */
	protected static void makeKeywordField(Document doc, String prefix, float boost) {
		for(int i=1;i<=KeywordsAnalyzer.KEYWORD_LEVELS;i++){
			Field keyfield = new Field(prefix+i, "", 
					Field.Store.NO, Field.Index.TOKENIZED);
			keyfield.setBoost(boost);
			doc.add(keyfield);
		}
		
	}

	protected static void makeAltTitles(Document doc, String prefix, Article article) {
		// the redirects, rank list are sorted..
		final ArrayList<String> redirects = article.getRedirectKeywords();
		final ArrayList<Integer> ranks = article.getRedirectKeywordRanks();
		if(redirects.size() == 0)
			return;
		// add alternative titles alttitle1, alttitle2 ...
		for(int i=0;i<ALT_TITLES && i<redirects.size();i++){
			if(ranks.get(i) == 0)
				break; // we don't want redirects with zero links
			//log.info("For "+article+" alttitle"+(i+1)+" "+redirects.get(i)+" = "+ranks.get(i));
			Field alttitle = new Field(prefix+(i+1), redirects.get(i),Field.Store.NO, Field.Index.TOKENIZED);				
			alttitle.setBoost(calculateArticleRank(ranks.get(i)));
			doc.add(alttitle);			
		}
	}

	/** 
	 * 
	 * Calculate document boost (article rank) from number of
	 * pages that link this page.
	 * 
	 * @param rank
	 * @return
	 */
	public static float calculateArticleRank(int rank){
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
