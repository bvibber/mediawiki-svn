package org.wikimedia.lsearch.spell.api;

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
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.MultiSearcher;
import org.apache.lucene.search.PhraseQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.search.Searcher;
import org.apache.lucene.search.TermQuery;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.search.IndexSearcherMul;
import org.wikimedia.lsearch.search.WikiSearcher;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;
import org.wikimedia.lsearch.util.HighFreqTerms;

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
	protected IndexId iid,titles;
	protected String langCode;
	protected IndexRegistry registry;
	protected DoubleMetaphone dmeta = new DoubleMetaphone();
	
	public TitleIndexer(IndexId iid){
		this(iid,false);
	}
	
	public TitleIndexer(IndexId titles, boolean createNew){
		this.titles = titles;
		this.iid = titles.getDB();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		this.minWordFreq = global.getIntDBParam(iid.getDBname(),"spell_titles","wordsMinFreq",3);
		this.minPhraseFreq = global.getIntDBParam(iid.getDBname(),"spell_titles","phrasesMinFreq",1);
		this.createNew = createNew;
		this.langCode=GlobalConfiguration.getInstance().getLanguage(iid.getDBname());
		this.ngramWriter = new NgramIndexer();
		this.registry = IndexRegistry.getInstance();
	}
	
	protected Searcher makeSearcher(IndexId main) throws IOException{
		if(main.isSingle())
			return new IndexSearcherMul(registry.getLatestSnapshot(main).path);
		else{
			ArrayList<IndexSearcherMul> searchers = new ArrayList<IndexSearcherMul>();
			for(String part : main.getPhysicalIndexes()){
				searchers.add(new IndexSearcherMul(registry.getLatestSnapshot(IndexId.get(part)).path));
			}
			return new MultiSearcher(searchers.toArray(new SearchableMul[]{}));
		}
	}
	
	/** Returns {NamespaceFreq, HashSet<Integer>} */
	protected Object[] getFreqAndNamespaces(Searcher searcher, int[] namespaces, int[] ranks, Query q) throws IOException {
		Hits hits = searcher.search(q);
		NamespaceFreq wnf = new NamespaceFreq();
		HashSet<Integer> ns = new HashSet<Integer>();
		for(int i=0;i<hits.length();i++){
			/*Document d = hits.doc(i);
			int n = Integer.parseInt(d.get("namespace"));
			String rr = d.get("rank");
			int r = rr==null? 0 : Integer.parseInt(d.get("rank")); */
			int id = hits.id(i);
			int n = namespaces[id];
			int r = ranks[id];
			wnf.incFrequency(n,r);
			ns.add(n);
		}
		return new Object[] {wnf,ns};
	}
	
	protected Object[] getFreqAndNamespaces(Searcher searcher, int[] ns, int[] ranks, String word) throws IOException {
		return getFreqAndNamespaces(searcher,ns,ranks,new TermQuery(new Term("title",word)));
	}
	
	protected Object[] getFreqAndNamespaces(Searcher searcher, int[] ns, int[] ranks, String[] phrase) throws IOException{
		PhraseQuery pq = new PhraseQuery();
		for(String p : phrase){
			pq.add(new Term("title",p));
		}
		return getFreqAndNamespaces(searcher,ns,ranks,pq);
	}
	
	protected NamespaceFreq getFrequency(Searcher searcher, int[] namespaces, Query q) throws IOException{
		Hits hits = searcher.search(q);
		NamespaceFreq wnf = new NamespaceFreq();
		//wnf.setFrequency(-10,hits.length());
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
	protected Object[] makeNamespaceMap(Searcher searcher) throws IOException{
		log.debug("Making namespace map...");
		int[] namespaces = new int[searcher.maxDoc()];
		int[] ranks = new int[searcher.maxDoc()];
		for(int i=0;i<namespaces.length;i++){
			namespaces[i] = -100;
			Document doc = searcher.doc(i);
			if(doc != null){
				namespaces[i] = Integer.parseInt(doc.get("namespace"));
				String rr = doc.get("rank");
				ranks[i] = rr==null? 0 : Integer.parseInt(rr);
			}
		}
		log.debug("Done making namespace map");
		return new Object[] {namespaces,ranks};
	}
	
	/** 
	 * Create new index from an index *snapshot* by reading all terms in the index. 
	 * Index will be created in the import directory.
	 */
	@SuppressWarnings("unchecked")
	public void createFromSnapshot(){
		String path = titles.getImportPath(); // dest where to put index
		try{
			log.debug("Creating new suggest index");
			ngramWriter.createIndex(path,new SimpleAnalyzer());
			Searcher searcher = makeSearcher(iid);
			//IndexSearcher searcher = new IndexSearcherMul(iid.getSpellTitles().getTempPath());
			// map doc_id -> namespace
			//int[] namespaces = makeNamespaceMap(searcher);
			Object[] nsr = makeNamespaceMap(searcher);
			int[] namespaces = (int[]) nsr[0];
			int[] ranks = (int[]) nsr[1];
			int totalAdded = 0, lastReport=0;
			
			for(String dbrole : iid.getPhysicalIndexes()){
				log.info("Processing index "+dbrole);
				if(!ngramWriter.isOpen()) // if we closed the index previously
					ngramWriter.reopenIndex(path,new SimpleAnalyzer());
				
				IndexId part = IndexId.get(dbrole);
				//IndexReader ir = searcher.getIndexReader(); 
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
					
					int freq = searcher.docFreq(new Term("contents",w));
					if(freq > minWordFreq){
						// index word
						Object[] ret = getFreqAndNamespaces(searcher,namespaces,ranks,w);
						NamespaceFreq wnf = (NamespaceFreq) ret[0];
						Collection<Integer> wns = (Collection<Integer>) ret[1];
						//NamespaceFreq wnf = getFrequency(searcher,namespaces,w);
						if(wnf.getFrequency() > minWordFreq){
							//Collection<Integer> wns = getNamespaces(searcher,namespaces,w);
							addWord(w,wnf,wns);
						}
					}
					if(freq > minPhraseFreq){
						// index phrases
						HashSet<String> phrases = new HashSet<String>();
						Hits hits = searcher.search(new TermQuery(new Term("title",w)));
						// from titles find phrases beginning with word
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
							Object[] ret = getFreqAndNamespaces(searcher,namespaces,ranks,phrase.split("_"));
							NamespaceFreq nf = (NamespaceFreq) ret[0];
							Collection<Integer> pns = (Collection<Integer>) ret[1];
							//NamespaceFreq nf = getFrequency(searcher,namespaces,phrase.split("_"));
							if(nf.getFrequency() > minPhraseFreq){
								//Collection<Integer> pns = getNamespaces(searcher,namespaces,phrase.split("_"));
								addPhrase(phrase,nf,pns,false);
							}
						}
						totalAdded += phrases.size();
						if(totalAdded - lastReport > 1000){
							log.info("Processed "+totalAdded+" phrases");
							lastReport = totalAdded;
						}
					}
				}
				log.debug("Finished index "+iid+", closing/optimizing.");
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
	
	public void createFromTempIndex(){
		String path = titles.getImportPath(); // dest where to put index
		FieldNameFactory fields = new FieldNameFactory();
		final String title = fields.title();
		final String contents = fields.contents();
		final String alttitle = fields.alttitle();
		try {
			ngramWriter.createIndex(path,new SimpleAnalyzer());
			IndexReader ir = IndexReader.open(iid.getSpellWords().getTempPath());
			HashSet<String> stopWords = new HashSet<String>();
			TermDocs td = ir.termDocs(new Term("metadata_key","stopWords"));
			if(td.next()){
				for(String s : ir.document(td.doc()).get("metadata_value").split(" "))
					stopWords.add(s);
			}
			addMetadata("stopWords",stopWords);

			// add all titles
			for(int i=0;i<ir.maxDoc();i++){
				if(ir.isDeleted(i))
					continue;
				String titleText = ir.document(i).get(title);
				if(titleText != null)
					addTitle(titleText);
				// FIXME: alttitle fiels is not generated!
				for(int j=0;j<WikiIndexModifier.ALT_TITLES;j++){
					String altTitleText = ir.document(i).get(alttitle+j);
					if(altTitleText != null)
						addTitle(altTitleText);
				}
			}
			
			LuceneDictionary dict = new LuceneDictionary(ir,contents);
			Word word;
			while((word = dict.next()) != null){
				String w = word.getWord();
				int freq = word.getFrequency();
				if(w.contains("_")){ // phrase
					String[] words = w.split("_+");					
					if(stopWords.contains(words[0]) || stopWords.contains(words[words.length-1]))
						continue;
					boolean allowed = true;
					for(String ww : words){
						// allow only those phrases consisting of title words
						if(ir.docFreq(new Term(title,ww)) == 0){
							allowed = false; 
							break; 
						}
					}
					if(allowed && freq > minPhraseFreq){
						boolean inTitle = ir.docFreq(new Term(title,w))!= 0;
						NamespaceFreq nsf = new NamespaceFreq();
						nsf.setFrequency(0,freq);
						ArrayList<Integer> nss = new ArrayList<Integer>();
						nss.add(0);
						addPhrase(w,nsf,nss,inTitle);
					}
				} else{
					if(freq > minWordFreq){
						NamespaceFreq nsf = new NamespaceFreq();
						nsf.setFrequency(0,freq);
						ArrayList<Integer> nss = new ArrayList<Integer>();
						nss.add(0);
						addWord(w,nsf,nss);
					}
				}
			}
			//ngramWriter.closeAndOptimize();
			//ngramWriter.reopenIndex(path,new SimpleAnalyzer());
			//IndexReader ngramReader = ngramWriter.getReader();
			// add stuff from titles with stop words
			dict = new LuceneDictionary(ir,title);
			while((word = dict.next()) != null){
				String w = word.getWord();
				if(w.contains("_")){ // phrase
					String[] words = w.split("_+");
					if(stopWords.contains(words[0]) || stopWords.contains(words[words.length-1])){
						int freq = ir.docFreq(new Term("contents",w));
						NamespaceFreq nsf = new NamespaceFreq();
						nsf.setFrequency(0,freq);
						ArrayList<Integer> nss = new ArrayList<Integer>();
						nss.add(0);
						addPhrase(w,nsf,nss,true);
					}
				}
			}			
			ngramWriter.closeAndOptimize();
			ir.close();
			
		} catch (IOException e) {
			log.fatal("Cannot build titles suggest index for "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return;
		}
		
	}

	/**
	 * Register a title in the index, without tokenization, just lowercase. 
	 * 
	 * @param title
	 */
	public void addTitle(String title){
		Document doc = new Document();
		doc.add(new Field("title", title.toLowerCase(), Field.Store.NO, Field.Index.UN_TOKENIZED));
		ngramWriter.addDocument(doc);
	}
	/** 
	 * Add phrase to index
	 * 
	 * @param phrase - 2+ words joined with underscore
	 * @param nf -  frequencies of phrase in various namespaces
	 * @param namespaces - namespaces where phrase appears in title
	 */
	public void addPhrase(String phrase, NamespaceFreq nf, Collection<Integer> namespaces, boolean inTitle){
		String freq = nf.serialize(minPhraseFreq);
		if(freq.length() == 0)
			return;
		if(phrase.length() <= 2){
			log.warn("Invalid phrase: "+phrase);
			return;
		}
		Document doc = new Document();
		//ngramWriter.createNgramFields(doc,"phrase",phrase);
		doc.add(new Field("phrase",phrase, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",freq, Field.Store.YES, Field.Index.NO));
		for(Integer ns : namespaces){
			doc.add(new Field("namespace",ns.toString(),Field.Store.NO, Field.Index.UN_TOKENIZED));
		}
		if(inTitle)
			doc.add(new Field("intitle","1", Field.Store.YES, Field.Index.UN_TOKENIZED));

		ngramWriter.addDocument(doc);
	}
	
	/** 
	 * Add into metadata_key and metadata_value. 
	 * Collection is assumed to contain words (without spaces) 
	 */
	public void addMetadata(String key, Collection<String> values){
		StringBuilder sb = new StringBuilder();
		// serialize by joining with spaces
		for(String val : values){
			if(sb.length() != 0)
				sb.append(" ");
			sb.append(val);
		}
		Document doc = new Document();
		doc.add(new Field("metadata_key",key, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("metadata_value",sb.toString(), Field.Store.YES, Field.Index.NO));
		
		ngramWriter.addDocument(doc);
	}
	
	/** Add ordinary word to the index 
	 * 
	 * @param word - word to add
	 * @param nf - frequencies in namespaces
	 * @param namespaces - namespaces where word appears in title
	 */
	public void addWord(String word, NamespaceFreq nf, Collection<Integer> namespaces){
		if(word.length() < 2)
			return;
		String freq = nf.serialize();
		if(freq.length() == 0)
			return;
		Document doc = new Document();
		ngramWriter.createNgramFields(doc,"word",word);
		doc.add(new Field("word",word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",freq, Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta1",dmeta.doubleMetaphone(word), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta2",dmeta.doubleMetaphone(word,true), Field.Store.YES, Field.Index.NO));
		for(Integer ns : namespaces){
			doc.add(new Field("namespace",ns.toString(),Field.Store.NO, Field.Index.UN_TOKENIZED));
		}
		
		ngramWriter.addDocument(doc);
	}
	
	/** Update the index */
	public void update(Collection<IndexUpdateRecord> records){
		/*String path = iid.getIndexPath();
		try{			
			log.info("Updating suggest index for "+iid+" with "+records.size());
			IndexReader ir = IndexReader.open(path);
			Searcher searcher = makeSearcher(iid.getDB());
			// TODO: don't use namespaces, but fetch fields, it's likely to be more efficient for small updates
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
			log.error("Cannot update index for "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return;
		}*/
	}
	
}
