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
import org.wikimedia.lsearch.ranks.StringList;
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
			
			log.info("Adding words and phrases");
			LuceneDictionary dict = new LuceneDictionary(ir,contents);
			//dict.setNoProgressReport();
			Word word;
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
					if(allowed && freq > minPhraseFreq){
						boolean inTitle = ir.docFreq(new Term(title,w))!= 0;
						addPhrase(w,freq,inTitle,null,null);
					}
				} else{ // word
					if(freq > minWordFreq){
						boolean inTitle = ir.docFreq(new Term(title,w))!= 0;
						if(inTitle && !stopWords.contains(w))
							context = makeContext(w,ir,fields,stopWords);
						addWord(w,freq,context);
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
						//String context = makeContext(w,ir,fields,stopWords);
						addPhrase(w,freq,true,null,null);
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
			System.out.println(phrase+" -> "+corrected);
		}
		addContext(phrase,context);			

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
		ngramWriter.createNgramFields(doc,"word",word,NgramIndexer.Type.WORDS);
		doc.add(new Field("word",word, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("freq",Integer.toString(freq), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta1",dmeta.doubleMetaphone(word), Field.Store.YES, Field.Index.NO));
		doc.add(new Field("meta2",dmeta.doubleMetaphone(word,true), Field.Store.YES, Field.Index.NO));
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
}
