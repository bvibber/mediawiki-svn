package org.wikimedia.lsearch.spell.api;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
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
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.highlight.Highlight.FragmentScore;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.search.IndexSearcherMul;
import org.wikimedia.lsearch.search.WikiSearcher;
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;
import org.wikimedia.lsearch.spell.dist.EditDistance;
import org.wikimedia.lsearch.util.HighFreqTerms;

/** 
 * Index words and phrases from articles. 
 * 
 * Fields:
 *  * title - striped lowercased titles
 *  + fields from NgramIndexer for words
 *  + fields from NgramIndexer for phrases
 * 
 * @author rainman
 *
 */
public class SpellCheckIndexer {
	static Logger log = Logger.getLogger(SpellCheckIndexer.class);
	protected NgramIndexer ngramWriter;
	public static final boolean NEW_INDEX = true;
	protected boolean createNew;
	protected int minWordFreq, minPhraseFreq;
	protected IndexId iid,spell;
	protected String langCode;
	protected IndexRegistry registry;
	protected DoubleMetaphone dmeta = new DoubleMetaphone();
	
	public SpellCheckIndexer(IndexId iid){
		this(iid,false);
	}
	
	public SpellCheckIndexer(IndexId spell, boolean createNew){
		this.spell = spell;
		this.iid = spell.getDB();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		this.minWordFreq = global.getIntDBParam(iid.getDBname(),"spell","wordsMinFreq",3);
		this.minPhraseFreq = global.getIntDBParam(iid.getDBname(),"spell","phrasesMinFreq",1);
		if(minPhraseFreq < 1)
			minPhraseFreq = 1;
		this.createNew = createNew;
		this.langCode=GlobalConfiguration.getInstance().getLanguage(iid.getDBname());
		this.ngramWriter = new NgramIndexer();
		this.registry = IndexRegistry.getInstance();
	}
	
	static protected class Phrase {
		EditDistance dist;
		String phrase;
		int freq;
		
		Phrase(Word w){
			this.phrase = w.getWord();
			this.freq = w.getFrequency();
			dist = new EditDistance(phrase,false);
		}
	}
	
	public void createFromTempIndex(){
		String path = spell.getImportPath(); // dest where to put index
		FieldNameFactory fields = new FieldNameFactory();
		FilterFactory filters = new FilterFactory(iid);
		final String title = fields.title();
		final String contents = fields.contents();
		final String alttitle = fields.alttitle();
		try {
			ngramWriter.createIndex(path,new SimpleAnalyzer());
			IndexReader ir = IndexReader.open(iid.getSpell().getTempPath());
			HashSet<String> stopWords = new HashSet<String>();
			TermDocs td = ir.termDocs(new Term("metadata_key","stopWords"));
			if(td.next()){
				for(String s : ir.document(td.doc()).get("metadata_value").split(" "))
					stopWords.add(s);
			}
			addMetadata("stopWords",stopWords);

			log.info("Adding titles");
			// add all titles
			for(int i=0;i<ir.maxDoc();i++){
				if(ir.isDeleted(i))
					continue;
				Document d = ir.document(i);
				String titleText = d.get(title);
				String rank = d.get("rank");
				if(titleText != null && !rank.equals("0"))
					addTitle(titleText,rank);
			}
			/*log.info("Getting most common phrases");
			LuceneDictionary dict = new LuceneDictionary(ir,contents);
			Word word;
			dict.setNoProgressReport();
			// prefix -> list of phrases
			HashMap<String,ArrayList<Phrase>> commonPhrases = new HashMap<String,ArrayList<Phrase>>();
			while((word = dict.next()) != null){
				if(word.getFrequency() > (minPhraseFreq * 500) && word.getWord().contains("_")){
					String w = word.getWord();
					String key = w.substring(0,2);
					ArrayList<Phrase> targ = commonPhrases.get(key);
					if(targ == null)
						commonPhrases.put(key,(targ=new ArrayList<Phrase>()));
					targ.add(new Phrase(word));
				}
			}
			// sort descending on freq
			for(ArrayList<Phrase> targ : commonPhrases.values()){
				Collections.sort(targ,  new Comparator<Phrase>() {
					public int compare(Phrase o1, Phrase o2) {
						return o2.freq - o1.freq;
					}});
			} */
			
			
			log.info("Adding words and phrases");
			LuceneDictionary dict = new LuceneDictionary(ir,contents);
			//dict.setNoProgressReport();
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
						/*String common = getCommonMisspell(w,freq,commonPhrases);
						if(common != null && !validateCommonMisspell(w,common,stopWords,filters))
							common = null; */
						addPhrase(w,freq,inTitle,null);
					}
				} else{
					if(freq > minWordFreq){
						addWord(w,freq);
					}
				}
			}
			log.info("Adding phrases with stop words from titles");
			dict = new LuceneDictionary(ir,title);
			while((word = dict.next()) != null){
				String w = word.getWord();
				if(w.contains("_")){ // phrase
					String[] words = w.split("_+");
					// start or end with stop word
					if(stopWords.contains(words[0]) || stopWords.contains(words[words.length-1])){
						int freq = ir.docFreq(new Term("contents",w));
						addPhrase(w,freq,true,null);
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
	
	private String[] splitPhrase(String p){
		return p.split("_");		
	}
	
	private boolean isNumber(String p){
		for(int i=0;i<p.length();i++){
			if(!Character.isDigit(p.charAt(i)))
				return false;
		}
		return true;
	}
	
	/** Check if this is a valid misspell correction */
	protected boolean validateCommonMisspell(String phrase, String corrected, HashSet<String> stopWords, FilterFactory filters){
		// no numbers, corrected words 5+ letters, & doesn't stem to same
		// corrected words cannot be stopwords
		String[] p = splitPhrase(phrase);
		String[] c = splitPhrase(corrected);
		String old=null, corr=null;
		// no numbers or stop words
		for(String pp : p){
			if(isNumber(pp) || stopWords.contains(pp))
				return false;
		}
		for(String cc : c){
			if(isNumber(cc) || stopWords.contains(cc))
				return false;
		}
		
		if(p.length != c.length)
			return true; // split/join words
		
		// figure out what changed
		for(int i=0;i<p.length;i++){
			if(!p[i].equals(c[i])){
				old = p[i];
				corr = c[i];
				break;
			}
		}
		// check length
		if(old==null || corr==null || corr.length()<5)
			return false;
		// check stem
		HashSet<String> words = new HashSet<String>();
		words.add(old); words.add(corr);
		if(Suggest.stem(words,filters).size() == 1)
			return false; // new and old word stem to same
		
		return true;		
	}
	
	/** See if this phrase is a misspell of some very common phrase */
	protected String getCommonMisspell(String phrase, int freq, HashMap<String,ArrayList<Phrase>> commonPhrases){
		String key = phrase.substring(0,2);
		if(!commonPhrases.containsKey(key))
			return null;
		int minFreq = freq * 500;
		for(Phrase p : commonPhrases.get(key)){
			if(p.freq < minFreq)
				return null;
			if(p.dist.getDistance(phrase) == 1){				
				return p.phrase;
			}
		}
		return null;
	}

	/** Check if there are common mispellings of this phrase */
	@Deprecated
	protected boolean checkCommonPhraseMisspell(String phrase, int freq, IndexReader ir, String field) {
		LuceneDictionary d = new LuceneDictionary(ir,field,phrase.substring(0,1));
		d.setNoProgressReport();
		Suggest.Metric metric = new Suggest.Metric(phrase);
		Word word;
		while((word = d.next()) != null){
			if(word.getFrequency() * 100 < freq && word.getWord().indexOf("_")!=-1 ){
				String w = word.getWord();
				if(metric.distance(w) == 1){
					System.out.println("Detected common mispelling for "+w+" (correct: "+phrase+")");
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Register a title in the index, without tokenization, strip of accents and such. 
	 * 
	 * @param title
	 */
	public void addTitle(String title, String rank){
		Document doc = new Document();
		String stripped = FastWikiTokenizerEngine.stipTitle(title.toLowerCase());
		doc.add(new Field("title", stripped, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("rank", rank, Field.Store.YES, Field.Index.NO));
		if(title.length() >= 5)
			ngramWriter.createNgramFields(doc,"title",stripped,NgramIndexer.Type.TITLES);		
		ngramWriter.addDocument(doc);
	}
	/** 
	 * Add phrase to index
	 * 
	 * @param phrase - 2+ words joined with underscore
	 * @param nf -  frequencies of phrase in various namespaces
	 * @param namespaces - namespaces where phrase appears in title
	 */
	public void addPhrase(String phrase, int freq, boolean inTitle, String corrected){
		if(phrase.length() <= 2){
			log.warn("Invalid phrase: "+phrase);
			return;
		}
		Document doc = new Document();
		//ngramWriter.createNgramFields(doc,"phrase",phrase);
		doc.add(new Field("phrase",phrase, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(freq), Field.Store.YES, Field.Index.NO));
		if(inTitle)
			doc.add(new Field("intitle","1", Field.Store.YES, Field.Index.UN_TOKENIZED));
		if(corrected != null){
			doc.add(new Field("misspell",corrected, Field.Store.YES, Field.Index.NO));
			System.out.println(phrase+" -> "+corrected);
		}
			

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
	public void addWord(String word, int freq){
		if(word.length() < 2)
			return;
		Document doc = new Document();
		ngramWriter.createNgramFields(doc,"word",word,NgramIndexer.Type.WORDS);
		doc.add(new Field("word",word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(freq), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta1",dmeta.doubleMetaphone(word), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta2",dmeta.doubleMetaphone(word,true), Field.Store.YES, Field.Index.NO));
		
		ngramWriter.addDocument(doc);
	}	
}
