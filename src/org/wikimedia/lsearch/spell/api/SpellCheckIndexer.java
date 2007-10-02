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
import org.wikimedia.lsearch.spell.Suggest;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;
import org.wikimedia.lsearch.util.HighFreqTerms;

/** 
 * Index words and phrases from articles. 
 * 
 * Fields:
 *  * word - word from title
 *  * word_ngramN - word ngrams
 *  * phrase - phrase like douglas_adams
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
	protected IndexId iid,titles;
	protected String langCode;
	protected IndexRegistry registry;
	protected DoubleMetaphone dmeta = new DoubleMetaphone();
	
	public SpellCheckIndexer(IndexId iid){
		this(iid,false);
	}
	
	public SpellCheckIndexer(IndexId titles, boolean createNew){
		this.titles = titles;
		this.iid = titles.getDB();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		this.minWordFreq = global.getIntDBParam(iid.getDBname(),"spell","wordsMinFreq",3);
		this.minPhraseFreq = global.getIntDBParam(iid.getDBname(),"spell","phrasesMinFreq",1);
		this.createNew = createNew;
		this.langCode=GlobalConfiguration.getInstance().getLanguage(iid.getDBname());
		this.ngramWriter = new NgramIndexer();
		this.registry = IndexRegistry.getInstance();
	}
	
	public void createFromTempIndex(){
		String path = titles.getImportPath(); // dest where to put index
		FieldNameFactory fields = new FieldNameFactory();
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
				String titleText = ir.document(i).get(title);
				if(titleText != null)
					addTitle(titleText);
				for(int j=0;j<WikiIndexModifier.ALT_TITLES;j++){
					String altTitleText = ir.document(i).get(alttitle+j);
					if(altTitleText != null)
						addTitle(altTitleText);
				}
			}
			log.info("Adding words and phrases");
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
						addPhrase(w,freq,inTitle);
					}
				} else{
					if(freq > minWordFreq){
						addWord(w,freq);
					}
				}
			}
			log.info("Adding phrases with stop words from titles");
			// add stuff from titles with stop words
			dict = new LuceneDictionary(ir,title);
			while((word = dict.next()) != null){
				String w = word.getWord();
				if(w.contains("_")){ // phrase
					String[] words = w.split("_+");
					if(stopWords.contains(words[0]) || stopWords.contains(words[words.length-1])){
						int freq = ir.docFreq(new Term("contents",w));
						addPhrase(w,freq,true);
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

	/** Check if there are common mispellings of this phrase */
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
	public void addTitle(String title){
		Document doc = new Document();
		doc.add(new Field("title", FastWikiTokenizerEngine.stipTitle(title.toLowerCase()), Field.Store.NO, Field.Index.UN_TOKENIZED));
		ngramWriter.addDocument(doc);
	}
	/** 
	 * Add phrase to index
	 * 
	 * @param phrase - 2+ words joined with underscore
	 * @param nf -  frequencies of phrase in various namespaces
	 * @param namespaces - namespaces where phrase appears in title
	 */
	public void addPhrase(String phrase, int freq, boolean inTitle){
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
		ngramWriter.createNgramFields(doc,"word",word);
		doc.add(new Field("word",word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(freq), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta1",dmeta.doubleMetaphone(word), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta2",dmeta.doubleMetaphone(word,true), Field.Store.YES, Field.Index.NO));
		
		ngramWriter.addDocument(doc);
	}	
}
