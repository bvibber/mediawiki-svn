package org.wikimedia.lsearch.prefix;

import java.io.IOException;
import java.util.ArrayList;
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
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexThread;
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
	
	protected IndexId iid, prefixIid, pre;
	protected FilterFactory filters;
	protected Links links=null;
	protected IndexWriter writer=null;
	/** Builder for a new index based on standalone links snapshot */
	static public PrefixIndexBuilder newFromStandalone(IndexId iid) throws IOException{
		return new PrefixIndexBuilder(iid,Links.openStandalone(iid),null);
	}
	/** Builder for incremental updates to precursor index */
	static public PrefixIndexBuilder forPrecursorModification(IndexId iid, Links links) throws IOException{
		IndexWriter writer = WikiIndexModifier.openForWrite(iid.getPrecursor().getIndexPath(),false,new PrefixAnalyzer());
		writer.setMergeFactor(20);
		writer.setMaxBufferedDocs(500);
		return new PrefixIndexBuilder(iid,links,writer);
	}
	
	private PrefixIndexBuilder(IndexId iid, Links links, IndexWriter writer) throws IOException {
		this.iid = iid;
		prefixIid = iid.getPrefix();
		pre = prefixIid.getPrecursor();
		filters = new FilterFactory(iid);
		this.links = links;
		this.writer = writer;
	}
	
	
	
	public static void main(String[] args) throws IOException{
		int perPrefix = 15;
		boolean usetemp = false;
		String dbname = null;
		
		Configuration.open();		
		if(args.length == 0){
			System.out.println("Syntax: java PrefixIndexBuilder [-t] [-p <num>] <dbname>");
			System.out.println("Options:");
			System.out.println("   -p       - reuse temporary precursor index");
			System.out.println("   -t <num> - titles per prefix (default: "+perPrefix+")");
			return;
		}
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-p"))
				usetemp = true;
			else if(args[i].equals("-t"))
				perPrefix = Integer.parseInt(args[++i]);
			else if(args[i].startsWith("-")){
				System.out.println("Unrecognized option "+args[i]);
				return;
			} else
				dbname = args[i];
		}		
		
		IndexId iid = IndexId.get(dbname);
		PrefixIndexBuilder builder = newFromStandalone(iid);
		builder.createNewFromLinks(perPrefix,usetemp);
	}

	/**
	 * Create a completely new prefix index in the import path, 
	 * optionally rebuilds the precursor as well
	 * 
	 * @param perPrefix
	 * @param useExistingPrecursor
	 * @throws IOException
	 */
	public void createNewFromLinks(int perPrefix, boolean useExistingPrecursor) throws IOException{
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
		
		rebuildPrefixIndex(pre.getImportPath(),perPrefix);
		
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
				if(redirect != null && redirect.length()>0)
					redirects.put(key,redirect);
				double ref = Integer.parseInt(d.get("ref")) * lengthCoeff(key,prefix);
				if(key.equalsIgnoreCase(prefix))
					ref *= 100; // boost for exact match
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
			HashSet<String> selectedRedirects = new HashSet<String>();
			ArrayList<String> selected = new ArrayList<String>();
			for(int i=0;i<perPrefix && i<sorted.size();i++){
				String key = sorted.get(i).getKey();
				String redirect = redirects.get(key);
				if(redirect == null || !selectedRedirects.contains(redirect)){
					selected.add(key);
					selectedRedirects.add(redirect);
					selectedRedirects.add(key);
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
			Document d = new Document();
			String key = ir.document(i).get("key");
			d.add(new Field("key",key,Field.Store.YES,Field.Index.NO));
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
		
		IndexThread.makeIndexSnapshot(prefixIid,path);
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
	
	private static double lengthCoeff(String key, String prefix) {
		return 1;
	}
	/** Modify a precursor index entry */
	protected void modifyPrecursor(String key) throws IOException{
		writer.deleteDocuments(new Term("key",key));
		addToPrecursor(key);
	}
	/** Add a new precursor index entry */
	protected void addToPrecursor(String key) throws IOException{
		int ref = links.getNumInLinks(key);
		String redirect = links.getRedirectTarget(key);
		String strippedKey = strip(key);
		String strippedTarget = redirect==null? null : strip(redirect);
		if(redirect == null);
		else if(strippedTarget.equalsIgnoreCase(strippedKey))
			return; // ignore camelcase redirects (case Aa -> AA)
		else if(strippedTarget.startsWith(strippedKey))
			return; // ignore redirects like byzantine -> byzantine empire
		// add to index
		Document d = new Document();
		d.add(new Field("key",key,Field.Store.YES,Field.Index.UN_TOKENIZED));
		ArrayList<Token> canonized = canonize(key,iid,filters); 
		for(Token t : canonized){
			d.add(new Field("key",t.termText(),Field.Store.NO,Field.Index.TOKENIZED));
		}
		if(redirect!=null && !redirect.equals(""))
			d.add(new Field("redirect",redirect,Field.Store.YES,Field.Index.NO));
		d.add(new Field("ref",Integer.toString(ref),Field.Store.YES,Field.Index.NO));			
		writer.addDocument(d);	
	}

}
