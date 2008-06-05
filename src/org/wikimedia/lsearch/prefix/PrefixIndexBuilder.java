package org.wikimedia.lsearch.prefix;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.analyzers.LowercaseAnalyzer;
import org.wikimedia.lsearch.analyzers.PrefixAnalyzer;
import org.wikimedia.lsearch.analyzers.TokenizerOptions;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.index.Transaction;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.search.UpdateThread;
import org.wikimedia.lsearch.spell.api.LuceneDictionary;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.util.ProgressReport;

/**
 * Build an index of all title prefixes
 * 
 * @author rainman
 *
 */
public class PrefixIndexBuilder {
	static Logger log = Logger.getLogger(PrefixIndexBuilder.class);
	
	public static float EXACT_BOOST = 25;
	
	protected IndexId iid, prefixIid, pre;
	protected FilterFactory filters;
	protected Links links=null;
	protected IndexWriter writer=null;
	protected HashSet<Integer> namespaces = null;
	/** Builder for a new index based on standalone links snapshot */
	static public PrefixIndexBuilder newFromStandalone(IndexId iid) throws IOException{
		return new PrefixIndexBuilder(iid,Links.openStandalone(iid),null);
	}
	/** To rebuild the prefix index only (and not the precursor) */
	static public PrefixIndexBuilder newForPrefixOnly(IndexId iid) throws IOException{
		return new PrefixIndexBuilder(iid,null,null);
	}
	/** Builder for incremental updates to precursor index */
	static public PrefixIndexBuilder forPrecursorModification(IndexId iid) throws IOException{
		iid = iid.getPrefix();
		IndexWriter writer = WikiIndexModifier.openForWrite(iid.getPrecursor().getIndexPath(),false,new PrefixAnalyzer());
		initWriter(writer);
		return new PrefixIndexBuilder(iid,null,writer);
	}
	/** Batch modification of the index */
	static public PrefixIndexBuilder forPrecursorBatchModification(IndexId iid) throws IOException{
		iid = iid.getPrefix();
		return new PrefixIndexBuilder(iid,null,null);
	}
	
	private PrefixIndexBuilder(IndexId iid, Links links, IndexWriter writer) throws IOException {
		this.iid = iid;
		prefixIid = iid.getPrefix();
		pre = prefixIid.getPrecursor();
		filters = new FilterFactory(iid);
		this.links = links;
		this.writer = writer;
		this.namespaces = iid.getDefaultNamespace().getNamespaces();
	}
	
	protected static void initWriter(IndexWriter writer){
		if(writer != null){
			writer.setMergeFactor(20);
			writer.setMaxBufferedDocs(500);	
		}
	}
	
	public static void main(String[] args) throws IOException{
		int perPrefix = 15;
		boolean usetemp = false;
		String dbname = null;
		boolean useSnapshot = false;
		
		System.out.println("MediaWiki lucene-search indexer - rebuild prefix index used for ajax suggestions.");
		
		Configuration.open();		
		if(args.length == 0){
			System.out.println("Syntax: java PrefixIndexBuilder [-t] [-p <num>] <dbname>");
			System.out.println("Options:");
			System.out.println("   -p       - reuse temporary precursor index (import path)");
			System.out.println("   -s       - reuse latest temporary precursor snapshot");
			System.out.println("   -t <num> - titles per prefix (default: "+perPrefix+")");
			return;
		}
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-p"))
				usetemp = true;
			else if(args[i].equals("-t"))
				perPrefix = Integer.parseInt(args[++i]);
			else if(args[i].equals("-s")){
				usetemp = true;
				useSnapshot = true;
			} else if(args[i].startsWith("-")){
				System.out.println("Unrecognized option "+args[i]);
				return;
			} else
				dbname = args[i];
		}		
		
		IndexId iid = IndexId.get(dbname);
		PrefixIndexBuilder builder = usetemp? newForPrefixOnly(iid) : newFromStandalone(iid);
		IndexId pre = iid.getPrefix().getPrecursor();
		String precursorPath = pre.getImportPath();
		if(useSnapshot)
			precursorPath = IndexRegistry.getInstance().getLatestSnapshot(pre).path;
		
		builder.createNewFromLinks(perPrefix,usetemp,precursorPath);
	}

	/**
	 * Create a completely new prefix index in the import path, 
	 * optionally rebuilds the precursor as well
	 * 
	 * @param perPrefix
	 * @param useExistingPrecursor
	 * @throws IOException
	 */
	public void createNewFromLinks(int perPrefix, boolean useExistingPrecursor, String precursorPath) throws IOException{
		long start = System.currentTimeMillis();
		
		if(!useExistingPrecursor){
			writer = new IndexWriter(pre.getImportPath(),new PrefixAnalyzer(),true);
			writer.setMergeFactor(20);
			writer.setMaxBufferedDocs(500);
			log.info("Writing precursor index");
			LuceneDictionary dict = links.getKeys();
			dict.setProgressReport(new ProgressReport("titles",1000));
			Word w;
			// iterate over all documents in the index
			while((w = dict.next()) != null){
				String key = w.getWord();
				addToPrecursor(key);							
			}
			log.info("Optimizing precursor index");
			writer.optimize();
			writer.close();
		}
		
		rebuildPrefixIndex(precursorPath,perPrefix);
		
		long delta = System.currentTimeMillis() - start;
		System.out.println("Finished in "+ProgressReport.formatTime(delta));
	}
	
	/** Rebuild the prefix index using a precursor at path */
	public void rebuildPrefixIndex(String path, int perPrefix) throws IOException {
		log.info("Writing prefix index");
		IndexWriter writer = new IndexWriter(prefixIid.getImportPath(), new LowercaseAnalyzer(),true);
		writer.setMergeFactor(20);
		writer.setMaxBufferedDocs(1000);
		IndexReader ir = IndexReader.open(path);
		LuceneDictionary dict = new LuceneDictionary(ir,"key");
		dict.setProgressReport(new ProgressReport("prefixes",10000));
		Word w;
		while((w = dict.next()) != null){
			String prefix = w.getWord();
			int colon = prefix.indexOf(':'); 
			if(colon == -1 || colon==prefix.length()-1)
				continue; // empty prefixes like "0:"
			Term t = new Term("key",prefix);
			// filter out unique keys
			if(ir.docFreq(t) <= 1)
				continue;
			TermDocs td = ir.termDocs(t);
			// key -> rank
			HashMap<String,Double> refs = new HashMap<String,Double>();
			// key -> redirect target
			HashMap<String,String> redirects = new HashMap<String,String>();
			while(td.next()){
				Document d = ir.document(td.doc());
				String key = d.get("key");
				String redirect = d.get("redirect");
				double ref = Integer.parseInt(d.get("rank"));
				if(redirect != null && redirect.length()>0){
					redirects.put(key,redirect);
					ref = average(ref,Integer.parseInt(d.get("redirect_rank")));					
				}
				
				if(key.equalsIgnoreCase(prefix))
					ref *= EXACT_BOOST; // boost for exact match
				refs.put(key,ref);
			}
			ArrayList<Entry<String,Double>> sorted = new ArrayList<Entry<String,Double>>();
			sorted.addAll(refs.entrySet());
			Collections.sort(sorted,new Comparator<Entry<String,Double>>() {
				public int compare(Entry<String,Double> o1, Entry<String,Double> o2){
					double d = o2.getValue() - o1.getValue();
					if(d == 0) return 0;
					if(d > 0) return 1;
					else return -1;
				}
			});
			// hash set of selected articles and places they redirect to
			HashSet<String> selectedWithRedirects = new HashSet<String>();
			ArrayList<String> selected = new ArrayList<String>();
			for(int i=0;selected.size()<perPrefix && i<sorted.size();i++){
				String key = sorted.get(i).getKey();
				String redirect = redirects.get(key);
				if((redirect == null || !selectedWithRedirects.contains(redirect)) 
						&& !selectedWithRedirects.contains(key)){
					selected.add(serialize(key,sorted.get(i).getValue(),redirect));
					selectedWithRedirects.add(key);
					selectedWithRedirects.add(redirect);					
				}
			}
			Document d = new Document();
			d.add(new Field("prefix",prefix,Field.Store.NO,Field.Index.NO_NORMS));
			d.add(new Field("articles",new StringList(selected).toString(),Field.Store.YES,Field.Index.NO));
			writer.addDocument(d);
		}
		log.info("Adding title keys ...");
		ProgressReport progress = new ProgressReport("title keys",1000);
		for(int i=0;i<ir.maxDoc();i++){
			progress.inc();
			if(ir.isDeleted(i))
				continue;
			
			Document stored = ir.document(i); 
			String key = stored.get("key");
			String redirect = stored.get("redirect");
			double ref = Integer.parseInt(stored.get("rank"));
			if(redirect != null && redirect.length()>0){
				ref = average(ref,Integer.parseInt(stored.get("redirect_rank")));					
			}			
			Document d = new Document();
			d.add(new Field("article",serialize(key,ref,redirect),Field.Store.YES,Field.Index.NO));			
			ArrayList<Token> canonized = canonize(key,iid,filters); 
			for(Token t : canonized){
				d.add(new Field("key",t.termText(),Field.Store.NO,Field.Index.TOKENIZED));
			}
			writer.addDocument(d);
		}
		ir.close();
		log.info("Optimizing ...");
		writer.optimize();
		writer.close();		
		
		IndexThread.makeIndexSnapshot(prefixIid,prefixIid.getImportPath());
	}
	
	private double average(double ref, int i) {
		return Math.sqrt(ref * i);
	}
	private String serialize(String key, double score, String redirect) {
		String r = "";
		if(redirect != null){
			// include redirect info only for inter-namespace redirects
			String ns1 = key.substring(0,key.indexOf(':'));
			String ns2 = redirect.substring(0,redirect.indexOf(':'));
			if(!ns1.equals(ns2))
				r = redirect.replace(' ','_');
		}
		return key.replace(' ','_')+" "+(int)score+" "+r;
	}
	
	public static String strip(String s){
		s = s.toLowerCase();
		return FastWikiTokenizerEngine.stripTitle(s);
	}
	
	/** Obtain all the different versions of the whole key (with accents, without, transliterated.. ) */
	private static ArrayList<Token> canonize(String key, IndexId iid, FilterFactory filters) throws IOException{
		FastWikiTokenizerEngine tokenizer = new FastWikiTokenizerEngine(key,iid,new TokenizerOptions.PrefixCanonization());
		return filters.canonicalFilter(tokenizer.parse());		
	}
	
	/** Do an old-fashioned batch update of the precursor index */
	public void batchUpdate(Collection<IndexUpdateRecord> records) throws IOException {
		Transaction trans = new Transaction(pre, IndexId.Transaction.INDEX);
		trans.begin();
		try{
			IndexReader reader = IndexReader.open(pre.getIndexPath()); 
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
			writer = WikiIndexModifier.openForWrite(pre.getIndexPath(),false,new PrefixAnalyzer());
			initWriter(writer);
			for(IndexUpdateRecord rec : records){
				if(rec.doAdd()){
					Article a = rec.getArticle();					
					WikiIndexModifier.transformArticleForIndexing(a);
					log.debug(iid+": Adding "+a.toStringFull());
					addToPrecursor(rec.getNsTitleKey(),a.getRank(),a.getRedirectTarget(),a.getRedirectRank(),rec.getIndexKey());
				}
			}
			writer.close();
			trans.commit();
		} catch(IOException e){
			trans.rollback();
			throw e;
		}
	}
	
	/** Add a new precursor index entry */
	protected void addToPrecursor(String key) throws IOException{
		int rank = links.getRank(key);
		String redirect = links.getRedirectTarget(key);
		String pageid = links.getPageId(key);
		int redirectRank = 0;
		if(redirect != null)
			redirectRank = links.getRank(redirect);
		addToPrecursor(key,rank,redirect,redirectRank,pageid);
	}
	
	/** Modify a precursor index entry */
	public void deleteFromPrecursor(String pageId) throws IOException{
		writer.deleteDocuments(new Term("pageid",pageId));
	}
	
	/** Add a new precursor index entry */
	public void addToPrecursor(String key, int rank, String redirect, int redirectRank, String pageId) throws IOException{	
		String strippedKey = strip(key);
		String strippedTarget = redirect==null? null : strip(redirect);
		if(redirect == null);
		else if(strippedTarget.equalsIgnoreCase(strippedKey))
			return; // ignore camelcase redirects (case Aa -> AA)
		else if(strippedTarget.startsWith(strippedKey))
			return; // ignore redirects like byzantine -> byzantine empire
		// add to index
		Document d = new Document();
		d.add(new Field("pageid",pageId,Field.Store.NO,Field.Index.UN_TOKENIZED));
		d.add(new Field("key",key,Field.Store.YES,Field.Index.UN_TOKENIZED));
		ArrayList<Token> canonized = canonize(key,iid,filters); 
		for(Token t : canonized){
			d.add(new Field("key",t.termText(),Field.Store.NO,Field.Index.TOKENIZED));
		}
		if(redirect!=null && !redirect.equals("")){ // redirect target and its rank
			d.add(new Field("redirect",redirect,Field.Store.YES,Field.Index.NO));
			d.add(new Field("redirect_rank",Integer.toString(redirectRank),Field.Store.YES,Field.Index.NO));
		}
		d.add(new Field("rank",Integer.toString(rank),Field.Store.YES,Field.Index.NO));
		writer.addDocument(d);	
	}
	
	public void close() throws IOException {
		if(writer != null)
			writer.close();
		if(links != null)
			links.close();
	}

}
