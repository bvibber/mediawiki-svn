package org.wikimedia.lsearch.prefix;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.wikimedia.lsearch.analyzers.LowercaseAnalyzer;
import org.wikimedia.lsearch.analyzers.PrefixAnalyzer;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.spell.api.LuceneDictionary;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.storage.ArticleAnalytics;
import org.wikimedia.lsearch.storage.LinkAnalysisStorage;

/**
 * Build an index of all title prefixes
 * 
 * @author rainman
 *
 */
public class PrefixIndexBuilder {
	static Logger log = Logger.getLogger(PrefixIndexBuilder.class);
	
	public static void main(String[] args) throws IOException{
		final int PER_PREFIX = 10;
		boolean usetemp = false;
		String dbname = null;
		
		Configuration.open();		
		if(args.length == 0){
			System.out.println("Syntax: java PrefixIndexBuilder [-t] <dbname>");
			return;
		}
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-t"))
				usetemp = true;
			else if(args[i].startsWith("-")){
				System.out.println("Unrecognized option "+args[i]);
				return;
			} else
				dbname = args[i];
		}		
		
		IndexId iid = IndexId.get(dbname);
		IndexId pre = iid.getPrefix();
		
		long start = System.currentTimeMillis();
		
		if(!usetemp){
			IndexWriter writer = new IndexWriter(pre.getTempPath(),new PrefixAnalyzer(),true);
			writer.setMergeFactor(20);
			writer.setMaxBufferedDocs(500);
			LinkAnalysisStorage st = new LinkAnalysisStorage(iid);
			log.info("Writing temp index");
			int count = 0;
			Iterator<ArticleAnalytics> it =  st.iterator();
			while(it.hasNext()){
				if(++count % 1000 == 0)
					System.out.println("Processed "+count);
				ArticleAnalytics aa = it.next();
				String key = aa.getKey();
				//String title = key.substring(key.indexOf(":")+1).toLowerCase();
				String redirect = aa.getRedirectTarget();
				if(redirect == null)
					redirect = "";
				int ref = aa.getReferences();
				Document d = new Document();
				d.add(new Field("key",key,Field.Store.YES,Field.Index.TOKENIZED));
				d.add(new Field("redirect",redirect,Field.Store.YES,Field.Index.NO));
				d.add(new Field("ref",Integer.toString(ref),Field.Store.YES,Field.Index.NO));			
				writer.addDocument(d);
			}
			log.info("Optimizing temp index");
			writer.optimize();
			writer.close();
		}
		log.info("Writing prefix index");
		IndexWriter writer = new IndexWriter(pre.getImportPath(), new LowercaseAnalyzer(),true);
		writer.setMergeFactor(20);
		writer.setMaxBufferedDocs(1000);
		IndexReader ir = IndexReader.open(pre.getTempPath());
		LuceneDictionary dict = new LuceneDictionary(ir,"key");
		Word w;
		while((w = dict.next()) != null){
			String prefix = w.getWord();
			Term t = new Term("key",prefix);
			if(ir.docFreq(t) < 2)
				continue;
			TermDocs td = ir.termDocs(t);
			HashMap<String,Integer> refs = new HashMap<String,Integer>();
			while(td.next()){
				Document d = ir.document(td.doc());
				refs.put(d.get("key"),Integer.parseInt(d.get("ref")));				
			}
			ArrayList<Entry<String,Integer>> sorted = new ArrayList<Entry<String,Integer>>();
			sorted.addAll(refs.entrySet());
			Collections.sort(sorted,new Comparator<Entry<String,Integer>>() {
				public int compare(Entry<String,Integer> o1, Entry<String,Integer> o2){
					return o2.getValue() - o1.getValue();
				}
			});
			ArrayList<String> selected = new ArrayList<String>();
			for(int i=0;i<PER_PREFIX && i<sorted.size();i++){
				selected.add(sorted.get(i).getKey());
			}
			Document d = new Document();
			d.add(new Field("prefix",prefix,Field.Store.NO,Field.Index.NO_NORMS));
			d.add(new Field("articles",new StringList(selected).toString(),Field.Store.YES,Field.Index.NO));
			writer.addDocument(d);
		}
		log.info("Adding title keys ...");
		int count = 0;
		for(int i=0;i<ir.maxDoc();i++){
			if(++count % 1000 == 0)
				System.out.println("Added "+count);
			if(ir.isDeleted(i))
				continue;
			Document d = new Document();
			d.add(new Field("key",ir.document(i).get("key"),Field.Store.YES,Field.Index.TOKENIZED));
			writer.addDocument(d);
		}
		ir.close();
		log.info("Optimizing ...");
		writer.optimize();
		writer.close();		
		
		IndexThread.makeIndexSnapshot(pre,pre.getImportPath());
		long delta = System.currentTimeMillis() - start;
		System.out.println("Finished in "+formatTime(delta));
	}
	
	private static String formatTime(long l) {
		l /= 1000;
		if(l >= 3600) return l/3600+"h "+(l%3600)/60+"m "+(l%60)+"s";
		else if(l >= 60) return (l%3600)/60+"m "+(l%60)+"s";
		else return l+"s";
	}

}
