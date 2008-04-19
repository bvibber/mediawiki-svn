package org.wikimedia.lsearch.spell.api;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.StringTokenizer;
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
import org.apache.lucene.search.MultiSearcherBase;
import org.apache.lucene.search.PhraseQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.search.Searcher;
import org.apache.lucene.search.TermQuery;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.analyzers.LanguageAnalyzer;
import org.wikimedia.lsearch.analyzers.StringTokenStream;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.highlight.Highlight.FragmentScore;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.search.IndexSearcherMul;
import org.wikimedia.lsearch.search.WikiSearcher;
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;
import org.wikimedia.lsearch.spell.dist.EditDistance;
import org.wikimedia.lsearch.util.HighFreqTerms;
import org.wikimedia.lsearch.util.ProgressReport;

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
		this(iid.getSpell(),false);
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
		this.langCode=iid.getLangCode();
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
		
		public String toString(){
			return phrase+" "+freq;
		}
	}
	
	public void createFromPrecursor(String precursorPath){
		String path = spell.getImportPath(); // dest where to put index
		FieldNameFactory fields = new FieldNameFactory();
		FilterFactory filters = new FilterFactory(iid);
		final String title = fields.title();
		final String contents = fields.contents();
		try {
			ngramWriter.createIndex(path,new SimpleAnalyzer());
			IndexReader ir = IndexReader.open(precursorPath);
			HashSet<String> stopWords = new HashSet<String>();
			TermDocs td = ir.termDocs(new Term("metadata_key","stopWords"));
			if(td.next()){
				for(String s : ir.document(td.doc()).get("metadata_value").split(" "))
					stopWords.add(s);
			}
			addMetadata("stopWords",stopWords);

			log.info("Adding titles");
			ProgressReport progress = new ProgressReport("titles",10000);
			// add all titles
			for(int i=0;i<ir.maxDoc();i++){
				progress.inc();
				if(ir.isDeleted(i))
					continue;
				Document d = ir.document(i);
				// normal title
				String titleText = d.get(title);
				if(titleText != null){
					String rank = d.get("rank");
					String redirect = d.get("redirect");
					String ns = d.get("namespace");
					addTitle(titleText,ns,rank,redirect);
					String canonicalTitle = filters.canonizeString(titleText);
					if(canonicalTitle != null)
						addTitle(canonicalTitle,ns,rank,filters.canonicalStringFilter(redirect));
				}
				
			}
			
			log.info("Getting most common phrases");
			LuceneDictionary dict = new LuceneDictionary(ir,contents);
			Word word;
			dict.setNoProgressReport();
			// prefix -> list of phrases
			HashMap<String,ArrayList<Phrase>> commonPhrases = new HashMap<String,ArrayList<Phrase>>();
			while((word = dict.next()) != null){
				if(word.getFrequency() > (minPhraseFreq * 100) && word.getWord().contains("_")){					
					String w = word.getWord();
					String[] parts = w.split("_");
					if(!anyStop(parts,stopWords)){
						String key = w.substring(0,2);
						ArrayList<Phrase> targ = commonPhrases.get(key);
						if(targ == null)
							commonPhrases.put(key,(targ=new ArrayList<Phrase>()));
						targ.add(new Phrase(word));
					}
				}
			}
			// sort descending on freq
			for(ArrayList<Phrase> targ : commonPhrases.values()){
				Collections.sort(targ,  new Comparator<Phrase>() {
					public int compare(Phrase o1, Phrase o2) {
						return o2.freq - o1.freq;
					}});
			} 
			
			log.info("Adding words and phrases");
			dict = new LuceneDictionary(ir,contents);
			dict.setProgressReport(new ProgressReport("terms",10000));
			// Word word;
			while((word = dict.next()) != null){
				String w = word.getWord();
				int freq = word.getFrequency();				
				String context = null;				
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
					if(allowed && freq >= minPhraseFreq){
						boolean inTitle = ir.docFreq(new Term(title,w))!= 0;
						String common = getCommonMisspell(w,freq,commonPhrases);
						if(common != null && !validateCommonMisspell(w,common,stopWords,filters))
							common = null;
						addPhrase(w,freq,inTitle,common,null);
					}
				} else{ // word
					if(freq >= minWordFreq){
						boolean inTitle = ir.docFreq(new Term(title,w))!= 0;
						if(inTitle && !stopWords.contains(w))
							context = makeContext(w,ir,fields,stopWords);
						addWord(w,freq,context);
					}
				}
			}
			log.info("Adding phrases with stop words from titles");
			dict = new LuceneDictionary(ir,title);
			dict.setProgressReport(new ProgressReport("terms",10000));
			while((word = dict.next()) != null){
				String w = word.getWord();
				if(w.contains("_")){ // phrase
					String[] words = w.split("_+");
					// start or end with stop word
					if(stopWords.contains(words[0]) || stopWords.contains(words[words.length-1])){
						int freq = ir.docFreq(new Term("contents",w));
						//String context = makeContext(w,ir,fields,stopWords);
						addPhrase(w,freq,true,null,null);
					}
				} else{
					// add words that haven't been added in first pass but are in titles
					int freq = ir.docFreq(new Term("contents",w));
					if(freq < minWordFreq && hasInLinks(w,ir,fields)){
						String context = null;
						if(!stopWords.contains(w))
							context = makeContext(w,ir,fields,stopWords);
						addWord(w,freq,context);
					}
				} 
			}
			log.info("Adding titles (other namespaces)");
			progress = new ProgressReport("titles",10000);
			// add all titles
			for(int i=0;i<ir.maxDoc();i++){
				progress.inc();
				if(ir.isDeleted(i))
					continue;
				Document d = ir.document(i);
				// title + namespace
				String allTitleText = d.get("ns_title"); 
				if(allTitleText != null){
					String namespace = d.get("ns_namespace");
					String rank = d.get("ns_rank");
					String redirect = d.get("ns_redirect");		
					addNsTitle(allTitleText,namespace,rank,redirect);
					String canonicalTitle = filters.canonizeString(allTitleText);
					if(canonicalTitle != null)
						addNsTitle(canonicalTitle,namespace,rank,filters.canonicalStringFilter(redirect));
				}
			}
			// add words/phrases
			log.info("Adding words and phrases (other namespaces)");
			dict = new LuceneDictionary(ir,"ns_title");
			dict.setProgressReport(new ProgressReport("terms",10000));
			while((word = dict.next()) != null){
				String w = word.getWord();
				if(w.contains("_")){ // phrase					
					addNsPhrase(w,ir,true);
				} else{ // word
					addNsWord(w,ir);
				}
			}
			log.info("Optimizing...");
			ngramWriter.closeAndOptimize();
			ir.close();			
		} catch (IOException e) {
			log.fatal("Cannot build titles suggest index for "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return;
		}
		
	}	
	
	/** If all strings has stop words or numbers */
	private boolean anyStop(String[] parts, HashSet<String> stopWords) {
		for(String s : parts)
			if(stopWords.contains(s) || isNumberLike(s) || s.length()<=2)
				return true;
		return false;
	}

	/** Provide contextual words for words and phraes in title */
	protected String makeContext(String w, IndexReader ir, FieldNameFactory fields, HashSet<String> stopWords) throws IOException{
		// add additional information about context
		TermDocs ttd = ir.termDocs(new Term(fields.title(),w));
		HashSet<String> words = new HashSet<String>();
		while(ttd.next()){
			words.addAll(new StringList(ir.document(ttd.doc()).get(fields.spellcheck_context())).toCollection());
		}
		words.removeAll(stopWords);
		words.remove(w);
		return new StringList(words).toString();
	}
	
	protected boolean hasInLinks(String w, IndexReader ir, FieldNameFactory fields) throws IOException{
		TermDocs td = ir.termDocs(new Term(fields.title(),w));
		while(td.next()){
			String rank = ir.document(td.doc()).get("rank");
			if(rank != null && !rank.equals("0"))
				return true;
		}
		return false;
	}

	
	
	/**
	 * Register a title in the index, without tokenization, strip of accents and such. 
	 * 
	 * @param title
	 */
	public void addTitle(String title, String ns, String rank, String redirect){
		Document doc = new Document();
		// store normalized version (only letters and spaces)
		String normalized = FastWikiTokenizerEngine.normalize(title.toLowerCase());
		String decomposed = FastWikiTokenizerEngine.decompose(normalized);
		// doc.add(new Field("title", ns+":"+title, Field.Store.YES, Field.Index.NO));
		doc.add(new Field("title", normalized, Field.Store.YES, Field.Index.UN_TOKENIZED));
		if(decomposed != normalized)
			doc.add(new Field("title", decomposed, Field.Store.NO, Field.Index.UN_TOKENIZED));
		doc.add(new Field("rank", rank, Field.Store.YES, Field.Index.NO));
		if(redirect!=null){
			String redirectNormalized = FastWikiTokenizerEngine.normalize(redirect.substring(redirect.indexOf(':')+1).toLowerCase());
			if(!normalized.equals(redirectNormalized)){
				doc.add(new Field("redirect", redirectNormalized, Field.Store.YES, Field.Index.NO));
			}
		}
		if(title.length() >= 5)
			ngramWriter.createNgramFields(doc,"title",decomposed,NgramIndexer.Type.TITLES);		
		ngramWriter.addDocument(doc);
	}
	/** Add titles in all namespaces */	
	public void addNsTitle(String title, String ns, String rank, String redirect){
		Document doc = new Document();
		String normalized = FastWikiTokenizerEngine.normalize(title.toLowerCase());
		String decomposed = FastWikiTokenizerEngine.decompose(normalized);
		//doc.add(new Field("ns_title", ns+":"+title, Field.Store.YES, Field.Index.NO));
		doc.add(new Field("ns_title", ns+":"+normalized, Field.Store.YES, Field.Index.UN_TOKENIZED));
		if(decomposed != normalized)
			doc.add(new Field("ns_title", ns+":"+decomposed, Field.Store.NO, Field.Index.UN_TOKENIZED));
		doc.add(new Field("ns_namespace", ns, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("ns_rank", rank, Field.Store.YES, Field.Index.NO));
		if(redirect!=null && redirect.substring(0,redirect.indexOf(':')).equals(ns)){
			String redirectNormalized = FastWikiTokenizerEngine.normalize(redirect.substring(redirect.indexOf(':')+1).toLowerCase());
			if(!normalized.equals(redirectNormalized)){
				doc.add(new Field("ns_redirect", redirectNormalized, Field.Store.YES, Field.Index.NO));
			}
		}
		if(title.length() >= 5)
			ngramWriter.createNgramFields(doc,"ns_title",decomposed,NgramIndexer.Type.TITLES);		
		ngramWriter.addDocument(doc);
	}
	
	static class SimpleInt {
		int count = 0;
	}
	protected HashMap<String,SimpleInt> getFrequencies(String w, IndexReader reader) throws IOException{
		HashMap<String,SimpleInt> ret = new HashMap<String,SimpleInt>();
		TermDocs td = reader.termDocs(new Term("ns_title",w));
		while(td.next()){
			Document d = reader.document(td.doc());
			String ns = d.get("ns_namespace"); 
			SimpleInt i = ret.get(ns);
			if(i == null){
				i = new SimpleInt();
				ret.put(ns,i);
			}
			i.count+=Integer.parseInt(d.get("ns_rank")); // estimate freq based on rank
		}
		return ret;
		
	}
 
	/** Add phrase in namespace other than default */
	public void addNsPhrase(String phrase, IndexReader ir, boolean inTitle) throws IOException {
		if(phrase.length() <= 2){
			log.warn("Invalid phrase: "+phrase);
			return;
		}
		HashMap<String,SimpleInt> freq = getFrequencies(phrase,ir);
		
		Document doc = new Document();
		doc.add(new Field("ns_phrase", phrase, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("ns_namespace", new StringTokenStream(freq.keySet())));
		for(Entry<String,SimpleInt> e : freq.entrySet()){
			doc.add(new Field("ns_freq_"+e.getKey(), Integer.toString(e.getValue().count), Field.Store.YES, Field.Index.NO));
		}
		if(inTitle){
			doc.add(new Field("ns_intitle","1", Field.Store.YES, Field.Index.UN_TOKENIZED));
		}
		ngramWriter.addDocument(doc);
	}
	
	/** Add word in ns other than default */
	public void addNsWord(String word, IndexReader ir) throws IOException {
		if(word.length() < 2)
			return;
		HashMap<String,SimpleInt> freq = getFrequencies(word,ir);
		int freqSum = 0;
		for(SimpleInt f : freq.values())
			freqSum += f.count;
		Document doc = new Document();
		String decomposed = FastWikiTokenizerEngine.decompose(word);
		ngramWriter.createNgramFields(doc,"ns_word",decomposed,NgramIndexer.Type.WORDS);
		doc.add(new Field("ns_word",word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		if(decomposed != word)
			doc.add(new Field("ns_word",decomposed, Field.Store.NO, Field.Index.UN_TOKENIZED));
		for(Entry<String,SimpleInt> e : freq.entrySet())
			doc.add(new Field("ns_freq_"+e.getKey(), Integer.toString(e.getValue().count), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("ns_freq",Integer.toString(freqSum),Field.Store.YES, Field.Index.NO));
		doc.add(new Field("ns_meta1",dmeta.doubleMetaphone(decomposed), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("ns_meta2",dmeta.doubleMetaphone(decomposed,true), Field.Store.YES, Field.Index.NO));
		ngramWriter.addDocument(doc);
	}	

	
	/** 
	 * Add phrase to index
	 * 
	 * @param phrase - 2+ words joined with underscore
	 * @param nf -  frequencies of phrase in various namespaces
	 * @param namespaces - namespaces where phrase appears in title
	 */
	public void addPhrase(String phrase, int freq, boolean inTitle, String corrected, String context){
		if(phrase.length() <= 2){
			log.warn("Invalid phrase: "+phrase);
			return;
		}
		Document doc = new Document();
		//ngramWriter.createNgramFields(doc,"phrase",phrase);
		doc.add(new Field("phrase",phrase, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(freq), Field.Store.YES, Field.Index.NO));
		if(inTitle){
			doc.add(new Field("intitle","1", Field.Store.YES, Field.Index.UN_TOKENIZED));
		}
		if(corrected != null){
			doc.add(new Field("misspell",corrected, Field.Store.YES, Field.Index.NO));
			System.out.println("Misspell: "+phrase+" -> "+corrected);
		}
		//addContext(phrase,context);			

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
	public void addWord(String word, int freq, String context){
		if(word.length() < 2)
			return;
		Document doc = new Document();
		String decomposed = FastWikiTokenizerEngine.decompose(word);
		ngramWriter.createNgramFields(doc,"word",decomposed,NgramIndexer.Type.WORDS);
		doc.add(new Field("word",word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		if(decomposed != word)
			doc.add(new Field("word",decomposed, Field.Store.NO, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(freq), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta1",dmeta.doubleMetaphone(decomposed), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta2",dmeta.doubleMetaphone(decomposed,true), Field.Store.YES, Field.Index.NO));
		addContext(word,context);		
		ngramWriter.addDocument(doc);
	}
		
	public void addContext(String key, String context){
		if(context == null)
			return;
		Document doc = new Document();
		doc.add(new Field("context_key",key, Field.Store.NO, Field.Index.UN_TOKENIZED));
		doc.add(new Field("context", context, Field.Store.YES, Field.Index.NO));
		ngramWriter.addDocument(doc);
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
	
	private boolean isNumberLike(String p){
		if(p.length()>0)
			return Character.isDigit(p.charAt(0));
		return false;
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
		
		// if they are same decomposed
		if(FastWikiTokenizerEngine.decompose(old).equals(FastWikiTokenizerEngine.decompose(corr)))
			return false;
		
		// check stem
		HashSet<String> words = new HashSet<String>();
		words.add(old); words.add(corr);
		if(filters.stem(words).size() == 1)
			return false; // new and old words stem to same
		
		return true;		
	}
	
	/** See if this phrase is a misspell of some very common phrase */
	protected String getCommonMisspell(String phrase, int freq, HashMap<String,ArrayList<Phrase>> commonPhrases){
		String key = phrase.substring(0,2);
		if(!commonPhrases.containsKey(key))
			return null;
		int minFreq = freq * 100;
		for(Phrase p : commonPhrases.get(key)){
			if(p.freq < minFreq)
				return null;
			if(p.dist.getDistance(phrase) == 1){				
				return p.phrase;
			}
		}
		return null;
	}
}
