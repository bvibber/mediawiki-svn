package org.wikimedia.lsearch.suggest.api;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.MultiSearcher;
import org.apache.lucene.search.PhraseQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.search.Searcher;
import org.apache.lucene.search.TermQuery;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.search.IndexSearcherMul;
import org.wikimedia.lsearch.search.WikiSearcher;
import org.wikimedia.lsearch.suggest.api.Dictionary.Word;

/** 
 * Index words and phrases from article titles. 
 * 
 * Fields:
 *  * word - word from title
 *  * phrase - phrase like douglas_adams
 *  * freq - stored serialized NamespaceFreq (ns:frequency, e.g. 0:234 1:12 14:3)
 *  * namespace - namespaces where the word/phrase is present   
 * 
 * @author rainman
 *
 */
public class TitleIndexer {
	static Logger log = Logger.getLogger(TitleIndexer.class);
	protected NgramIndexer ngramWriter;
	public static final boolean NEW_INDEX = true;
	protected boolean createNew;
	protected int minWordFreq, minPhraseFreq;
	protected IndexId iid;
	protected String langCode;
	protected IndexRegistry registry;
	protected String path;
	
	public TitleIndexer(IndexId iid, int minWordFreq, int minPhraseFreq){
		this(iid,minWordFreq,minPhraseFreq,false);
	}
	
	public TitleIndexer(IndexId iid, int minWordFreq, int minPhraseFreq, boolean createNew){
		this.iid = iid;
		this.minWordFreq = minWordFreq;
		this.minPhraseFreq = minPhraseFreq;
		this.createNew = createNew;
		this.langCode=GlobalConfiguration.getInstance().getLanguage(iid.getDBname());
		this.ngramWriter = new NgramIndexer();
		this.registry = IndexRegistry.getInstance();
		this.path = iid.getSuggestTitlesPath();
	}
	
	protected Searcher makeSearcher(IndexId logical) throws IOException{		
		if(logical.isSingle())
			return new IndexSearcherMul(registry.getLatestSnapshot(logical).path);
		else{
			ArrayList<IndexSearcherMul> searchers = new ArrayList<IndexSearcherMul>();
			for(String part : iid.getPhysicalIndexes()){
				searchers.add(new IndexSearcherMul(registry.getLatestSnapshot(IndexId.get(part)).path));
			}
			return new MultiSearcher(searchers.toArray(new SearchableMul[]{}));
		}
	}
	
	protected NamespaceFreq getFrequency(Searcher searcher, int[] namespaces, Query q) throws IOException{
		Hits hits = searcher.search(q);
		NamespaceFreq wnf = new NamespaceFreq();
		for(int j=0;j<hits.length();j++){
			wnf.incFrequency(namespaces[hits.id(j)]);
		}
		return wnf;
	}
	
	/** Get frequency for a single word */
	protected NamespaceFreq getFrequency(Searcher searcher, int[] namespaces, String word) throws IOException{
		return getFrequency(searcher,namespaces,new TermQuery(new Term("contents",word)));
	}
	
	/** Get frequency of phrase (invidual words as array) */
	protected NamespaceFreq getFrequency(Searcher searcher, int[] namespaces, String[] phrase) throws IOException{
		PhraseQuery pq = new PhraseQuery();
		for(String p : phrase){
			pq.add(new Term("contents",p));
		}
		return getFrequency(searcher,namespaces,pq);
	}
	
	/** Get namespaces where word appears in title */
	protected Collection<Integer> getNamespaces(Searcher searcher, int[] namespaces, Query q) throws IOException{
		Hits hits = searcher.search(q);
		HashSet<Integer> ns = new HashSet<Integer>();
		for(int j=0;j<hits.length();j++){
			ns.add(namespaces[hits.id(j)]);
		}
		return ns;
	}
	
	protected Collection<Integer> getNamespaces(Searcher searcher, int[] namespaces, String word) throws IOException{
		return getNamespaces(searcher,namespaces,new TermQuery(new Term("title",word)));
	}
	
	protected Collection<Integer> getNamespaces(Searcher searcher, int[] namespaces, String[] phrase) throws IOException{
		PhraseQuery pq = new PhraseQuery();
		for(String p : phrase){
			pq.add(new Term("title",p));
		}
		return getNamespaces(searcher,namespaces,pq);
	}
	
	/** 
	 * Returns the namespace for each doc_id
	 * @throws IOException 
	 * @FIXME: assumes optimized index
	 */
	protected int[] makeNamespaceMap(Searcher searcher) throws IOException{
		log.debug("Making namespace map...");
		int[] namespaces = new int[searcher.maxDoc()];
		for(int i=0;i<namespaces.length;i++){
			namespaces[i] = -100;
			Document doc = searcher.doc(i);
			if(doc != null)
				namespaces[i] = Integer.parseInt(doc.get("namespace"));
		}
		log.debug("Done making namespace map");
		return namespaces;
	}
	
	/** Create new title word/phrases index from an existing index *snapshot* by reading all terms in the index */
	public void createFromExistingIndex(IndexId src){		 
		try{
			log.debug("Creating new suggest index");
			ngramWriter.createIndex(path,new SimpleAnalyzer());
			Searcher searcher = makeSearcher(iid.getLogical());
			// map doc_id -> namespace
			int[] namespaces = makeNamespaceMap(searcher);
			
			for(String dbrole : src.getPhysicalIndexes()){
				log.info("Processing index "+dbrole);
				if(!ngramWriter.isOpen()) // if we closed the index previously
					ngramWriter.reopenIndex(path,new SimpleAnalyzer());
				
				IndexId part = IndexId.get(dbrole);
				IndexReader ir = IndexReader.open(registry.getLatestSnapshot(part).path);
				LuceneDictionary dict = new LuceneDictionary(ir,"title");
				IndexSearcher ngramSearcher = new IndexSearcher(path);
				Word word;
				// get all words, and all phrases beginning with word
				while((word = dict.next()) != null){
					log.debug("Processing word "+word);
					String w = word.getWord();
					
					// check if word is already in the index
					if(ngramSearcher.docFreq(new Term("word",w)) != 0)
						continue; 
					
					// index word					
					NamespaceFreq wnf = getFrequency(searcher,namespaces,w);
					Collection<Integer> wns = getNamespaces(searcher,namespaces,w);
					addWord(w,wnf,wns);

					// index phrases
					HashSet<String> phrases = new HashSet<String>();
					Hits hits = searcher.search(new TermQuery(new Term("title",w)));
					// find all phrases beginning with word
					for(int i=0;i<hits.length();i++){
						Document doc = hits.doc(i);
						// tokenize to make phrases
						FastWikiTokenizerEngine parser = new FastWikiTokenizerEngine(doc.get("title"),langCode,false); 
						ArrayList<Token> tokens = parser.parse();
						for(int j=0;j<tokens.size()-1;j++){
							Token t = tokens.get(j);
							// ignore aliases
							if(t.getPositionIncrement() == 0)
								continue;
							// find phrases beginning with the target word
							if(w.equals(t.termText())){
								phrases.add(t.termText()+"_"+tokens.get(j+1).termText());
							}
						}
					}
					log.debug("Adding "+phrases.size()+" phrases "+phrases);
					// index phrases
					for(String phrase : phrases){
						NamespaceFreq nf = getFrequency(searcher,namespaces,phrase.split("_"));			
						Collection<Integer> pns = getNamespaces(searcher,namespaces,phrase.split("_"));
						addPhrase(phrase,nf,pns);
					}
				}
				log.debug("Finished index "+dbrole+", closing/optimizing.");
				ir.close();
				ngramSearcher.close();
				ngramWriter.closeAndOptimize();
			}			
			searcher.close();
		} catch (IOException e) {
			log.fatal("Cannot build titles suggest index for "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return;
		}
	}
	
	/** 
	 * Add phrase to index
	 * 
	 * @param phrase - 2+ words joined with underscore
	 * @param nf -  frequencies of phrase in various namespaces
	 * @param namespaces - namespaces where phrase appears in title
	 */
	public void addPhrase(String phrase, NamespaceFreq nf, Collection<Integer> namespaces){
		String freq = nf.serialize(minPhraseFreq);
		if(freq.length() == 0)
			return;
		if(phrase.length() <= 2){
			log.warn("Invalid phrase: "+phrase);
			return;
		}
		Document doc = new Document();
		ngramWriter.createNgramFields(doc,"phrase",phrase);
		doc.add(new Field("phrase",phrase, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",freq, Field.Store.YES, Field.Index.NO));
		for(Integer ns : namespaces){
			doc.add(new Field("namespace",ns.toString(),Field.Store.NO, Field.Index.UN_TOKENIZED));
		}

		ngramWriter.addDocument(doc);
	}
	
	/** Add ordinary word to the index, convenient for suggesting joins 
	 * 
	 * @param word - word to add
	 * @param nf - frequencies in namespaces
	 * @param namespaces - namespaces where word appears in title
	 */
	public void addWord(String word, NamespaceFreq nf, Collection<Integer> namespaces){
		if(word.length() < 2)
			return;
		String freq = nf.serialize(minWordFreq);
		if(freq.length() == 0)
			return;
		Document doc = new Document();
		ngramWriter.createNgramFields(doc,"word",word);
		doc.add(new Field("word",word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",freq, Field.Store.YES, Field.Index.NO));
		for(Integer ns : namespaces){
			doc.add(new Field("namespace",ns.toString(),Field.Store.NO, Field.Index.UN_TOKENIZED));
		}
		
		ngramWriter.addDocument(doc);
	}
	
	/** Update the index */
	public void update(Collection<IndexUpdateRecord> records){
		try{			
			log.info("Updating suggest index for "+iid+" with "+records.size());
			IndexReader ir = IndexReader.open(path);
			Searcher searcher = makeSearcher(iid.getLogical());
			int[] namespaces = makeNamespaceMap(searcher);
			// get all words and phrases
			HashSet<String> words = new HashSet<String>();
			HashSet<String> phrases = new HashSet<String>();
			for(IndexUpdateRecord rec : records){
				String title = rec.getArticle().getTitle();
				ArrayList<Token> tokens = new FastWikiTokenizerEngine(title,langCode,false).parse();
				String last = null;
				// register word/phrases
				for(Token t : tokens){
					String w = t.termText();
					words.add(w);					
					if(last != null){
						phrases.add(last+"_"+w);						
					}
					last = w;
				}
			}
			searcher.close();
			
			// batch delete old values
			for(String word : words){
				ir.deleteDocuments(new Term("word",word));
			}
			for(String phrase : phrases){
				ir.deleteDocuments(new Term("phrase",phrase));
			}
			ir.close();
			ngramWriter.reopenIndex(path,new SimpleAnalyzer());
			
			// batch add new stuff
			for(String word : words){
				addWord(word,getFrequency(searcher,namespaces,word),getNamespaces(searcher,namespaces,word));
			}
			for(String phrase : phrases){
				String[] ph = phrase.split("_");
				addPhrase(phrase,getFrequency(searcher,namespaces,ph),getNamespaces(searcher,namespaces,ph));
			}
			
			ngramWriter.close();
		} catch(IOException e){
			log.error("Cannot update suggest index for "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return;
		}
	}
	
}
