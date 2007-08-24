package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.LinkedList;
import java.util.Set;
import java.util.WeakHashMap;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.TopDocs;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.beans.ResultSet;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.NamespaceFilterWrapper;
import org.wikimedia.lsearch.search.SearcherCache;
import org.wikimedia.lsearch.spell.api.NamespaceFreq;
import org.wikimedia.lsearch.spell.api.NgramIndexer;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;
import org.wikimedia.lsearch.spell.dist.EditDistance;

public class Suggest {
	static Logger log = Logger.getLogger(Suggest.class);
	protected IndexId iid;
	protected IndexSearcher words;
	protected IndexSearcher titles;	
	protected int minHitsWords;
	protected int minHitsTitles;
	protected static WeakHashMap<IndexSearcher,Set<String>> stopWordsIndexes = new WeakHashMap<IndexSearcher,Set<String>>();
	protected Set<String> stopWords;
	
	/** Distance an metaphone metrics */
	static class Metric {
		protected DoubleMetaphone dmeta =  new DoubleMetaphone();
		protected String meta1, meta2;
		protected EditDistance sd;
		protected EditDistance sdmeta1, sdmeta2;
		protected String word;
		
		public Metric(String word){
			this.word = word;
			meta1 = dmeta.doubleMetaphone(word);
			meta2 = dmeta.doubleMetaphone(word,true);
			sd = new EditDistance(word);
			sdmeta1 = new EditDistance(meta1);
			sdmeta2 = new EditDistance(meta2);
		}
		/** Edit distance */
		public int distance(String w){
			return sd.getDistance(w);
		}		
		/** Edit distance of primary metaphone of w */
		public int meta1Distance(String w){
			return sdmeta1.getDistance(dmeta.doubleMetaphone(w));
		}
		/** Edit distance of alternative metaphone of w */
		public int meta2Distance(String w){
			return sdmeta2.getDistance(dmeta.doubleMetaphone(w,true));
		}
	}
	
	/** Number of results to fetch */
	public static final int POOL = 150;
	
	/** Lower limit to hit rate for joining */
	public static final int JOIN_FREQ = 1;
	
	public Suggest(IndexId iid) throws IOException{
		SearcherCache cache = SearcherCache.getInstance();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		this.iid = iid;
		this.words = cache.getLocalSearcher(iid.getSpellWords());
		this.titles = cache.getLocalSearcher(iid.getSpellTitles());
		this.minHitsWords = global.getIntDBParam(iid.getDBname(),"spell_words","minHits",20);
		this.minHitsTitles = global.getIntDBParam(iid.getDBname(),"spell_titles","minHits",20);
		
		synchronized(stopWordsIndexes){
			if(!stopWordsIndexes.containsKey(titles)){
				Set<String> s = Collections.synchronizedSet(new HashSet<String>());
				stopWordsIndexes.put(titles,s);
				TermDocs d = titles.getIndexReader().termDocs(new Term("metadata_key","stopWords"));
				if(d.next()){
					String val = titles.doc(d.doc()).get("metadata_value");
					for(String sw : val.split(" ")){
						s.add(sw);
					}
				}				
			}
			this.stopWords = stopWordsIndexes.get(titles); 			
		}
	}
	
	static class Change {		
		/** position -> new string */
		HashMap<Integer,String> substitutes = new HashMap<Integer,String>();
		/** position -> new string */
		HashMap<Integer,String> preserves = new HashMap<Integer,String>();
		/** edit distance */
		int dist;
		/** expected number of hits for changes query */
		int freq;
		enum Type { PHRASE, SPLIT, JOIN, WORD, TITLE_WORD };
		Type type;
		public Change(int dist, int freq, Type type) {
			this.dist = dist;
			this.freq = freq;
			this.type = type;
		}		
	}
	
	/** 
	 * Make a suggestion:
	 * 1) make a phrase suggestion if it will yield more search results
	 * 2) make a phrase or words suggestion (what goes with less edit distance) 
	 *    if there are too little hits
	 *    
	 * @return suggested query, or null if no suggestions 
	 */
	@SuppressWarnings("unchecked")
	public SuggestQuery suggest(String searchterm, WikiQueryParser parser, NamespaceFilter nsf, SearchResults res){
		ArrayList<Token> tokens = parser.tokenizeBareText(searchterm);
		int numHits = res.getNumHits();
		
		if(numHits >= minHitsTitles)
			return null;
		
		// collect words in titles, these shouldn't be spell-checked
		HashSet<String> correctWords = new HashSet<String>();
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid,false);
		try {
			for(ResultSet r : res.getResults()){
				Token t = null;
				TokenStream ts = analyzer.tokenStream("title",r.title);				
				while( (t = ts.next()) != null ){
					correctWords.add(t.termText());
				}				
			}
		} catch (IOException e) {
			log.error("I/O error trying to get list of correct words : "+e.getMessage());
			e.printStackTrace();
		}

		// always spell-check phrases
		int minFreq = (numHits <  minHitsTitles)? 0 : numHits;
		ArrayList<Change> suggestions = new ArrayList<Change>(); 
		
		// add correct words
		for(int i=0;i<tokens.size();i++){
			Token t = tokens.get(i);
			if(correctWords.contains(t.termText())){
				Change c = new Change(0,1,Change.Type.TITLE_WORD);
				c.preserves.put(i,t.termText());
				suggestions.add(c);
			}
		}
		
		for(int i=0;i<tokens.size();i++){
			Token t = tokens.get(i);
			String w = t.termText();
			if(!"word".equals(t.type()) && !"phrase".equals(t.type()))
				continue; // ignore aliases and such
			if(tokens.size() == 1 && w.length() > 1){
				// only one word in query, try to get it from title
				ArrayList<SuggestResult> sug = suggestWordsFromTitle(w,nsf,1);
				if(sug.size() > 0){
					SuggestResult r = sug.get(0);
					Change c = new Change(r.dist,r.frequency,Change.Type.TITLE_WORD);
					if(r.word.equals(w))
						c.preserves.put(i,r.word);
					else					
						c.substitutes.put(i,r.word);
					suggestions.add(c);
				}	
			}
			// suggest split
			SuggestResult split = suggestSplitFromTitle(t.termText(),nsf,minFreq);
			if(split != null){
				Change sc = new Change(split.dist,split.frequency,Change.Type.SPLIT);
				sc.substitutes.put(i,split.word.replace("_"," "));
				suggestions.add(sc);
			}
			// get suggestions for pairs of words
			for(int j=i+1;j<tokens.size();j++){
				if(!correctWords.contains(tokens.get(i)) && !correctWords.contains(tokens.get(j))){
					boolean succ = addPhraseSuggestion(tokens,i,j,suggestions,nsf,minFreq);
					if(succ)
						break;
				}
			}
			
		}
		// indexes of tokens to be preserved in individual word check
		HashSet<Integer> preserveTokens = new HashSet<Integer>();
		if(suggestions.size() > 0){
			// found some suggestions
			Object[] ret = calculateChanges(suggestions,searchterm.length()/2);
			ArrayList<Entry<Integer,String>> proposedChanges = (ArrayList<Entry<Integer, String>>) ret[0];
			ArrayList<Entry<Integer,String>> preservedWords = (ArrayList<Entry<Integer, String>>) ret[1];
			for(Entry<Integer,String> e : preservedWords)
				preserveTokens.add(e.getKey());
			// substitute
			if(proposedChanges.size() > 0){
				for(Entry<Integer,String> e : proposedChanges){
					Token t = tokens.get(e.getKey());
					searchterm = markSuggestion(searchterm,t,e.getValue());
				}
				return new SuggestQuery(tidy(searchterm));
			}
		}
		
		// spell-check individual words
		if(numHits < minHitsWords && tokens.size() != 1){
			LinkedList<Change> changes = new LinkedList<Change>();
			for(int i=0;i<tokens.size();i++){
				Token t = tokens.get(i);
				String w = t.termText();
				if(w.length() < 2)
					continue;
				if(correctWords.contains(w) || preserveTokens.contains(i))
					continue;
				ArrayList<SuggestResult> sug = suggestWordsFromTitle(w,nsf,1);
				if(sug.size() > 0){
					SuggestResult r = sug.get(0);
					if(r.word.equals(w))
						continue; // the word is correct
					Change c = new Change(r.dist,r.frequency,Change.Type.WORD);
					c.substitutes.put(i,r.word);
					changes.addFirst(c);
				}				
			}
			// do changes
			if(changes.size() > 0){
				for(Change c : changes){
					for(Entry<Integer,String> e : c.substitutes.entrySet()){
						Token t = tokens.get(e.getKey());
						searchterm = markSuggestion(searchterm,t,e.getValue());
					}
				}
				return new SuggestQuery(tidy(searchterm),true);
			}
		}

		return null;
	}
	
	protected boolean addPhraseSuggestion(ArrayList<Token> tokens, int i1, int i2, ArrayList<Change> suggestions, NamespaceFilter nsf, int minFreq) {
		Token t1 = tokens.get(i1);
		Token t2 = tokens.get(i2);
		if(t2.type().equals(t1.type())){
			String word1 = t1.termText();
			String word2 = t2.termText();
			if(stopWords.contains(word1) || stopWords.contains(word2))
				return false;
			log.info("spell-check phrase \""+word1+" "+word2+"\"");
			// phrase
			ArrayList<SuggestResult> r = suggestPhraseFromTitle(word1,word2,1,nsf,minFreq);
			if(r.size() > 0){
				SuggestResult res = r.get(0);
				String[] ph = res.word.split("_");						
				if(ph.length == 2){
					// figure out which words need to be changed
					Change sc = new Change(res.dist,res.frequency,Change.Type.PHRASE);
					if(!ph[0].equals(word1))
						sc.substitutes.put(i1,ph[0]);
					else
						sc.preserves.put(i1,ph[0]);
					if(!ph[1].equals(word2))
						sc.substitutes.put(i2,ph[1]);
					else
						sc.preserves.put(i2,ph[1]);
					suggestions.add(sc);
				} else
					log.error("Unexpected phrase in suggest result "+res);
			}
			// join
			SuggestResult join = suggestJoinFromTitle(word1,word2,nsf,minFreq);
			if(join != null){
				Change sc = new Change(join.dist,join.frequency,Change.Type.JOIN);
				sc.substitutes.put(i1,""); 
				sc.substitutes.put(i2,join.word);
				suggestions.add(sc);
			}
			return true;
		}
		return false;		
	}

	protected String markSuggestion(String searchterm, Token t, String newWord){
		return searchterm.substring(0,t.startOffset()) 
		+ "<i>" + newWord	+ "</i>"
		+ searchterm.substring(t.endOffset());
	}
	
	/** tidy the query, convert double spaces into single spaces, and such... */
	protected String tidy(String searchterm){
		return searchterm.replaceAll("<i></i>","").replaceAll(" +"," ").replaceAll(";","");
	}
	
	/** 
	 * Extract the maximal number of non-confilicting suggestions, starting with the one
	 * that changes the query least.
	 * 
	 * @return set of token_number -> new string.
	 */ 
	protected Object[] calculateChanges(ArrayList<Change> changes, int maxDist){
		// sort suggested changes by relevance
		Collections.sort(changes,new Comparator<Change>() {
			public int compare(Change o1, Change o2){					
				if(o1.dist == o2.dist){
					if(o2.type == Change.Type.PHRASE && o2.type != o1.type) // favour phrase suggestions
						return 1;
					else if(o2.type == Change.Type.WORD && o2.type != o1.type) // disfavour word suggestions
						return -1;
					else
						return o2.freq - o1.freq;
				} else 
					return o1.dist - o2.dist;					
			}
		});
		
		HashMap<Integer,String> accept = new HashMap<Integer,String>();
		HashMap<Integer,String> preserve = new HashMap<Integer,String>();
		int dist = 0;
		for(Change c : changes){
			boolean acceptChange = true;
			for(Entry<Integer,String> e : c.substitutes.entrySet()){
				String acceptedTerm = accept.get(e.getKey());
				String preservedTerm = preserve.get(e.getKey());
				if((acceptedTerm != null && !acceptedTerm.equals(e.getValue()))
					|| (preservedTerm != null && !preservedTerm.equals(e.getValue()))){
					acceptChange = false; // conflicting suggestions
					break;
				}
			}
			if(acceptChange && (dist + c.dist < maxDist)){
				for(Entry<Integer,String> e : c.substitutes.entrySet())
					accept.put(e.getKey(),e.getValue());
				for(Entry<Integer,String> e : c.preserves.entrySet())
					preserve.put(e.getKey(),e.getValue());
				dist += c.dist;
			}
		}
		ArrayList<Entry<Integer,String>> proposedChanges = new ArrayList<Entry<Integer,String>>();
		proposedChanges.addAll(accept.entrySet());
		// sort in reverse order from that in query, i.e. first change in the last term
		Collections.sort(proposedChanges,new Comparator<Entry<Integer,String>>() {
			public int compare(Entry<Integer,String> o1, Entry<Integer,String> o2){
				return o2.getKey() - o1.getKey();
			}
		});
		ArrayList<Entry<Integer,String>> preservedWords = new ArrayList<Entry<Integer,String>>();
		preservedWords.addAll(preserve.entrySet());
		return new Object[] {proposedChanges, preservedWords};
	}
	
	/** Suggest some words from the words index */
	public ArrayList<SuggestResult> suggestWords(String word, int num){
		Metric metric = new Metric(word);
		BooleanQuery bq = new BooleanQuery();		
		addQuery(bq,"metaphone1",metric.meta1,2);
		addQuery(bq,"metaphone2",metric.meta2,2);
		bq.add(makeWordQuery(word,""),BooleanClause.Occur.SHOULD);
		
		try {
			TopDocs docs = words.search(bq,null,POOL);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			int minfreq = -1;
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = words.doc(sc.doc);
				String w = d.get("word");
				SuggestResult r = new SuggestResult(w,
						Integer.parseInt(d.get("freq")),
						metric);
				if(word.equals(r.word)){
					minfreq = r.frequency;
				}				
				if(acceptWord(r,metric))				
					res.add(r);
			}
			// filter out 
			if(minfreq != -1){
				for(int i=0;i<res.size();){
					if(res.get(i).frequency < minfreq ){
						res.remove(i);
					} else
						i++;
				}
			}
			// sort
			Collections.sort(res,new SuggestResult.Comparator());
			ArrayList<SuggestResult> ret = new ArrayList<SuggestResult>();
			for(int i=0;i<num && i<res.size();i++)
				ret.add(res.get(i));
			return ret;
		} catch (IOException e) {
			log.error("Cannot get suggestions for "+word+" at "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
	}

	public ArrayList<SuggestResult> suggestWordsFromTitle(String word, NamespaceFilter nsf, int num){
		Metric metric = new Metric(word);
		BooleanQuery bq = new BooleanQuery();		
		bq.add(makeWordQuery(word,"word"),BooleanClause.Occur.SHOULD);
		
		try {
			TopDocs docs = titles.search(bq,new NamespaceFilterWrapper(nsf),POOL);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			int minfreq = -1;
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = titles.doc(sc.doc);
				String w = d.get("word");
				SuggestResult r = new SuggestResult(w,
						new NamespaceFreq(d.get("freq")).getFrequency(nsf),
						metric);
				if(word.equals(r.word)){
					minfreq = r.frequency;
				}				
				if(acceptWord(r,metric))				
					res.add(r);
			}
			// filter out 
			if(minfreq != -1){
				for(int i=0;i<res.size();){
					if(res.get(i).frequency < minfreq ){
						res.remove(i);
					} else
						i++;
				}
			}
			// sort
			Collections.sort(res,new SuggestResult.Comparator());
			ArrayList<SuggestResult> ret = new ArrayList<SuggestResult>();
			for(int i=0;i<num && i<res.size();i++)
				ret.add(res.get(i));
			return ret;
		} catch (IOException e) {
			log.error("Cannot get suggestions for "+word+" at "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
	}

	
	/** Check if word can be accepted as suggestion, i.e. if it's not too different from typed-in word */
	protected boolean acceptWord(SuggestResult r, Metric m){
		// check metaphones: don't add if the pronunciation is something completely unrelated
		if((r.distMetaphone < m.meta1.length() || r.distMetaphone2 < m.meta2.length()) && (r.distMetaphone<=3 || r.distMetaphone2<=3)
				&& (r.dist <= m.word.length()/2 || r.dist <= r.word.length()/2) && Math.abs(m.word.length()-r.word.length()) <= 3)
			return true;
		else
			return false;
	}

	/** Make an ngram query on fields with specific prefix, e.g. phrase_ngram2, etc ..  */
	public Query makeWordQuery(String word, String prefix){
		BooleanQuery bq = new BooleanQuery(true);
		int min = NgramIndexer.getMinNgram(word);
		int max = NgramIndexer.getMaxNgram(word);
		String fieldBase = NgramIndexer.getNgramField(prefix);
		for(int i=min; i <= max; i++ ){
			String[] ngrams = NgramIndexer.nGrams(word,i);
			String field = fieldBase+i;
			for(int j=0 ; j<ngrams.length ; j++){
				String ngram = ngrams[j];
				addQuery(bq,field,ngram,1);
			}
		}
		return bq;
	}

	/** Add a SHOULD clause to boolean query */
	protected void addQuery(BooleanQuery q, String field, String value, float boost) {
		Query tq = new TermQuery(new Term(field, value));
		tq.setBoost(boost);
		q.add(new BooleanClause(tq, BooleanClause.Occur.SHOULD));
	}
	
	/** Try to split word into 2 words which make up a phrase */
	public SuggestResult suggestSplitFromTitle(String word, NamespaceFilter nsf, int minFreq){
		int freq = 0;
		Hits hits;
		ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
		try {
			// find frequency
			hits = titles.search(new TermQuery(new Term("word",word)),new NamespaceFilterWrapper(nsf));
			if(hits.length() == 1)
				freq = new NamespaceFreq(hits.doc(0).get("freq")).getFrequency(nsf);

			// try different splits 
			for(int i=1;i<word.length()-1;i++){
				String phrase = word.substring(0,i) + "_" + word.substring(i);
				hits = titles.search(new TermQuery(new Term("phrase",phrase)),new NamespaceFilterWrapper(nsf));
				if(hits.length() > 0){
					int pfreq = new NamespaceFreq(hits.doc(0).get("freq")).getFrequency(nsf);
					if(pfreq >= freq && pfreq > minFreq)
						res.add(new SuggestResult(phrase,pfreq,2));
				}
			}
			if(res.size() > 0){
				Collections.sort(res,new SuggestResult.Comparator());
				return res.get(0);
			}			
		} catch (IOException e) {
			log.warn("I/O error while suggesting split on "+iid+" : "+e.getMessage());
			e.printStackTrace();			
		}
		return null;
	}
	
	/** Returns suggestion if joining words makes sense */
	public SuggestResult suggestJoinFromTitle(String word1, String word2, NamespaceFilter nsf, int minFreq){
		try {
			Hits hits = titles.search(new TermQuery(new Term("word",word1+word2)),new NamespaceFilterWrapper(nsf));
			if(hits.length() > 0){
				int freq = new NamespaceFreq(hits.doc(0).get("freq")).getFrequency(nsf);
				if(freq >= minFreq)
					return new SuggestResult(word1+word2,freq,1);
			}
		} catch (IOException e) {
			log.warn("I/O error while suggesting join on "+iid+" : "+e.getMessage());
			e.printStackTrace();
		}
		return null;
	}
	
	/** Suggest phrase from a titles index, if the phrase is correct will return it as first result */
	public ArrayList<SuggestResult> suggestPhraseFromTitle(String word1, String word2, int num, NamespaceFilter nsf, int minFreq){
		String phrase = word1+"_"+word2;		
		Query q = makeWordQuery(phrase,"phrase");
		Metric m1 = new Metric(word1);
		Metric m2 = new Metric(word2);
		Metric metric = new Metric(phrase);
		try {
			TopDocs docs = titles.search(q,new NamespaceFilterWrapper(nsf),POOL/2);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			int minfreq = (minFreq == 0)? -1 : minFreq;
			// fetch results
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = titles.doc(sc.doc);
				String p = d.get("phrase");
				int freq = new NamespaceFreq(d.get("freq")).getFrequency(nsf); 
				SuggestResult r = new SuggestResult(p,freq,metric);
				if(phrase.equals(r.word) && minfreq == -1){
					minfreq = r.frequency;
				}
				String[] words = p.split("_");
				SuggestResult r1 = new SuggestResult(words[0],freq,m1);
				SuggestResult r2 = new SuggestResult(words[1],freq,m2);
				if(r.dist < phrase.length() / 2 && acceptWord(r1,m1) && acceptWord(r2,m2)) // don't add if it will change more than half of the phrase
					res.add(r);
			}
			// filter out 
			if(minfreq != -1){
				for(int i=0;i<res.size();){
					if(res.get(i).frequency < minfreq ){
						res.remove(i);
					} else
						i++;
				}
			}
			// sort
			Collections.sort(res,new SuggestResult.Comparator());
			// get first num results
			while(res.size() > num){
				res.remove(res.size()-1);
			}
			return res;
		} catch (IOException e) {
			log.error("Cannot get suggestions for "+phrase+" at "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
	}
}
